<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\Receta;
use App\Rules\MaxWords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecetasController extends Controller
{
    /**
     * Muestra el historial de recetas de un paciente.
     *
     * El administrador puede consultar cualquier historial.
     * El médico debe tener al menos una cita con el paciente.
     */
    public function historial(
        Request $request,
        Pacientes $paciente
    ): View {
        $this->autorizarHistorial($request, $paciente);

        $paciente->load([
            'recetas' => function ($query) {
                $query
                    ->with([
                        'cita.medico.user',
                    ])
                    ->orderByDesc('fecha_expedicion');
            },
        ]);

        $recetas = $paciente->recetas;

        return view(
            'recetas.historial',
            compact('paciente', 'recetas')
        );
    }

    /**
     * Muestra el detalle de una receta.
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
            $receta->cita === null || $receta->cita->paciente === null,
            404,
            'No se encontró la información clínica de esta receta.'
        );

        $this->autorizarHistorial(
            $request,
            $receta->cita->paciente
        );

        return view('recetas.show', compact('receta'));
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

        abort_if(
            $receta->cita === null,
            404,
            'No se encontró la cita relacionada con esta receta.'
        );

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

        $receta = $cita->receta()->create([
            'contenido' => $datos['contenido'],
            'fecha_expedicion' => now(),
        ]);

        return redirect()
            ->route('recetas.show', $receta)
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
        $receta->loadMissing('cita');

        abort_if(
            $receta->cita === null,
            404,
            'No se encontró la cita relacionada con esta receta.'
        );

        $medico = $this->medicoAutenticado($request);

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
        ], [
            'contenido.required' => 'Debes escribir el contenido de la receta.',
            'contenido.string' => 'El contenido de la receta no es válido.',
            'contenido.max' => 'El contenido de la receta es demasiado extenso.',
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
     * Autoriza el acceso al historial clínico del paciente.
     */
    private function autorizarHistorial(
        Request $request,
        Pacientes $paciente
    ): void {
        $usuario = $request->user();

        abort_if(
            $usuario === null,
            401,
            'Debes iniciar sesión para consultar esta información.'
        );

        if ($usuario->isAdmin()) {
            return;
        }

        $medico = $this->medicoAutenticado($request);

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