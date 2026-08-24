<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExploracionesFisicasController extends Controller
{
    /**
     * Crea o actualiza la exploración física de una cita.
     */
    public function update(
        Request $request,
        Citas $cita
    ): RedirectResponse {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Autorización clínica
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $user->isMedico() && $user->medico,
            403,
            'Tu usuario no tiene un perfil médico asociado.'
        );

        abort_unless(
            (int) $cita->medico_id
                === (int) $user->medico->id,
            403,
            'No tienes autorización para modificar esta exploración.'
        );

        abort_if(
            $cita->estado === 'cancelada',
            422,
            'No se puede registrar una exploración en una cita cancelada.'
        );

        /*
        |--------------------------------------------------------------------------
        | Validación
        |--------------------------------------------------------------------------
        */

        $validated = $request->validateWithBag(
            'exploracionFisica',
            [
                'interrogatorio' => [
                    'nullable',
                    'string',
                    'max:20000',
                ],

                'anotaciones' => [
                    'nullable',
                    'string',
                    'max:20000',
                ],

                'exploracion_fisica' => [
                    'nullable',
                    'string',
                    'max:20000',
                ],

                'recomendaciones' => [
                    'nullable',
                    'string',
                    'max:20000',
                ],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Normalizar campos narrativos
        |--------------------------------------------------------------------------
        */

        foreach ($validated as $campo => $valor) {
            $valor = trim((string) $valor);

            $validated[$campo] = $valor !== ''
                ? $valor
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener o crear la historia clínica
        |--------------------------------------------------------------------------
        */

        $historiaClinica = $cita
            ->paciente
            ->historiaClinica()
            ->firstOrCreate([
                'paciente_id' => $cita->paciente_id,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Crear o actualizar la exploración
        |--------------------------------------------------------------------------
        */

        $cita->exploracionFisica()->updateOrCreate(
            [
                'cita_id' => $cita->id,
            ],
            array_merge(
                $validated,
                [
                    'historia_clinica_id' =>
                        $historiaClinica->id,

                    'medico_id' =>
                        $cita->medico_id,
                ]
            )
        );

        return redirect()
            ->route('pacientes.show', $cita->paciente)
            ->with(
                'success',
                'Exploración física actualizada correctamente.'
            );
    }
}