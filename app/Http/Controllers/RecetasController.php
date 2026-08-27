<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\Receta;
use App\Rules\MaxWords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RecetasController extends Controller
{
    /**
     * Muestra el historial visual de recetas de un paciente.
     *
     * El administrador puede consultar cualquier historial.
     * El médico debe tener relación clínica con el paciente.
     */
    public function historial(
        Request $request,
        Pacientes $paciente
    ): View {
        $this->autorizarHistorial(
            $request,
            $paciente
        );

        $recetas = $paciente->recetas()
            ->with([
                'cita.medico.user',
                'cita.paciente',
            ])
            ->orderByDesc('fecha_expedicion')
            ->orderByDesc('recetas.id')
            ->get();

        return view(
            'recetas.historial',
            compact(
                'paciente',
                'recetas'
            )
        );
    }

    /**
     * Muestra una receta específica.
     */
    /**
     * Muestra el detalle visual de una receta.
     */
    public function show(
        Request $request,
        Receta $receta
    ): View {
        $receta->load([
            'cita.paciente',
            'cita.medico.user',
            'cita.signoVital',
        ]);

        abort_if(
            $receta->cita === null
                || $receta->cita->paciente === null,
            404,
            'La receta no tiene una cita o paciente asociado.'
        );

        $this->autorizarHistorial(
            $request,
            $receta->cita->paciente
        );

        return view(
            'recetas.show',
            compact('receta')
        );
    }

    /**
     * Descarga una receta médica en formato PDF.
     */
    public function pdf(

        Request $request,
        Receta $receta
    ): Response {
        $receta->load([
            'cita.paciente',
            'cita.medico.user',
            'cita.medico.universidad',
        ]);

        /*
     * Una receta debe permanecer asociada
     * con una cita y un paciente.
     */
        abort_if(
            $receta->cita === null
                || $receta->cita->paciente === null,
            404,
            'La receta no tiene una cita o paciente asociado.'
        );

        /*
     * Aplicamos las mismas reglas de acceso
     * utilizadas para consultar la receta.
     */
        $this->autorizarHistorial(
            $request,
            $receta->cita->paciente
        );

        $paciente = $receta->cita->paciente;

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

        $nombreArchivo =
            'receta-medica-'
            . $receta->id
            . '-'
            . $nombrePaciente
            . '.pdf';

        $pdf = Pdf::loadView(
            'recetas.pdf',
            compact('receta')
        )->setPaper(
            'letter',
            'portrait'
        );

        return $pdf->download(
            $nombreArchivo
        );
    }

    /**
     * Muestra el formulario para elaborar una receta.
     */
    public function create(
        Request $request,
        Citas $cita
    ): View {
        $medico = $this->medicoAutenticado($request);

        $this->autorizarCitaPropia($cita, $medico);

        abort_if(
            $cita->receta()->exists(),
            409,
            'Esta cita ya tiene una receta médica.'
        );

        $cita->load([
            'paciente',
            'medico.user',
            'signoVital',
        ]);

        return view('recetas.create', compact('cita'));
    }

    /**
     * Muestra el formulario para editar una receta.
     */
    public function edit(
        Request $request,
        Receta $receta
    ): View {
        $receta->load([
            'cita.paciente',
            'cita.medico.user',
            'cita.signoVital',
        ]);

        $medico = $this->medicoAutenticado($request);

        $this->autorizarCitaPropia(
            $receta->cita,
            $medico
        );

        return view('recetas.edit', compact('receta'));
    }

    /**
     * Crea una receta para una cita.
     *
     * Solo el médico asignado a la cita puede crearla.
     */
    public function store(
        Request $request,
        Citas $cita
    ): RedirectResponse {
        $medico = $this->medicoAutenticado($request);

        $this->autorizarCitaPropia($cita, $medico);

        abort_if(
            $cita->receta()->exists(),
            409,
            'Esta cita ya tiene una receta médica.'
        );

        $datos = $this->validarContenido($request);

        $cita->receta()->create([
            'contenido' => $datos['contenido'],
            'fecha_expedicion' => now(),
        ]);

        return redirect()
            ->route('citas.show', $cita)
            ->with(
                'success',
                'La receta médica se creó correctamente.'
            );
    }

    /**
     * Actualiza una receta existente.
     *
     * Solo el médico responsable de la cita puede modificarla.
     */
    public function update(
        Request $request,
        Receta $receta
    ): RedirectResponse {
        $medico = $this->medicoAutenticado($request);

        $receta->loadMissing('cita');

        $this->autorizarCitaPropia(
            $receta->cita,
            $medico
        );

        $datos = $this->validarContenido($request);

        $receta->update([
            'contenido' => $datos['contenido'],
        ]);

        return redirect()
            ->route('recetas.show', $receta)
            ->with(
                'success',
                'La receta médica se actualizó correctamente.'
            );
    }

    /**
     * Valida el contenido enviado por el médico.
     */
    private function validarContenido(Request $request): array
    {
        return $request->validate([
            'contenido' => [
                'required',
                'string',
                'max:50000',
                new MaxWords(2000),
            ],
        ]);
    }

    /**
     * Obtiene el perfil médico del usuario autenticado.
     */
    private function medicoAutenticado(Request $request): Medicos
    {
        $medico = $request->user()?->medico;

        abort_if(
            $medico === null,
            403,
            'Tu usuario no tiene un perfil médico vinculado.'
        );

        return $medico;
    }

    /**
     * Verifica que la cita pertenezca al médico autenticado.
     */
    private function autorizarCitaPropia(
        Citas $cita,
        Medicos $medico
    ): void {
        abort_unless(
            (int) $cita->medico_id === (int) $medico->id,
            403,
            'No puedes elaborar o modificar la receta de esta cita.'
        );
    }

    /**
     * Autoriza el acceso al historial del paciente.
     */
    private function autorizarHistorial(
        Request $request,
        Pacientes $paciente
    ): void {
        /*
         * El administrador puede consultar cualquier historial.
         */
        if ($request->user()->isAdmin()) {
            return;
        }

        /*
         * Los demás usuarios deben tener un perfil médico.
         */
        $medico = $this->medicoAutenticado($request);

        /*
         * El médico debe tener al menos una cita asignada
         * con este paciente.
         */
        $tieneCitaConPaciente = Citas::query()
            ->where('paciente_id', $paciente->id)
            ->where('medico_id', $medico->id)
            ->exists();

        abort_unless(
            $tieneCitaConPaciente,
            403,
            'No puedes consultar el historial de este paciente.'
        );
    }
}
