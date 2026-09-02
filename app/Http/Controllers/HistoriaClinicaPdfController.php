<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\HistoriaClinicaDocumento;
use App\Models\Pacientes;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class HistoriaClinicaPdfController extends Controller
{
    /**
     * Genera y almacena una nueva versión del expediente.
     */
    public function store(
        Request $request,
        Pacientes $paciente
    ): RedirectResponse {
        $this->autorizarPaciente(
            $request,
            $paciente
        );

        $this->cargarExpediente(
            $paciente
        );

        $nombrePaciente = Str::slug(
            trim(
                ($paciente->nombre ?? '')
                    . ' '
                    . ($paciente->apellido ?? '')
            )
        );

        if ($nombrePaciente === '') {
            $nombrePaciente = 'paciente';
        }

        $marcaTiempo = now()->format(
            'Ymd-His'
        );

        $nombreArchivo =
            'historia-clinica-'
            . $paciente->id
            . '-'
            . $nombrePaciente
            . '-'
            . $marcaTiempo
            . '.pdf';

        $ruta =
            'historias-clinicas/'
            . $paciente->id
            . '/'
            . now()->format('Y/m')
            . '/'
            . Str::uuid()
            . '.pdf';

        $fotoPaciente = $this->obtenerFotoPaciente(
            $paciente
        );

        $contenidoPdf = Pdf::loadView(
            'pacientes.pdf.historia-clinica',
            compact(
                'paciente',
                'fotoPaciente'
            )
        )
            ->setPaper(
                'letter',
                'portrait'
            )
            ->output();

        $guardado = Storage::disk('local')
            ->put(
                $ruta,
                $contenidoPdf
            );

        if (!$guardado) {
            throw new RuntimeException(
                'No se pudo almacenar el expediente clínico.'
            );
        }

        try {
            $documento =
                HistoriaClinicaDocumento::query()
                ->create([
                    'paciente_id' =>
                    $paciente->id,

                    'generado_por' =>
                    $request->user()->id,

                    'archivo_path' =>
                    $ruta,

                    'archivo_nombre' =>
                    $nombreArchivo,

                    'mime_type' =>
                    'application/pdf',

                    'archivo_size' =>
                    strlen($contenidoPdf),

                    'generado_en' =>
                    now(),
                ]);
        } catch (Throwable $exception) {
            Storage::disk('local')
                ->delete($ruta);

            throw $exception;
        }

        return redirect()
            ->route(
                'historias-clinicas.documentos.archivo',
                $documento
            );
    }

    /**
     * Muestra una versión PDF en el navegador.
     */
    public function archivo(
        Request $request,
        HistoriaClinicaDocumento $documento
    ): StreamedResponse {
        $this->autorizarDocumento(
            $request,
            $documento
        );

        $this->asegurarArchivoExistente(
            $documento
        );

        return Storage::disk('local')
            ->response(
                $documento->archivo_path,
                $documento->archivo_nombre,
                [
                    'Content-Type' =>
                    'application/pdf',

                    'Content-Disposition' =>
                    'inline; filename="'
                        . basename(
                            $documento->archivo_nombre
                        )
                        . '"',
                ]
            );
    }

    /**
     * Descarga una versión PDF almacenada.
     */
    public function descargar(
        Request $request,
        HistoriaClinicaDocumento $documento
    ): StreamedResponse {
        $this->autorizarDocumento(
            $request,
            $documento
        );

        $this->asegurarArchivoExistente(
            $documento
        );

        return Storage::disk('local')
            ->download(
                $documento->archivo_path,
                basename(
                    $documento->archivo_nombre
                ),
                [
                    'Content-Type' =>
                    'application/pdf',
                ]
            );
    }

    /**
     * Carga únicamente la información del expediente general.
     */
    private function cargarExpediente(
        Pacientes $paciente
    ): void {
        $paciente->load([
            'historiaClinica.antecedentesHeredofamiliares',

            'historiaClinica.antecedentesPersonalesPatologicos',

            'historiaClinica.antecedentesPersonalesNoPatologicos',

            'historiaClinica.habitoAlimenticio',

            'historiaClinica.antecedenteGinecoobstetrico',

            'historiaClinica.exploracionesFisicas' =>
            function ($query) {
                $query
                    ->with([
                        'cita.signoVital',
                        'medico.user',
                    ])
                    ->orderByDesc('created_at')
                    ->orderByDesc('id');
            },
        ]);
    }

    /**
     * Convierte la fotografía del paciente en una imagen embebida.
     */
    private function obtenerFotoPaciente(
        Pacientes $paciente
    ): ?string {
        if (!$paciente->foto) {
            return null;
        }

        $disco = Storage::disk('public');

        if (!$disco->exists($paciente->foto)) {
            return null;
        }

        $mimeType = $disco->mimeType(
            $paciente->foto
        );

        if (!in_array(
            $mimeType,
            [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
            ],
            true
        )) {
            return null;
        }

        $contenido = $disco->get(
            $paciente->foto
        );

        return 'data:'
            . $mimeType
            . ';base64,'
            . base64_encode($contenido);
    }

    /**
     * Comprueba que el archivo privado exista.
     */
    private function asegurarArchivoExistente(
        HistoriaClinicaDocumento $documento
    ): void {
        abort_unless(
            Storage::disk('local')
                ->exists(
                    $documento->archivo_path
                ),
            404,
            'El expediente clínico no fue encontrado.'
        );
    }

    /**
     * Autoriza el acceso mediante el documento.
     */
    private function autorizarDocumento(
        Request $request,
        HistoriaClinicaDocumento $documento
    ): void {
        $documento->loadMissing(
            'paciente'
        );

        abort_unless(
            $documento->paciente,
            404
        );

        $this->autorizarPaciente(
            $request,
            $documento->paciente
        );
    }

    /**
     * Autoriza el acceso al expediente del paciente.
     */
    private function autorizarPaciente(
        Request $request,
        Pacientes $paciente
    ): void {
        $usuario = $request->user();

        if ($usuario->isAdmin()) {
            return;
        }

        abort_unless(
            $usuario->isMedico()
                && $usuario->medico,
            403,
            'Tu usuario no tiene un perfil médico asociado.'
        );

        $tieneRelacionClinica = Citas::query()
            ->where(
                'paciente_id',
                $paciente->id
            )
            ->where(
                'medico_id',
                $usuario->medico->id
            )
            ->where(
                'estado',
                '!=',
                'cancelada'
            )
            ->exists();

        abort_unless(
            $tieneRelacionClinica,
            403,
            'No tienes autorización para consultar este expediente.'
        );
    }
}
