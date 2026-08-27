<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\Estudio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Pacientes;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EstudiosController extends Controller
{
    /**
     * Guarda uno o varios estudios asociados a una cita.
     */
    public function store(
        Request $request,
        Citas $cita
    ): RedirectResponse {

        /*
         * Segunda capa de protección.
         * Más adelante la ruta también tendrá middleware.
         */
        abort_unless(
            in_array(
                auth()->user()->role,
                ['admin', 'recepcionista'],
                true
            ),
            403
        );

        /*
         * Validación de los datos recibidos.
         */
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'fecha_estudio' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'archivos' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],

            'archivos.*' => [
                'required',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf',
                'max:15360',
            ],
        ]);

        /*
         * Procesamos cada PDF seleccionado.
         */
        foreach ($request->file('archivos') as $archivo) {

            /*
             * No utilizamos el nombre original para almacenar
             * físicamente el documento.
             *
             * Esto evita colisiones y nombres predecibles.
             */
            $nombreInterno = Str::uuid()->toString() . '.pdf';

            /*
             * El disco "local" de Laravel almacena estos
             * documentos fuera del directorio público.
             */
            $ruta = $archivo->storeAs(
                'estudios/' . now()->format('Y/m'),
                $nombreInterno,
                'local'
            );

            /*
             * Registramos la información del estudio.
             */
            Estudio::create([
                'cita_id' => $cita->id,

                'uploaded_by' => auth()->id(),

                'nombre' => $datos['nombre'],

                'descripcion' => $datos['descripcion'] ?? null,

                'fecha_estudio' => $datos['fecha_estudio'],

                'archivo_path' => $ruta,

                'archivo_original' =>
                $archivo->getClientOriginalName(),

                'mime_type' =>
                $archivo->getMimeType(),

                'archivo_size' =>
                $archivo->getSize(),
            ]);
        }

        return back()->with(
            'success',
            'Los estudios se cargaron correctamente.'
        );
    }

    /**
     * Mostrar el historial de estudios de un paciente.
     */
    public function historial(
        Request $request,
        Pacientes $paciente
    ): View {
        $usuario = $request->user();

        /*
     * El médico solamente puede consultar estudios
     * de pacientes con los que tenga relación clínica.
     */
        if ($usuario->role === 'medico') {

            $medico = $usuario->medico;

            abort_unless($medico, 403);

            $tieneRelacionClinica = $paciente
                ->citas()
                ->where('medico_id', $medico->id)
                ->exists();

            abort_unless($tieneRelacionClinica, 403);
        }

        /*
     * Obtenemos todos los estudios del paciente
     * mediante sus citas.
     */
        $estudios = $paciente
            ->estudios()
            ->with([
                'cita.medico',
                'subidoPor',
            ])
            ->orderByDesc('fecha_estudio')
            ->orderByDesc('estudios.created_at')
            ->paginate(10);

        return view(
            'estudios.historial',
            compact(
                'paciente',
                'estudios'
            )
        );
    }

    /**
     * Visualizar un estudio PDF.
     */
    public function archivo(
        Request $request,
        Estudio $estudio
    ) {
        $this->validarAccesoAlEstudio(
            $request,
            $estudio
        );

        abort_unless(
            Storage::disk('local')->exists(
                $estudio->archivo_path
            ),
            404,
            'El archivo del estudio no fue encontrado.'
        );

        return Storage::disk('local')->response(
            $estudio->archivo_path,
            $estudio->archivo_original,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' =>
                'inline; filename="' .
                    basename($estudio->archivo_original) .
                    '"',
            ]
        );
    }


    /**
     * Descargar un estudio PDF.
     */
    public function descargar(
        Request $request,
        Estudio $estudio
    ): StreamedResponse {
        $this->validarAccesoAlEstudio(
            $request,
            $estudio
        );

        abort_unless(
            Storage::disk('local')->exists(
                $estudio->archivo_path
            ),
            404,
            'El archivo del estudio no fue encontrado.'
        );

        return Storage::disk('local')->download(
            $estudio->archivo_path,
            basename($estudio->archivo_original)
        );
    }

    /**
     * Validar que el usuario pueda consultar
     * el estudio solicitado.
     */
    private function validarAccesoAlEstudio(
        Request $request,
        Estudio $estudio
    ): void {
        $usuario = $request->user();

        /*
     * Administrador y recepción tienen acceso.
     */
        if (in_array(
            $usuario->role,
            ['admin', 'recepcionista'],
            true
        )) {
            return;
        }

        /*
     * Cualquier otro rol distinto de médico
     * queda rechazado.
     */
        abort_unless(
            $usuario->role === 'medico',
            403
        );

        $medico = $usuario->medico;

        abort_unless($medico, 403);

        /*
     * Obtenemos al paciente propietario del estudio
     * mediante la cita.
     */
        $estudio->loadMissing('cita');

        abort_unless(
            $estudio->cita,
            404
        );

        $pacienteId = $estudio->cita->paciente_id;

        /*
     * El médico debe tener al menos una cita
     * con este paciente.
     */
        $tieneRelacionClinica = Citas::query()
            ->where('paciente_id', $pacienteId)
            ->where('medico_id', $medico->id)
            ->exists();

        abort_unless(
            $tieneRelacionClinica,
            403
        );
    }

    private function validarAccesoPaciente(
        Request $request,
        Pacientes $paciente
    ): void {
        $usuario = $request->user();

        // Admin y recepción pueden consultar.
        if (in_array(
            $usuario->role,
            ['admin', 'recepcionista'],
            true
        )) {
            return;
        }

        // Cualquier otro rol debe ser médico.
        abort_unless(
            $usuario->role === 'medico',
            403
        );

        $medico = $usuario->medico;

        abort_unless($medico, 403);

        // El médico debe tener relación clínica con el paciente.
        $tieneRelacionClinica = $paciente
            ->citas()
            ->where('medico_id', $medico->id)
            ->exists();

        abort_unless(
            $tieneRelacionClinica,
            403
        );
    }
}
