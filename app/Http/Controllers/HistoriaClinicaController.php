<?php

namespace App\Http\Controllers;

use App\Models\AntecedenteHeredofamiliar;
use App\Models\Pacientes;
use App\Models\AntecedentePersonalPatologico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\AntecedentePersonalNoPatologico;

class HistoriaClinicaController extends Controller
{
    /**
     * Crea o actualiza el resumen clínico principal.
     */
    public function update(
        Request $request,
        Pacientes $paciente
    ): RedirectResponse {
        $this->autorizarEdicionClinica(
            $request,
            $paciente
        );

        $validated = $request->validate([
            'patologia_base' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'padecimiento_actual' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'tratamientos_actuales' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'prioridad_analisis_medico' => [
                'nullable',
                'string',
                'max:20000',
            ],
        ]);

        $paciente->historiaClinica()->updateOrCreate(
            [
                'paciente_id' => $paciente->id,
            ],
            $validated
        );

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with(
                'success',
                'Historia clínica actualizada correctamente.'
            );
    }

    /**
     * Crea o actualiza los antecedentes heredofamiliares.
     */
    public function updateHeredofamiliares(
        Request $request,
        Pacientes $paciente
    ): RedirectResponse {
        $this->autorizarEdicionClinica(
            $request,
            $paciente
        );

        /*
        |--------------------------------------------------------------------------
        | Reglas de validación
        |--------------------------------------------------------------------------
        */

        $rules = [
            'numero_hermanos' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'antecedentes' => [
                'nullable',
                'array',
            ],
        ];

        foreach (
            array_keys(
                AntecedenteHeredofamiliar::CAMPOS
            ) as $campo
        ) {
            $rules["antecedentes.{$campo}"] = [
                'nullable',
                'string',
                'max:1000',
            ];
        }

        $validated = $request->validateWithBag(
            'heredofamiliares',
            $rules
        );

        /*
        |--------------------------------------------------------------------------
        | Normalización de antecedentes
        |--------------------------------------------------------------------------
        |
        | Solamente se guardarán los campos del catálogo oficial.
        | Los valores vacíos se convertirán en null.
        |
        */

        $antecedentes = [];

        foreach (
            array_keys(
                AntecedenteHeredofamiliar::CAMPOS
            ) as $campo
        ) {
            $valor = trim(
                (string) data_get(
                    $validated,
                    "antecedentes.{$campo}",
                    ''
                )
            );

            $antecedentes[$campo] = $valor !== ''
                ? $valor
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Crear la historia principal si todavía no existe
        |--------------------------------------------------------------------------
        */

        $historiaClinica = $paciente
            ->historiaClinica()
            ->firstOrCreate([
                'paciente_id' => $paciente->id,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Crear o actualizar antecedentes heredofamiliares
        |--------------------------------------------------------------------------
        */

        $historiaClinica
            ->antecedentesHeredofamiliares()
            ->updateOrCreate(
                [
                    'historia_clinica_id' =>
                    $historiaClinica->id,
                ],
                [
                    'numero_hermanos' =>
                    $validated['numero_hermanos']
                        ?? null,

                    'antecedentes' => $antecedentes,
                ]
            );

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with(
                'success',
                'Antecedentes heredofamiliares actualizados correctamente.'
            );
    }

    /**
     * Crea o actualiza los antecedentes personales patológicos.
     */
    public function updatePersonalesPatologicos(
        Request $request,
        Pacientes $paciente
    ): RedirectResponse {
        $this->autorizarEdicionClinica(
            $request,
            $paciente
        );

        /*
    |--------------------------------------------------------------------------
    | Reglas de validación
    |--------------------------------------------------------------------------
    */

        $rules = [
            'antecedentes' => [
                'nullable',
                'array',
            ],
        ];

        foreach (
            array_keys(
                AntecedentePersonalPatologico::CAMPOS
            ) as $campo
        ) {
            $rules["antecedentes.{$campo}"] = [
                'nullable',
                'string',
                'max:1000',
            ];
        }

        $validated = $request->validateWithBag(
            'personalesPatologicos',
            $rules
        );

        /*
    |--------------------------------------------------------------------------
    | Normalizar los campos
    |--------------------------------------------------------------------------
    */

        $antecedentes = [];

        foreach (
            array_keys(
                AntecedentePersonalPatologico::CAMPOS
            ) as $campo
        ) {
            $valor = trim(
                (string) data_get(
                    $validated,
                    "antecedentes.{$campo}",
                    ''
                )
            );

            $antecedentes[$campo] = $valor !== ''
                ? $valor
                : null;
        }

        /*
    |--------------------------------------------------------------------------
    | Obtener o crear la historia clínica
    |--------------------------------------------------------------------------
    */

        $historiaClinica = $paciente
            ->historiaClinica()
            ->firstOrCreate([
                'paciente_id' => $paciente->id,
            ]);

        /*
    |--------------------------------------------------------------------------
    | Crear o actualizar los antecedentes
    |--------------------------------------------------------------------------
    */

        $historiaClinica
            ->antecedentesPersonalesPatologicos()
            ->updateOrCreate(
                [
                    'historia_clinica_id' =>
                    $historiaClinica->id,
                ],
                [
                    'antecedentes' => $antecedentes,
                ]
            );

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with(
                'success',
                'Antecedentes personales patológicos actualizados correctamente.'
            );
    }

    /**
 * Crea o actualiza los antecedentes personales no patológicos.
 */
public function updatePersonalesNoPatologicos(
    Request $request,
    Pacientes $paciente
): RedirectResponse {
    $this->autorizarEdicionClinica(
        $request,
        $paciente
    );

    /*
    |--------------------------------------------------------------------------
    | Validación
    |--------------------------------------------------------------------------
    */

    $rules = [
        'antecedentes' => [
            'nullable',
            'array',
        ],
    ];

    foreach (
        array_keys(
            AntecedentePersonalNoPatologico::CAMPOS
        ) as $campo
    ) {
        $rules["antecedentes.{$campo}"] = [
            'nullable',
            'string',
            'max:1000',
        ];
    }

    $validated = $request->validateWithBag(
        'personalesNoPatologicos',
        $rules
    );

    /*
    |--------------------------------------------------------------------------
    | Normalización
    |--------------------------------------------------------------------------
    */

    $antecedentes = [];

    foreach (
        array_keys(
            AntecedentePersonalNoPatologico::CAMPOS
        ) as $campo
    ) {
        $valor = trim(
            (string) data_get(
                $validated,
                "antecedentes.{$campo}",
                ''
            )
        );

        $antecedentes[$campo] = $valor !== ''
            ? $valor
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Historia clínica
    |--------------------------------------------------------------------------
    */

    $historiaClinica = $paciente
        ->historiaClinica()
        ->firstOrCreate([
            'paciente_id' => $paciente->id,
        ]);

    /*
    |--------------------------------------------------------------------------
    | Crear o actualizar antecedentes
    |--------------------------------------------------------------------------
    */

    $historiaClinica
        ->antecedentesPersonalesNoPatologicos()
        ->updateOrCreate(
            [
                'historia_clinica_id' =>
                    $historiaClinica->id,
            ],
            [
                'antecedentes' => $antecedentes,
            ]
        );

    return redirect()
        ->route('pacientes.show', $paciente)
        ->with(
            'success',
            'Antecedentes personales no patológicos actualizados correctamente.'
        );
}

    /**
     * Verifica que el usuario pueda editar información clínica.
     */
    private function autorizarEdicionClinica(
        Request $request,
        Pacientes $paciente
    ): void {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        $esMedicoAutorizado = false;

        if ($user->isMedico() && $user->medico) {
            $esMedicoAutorizado = $paciente
                ->citas()
                ->where(
                    'medico_id',
                    $user->medico->id
                )
                ->where(
                    'estado',
                    '!=',
                    'cancelada'
                )
                ->exists();
        }

        abort_unless(
            $esMedicoAutorizado,
            403,
            'No tienes autorización para modificar esta información clínica.'
        );
    }
}
