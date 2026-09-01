<?php

namespace App\Http\Controllers;

use App\Models\AntecedenteHeredofamiliar;
use App\Models\Pacientes;
use App\Models\AntecedentePersonalPatologico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\AntecedentePersonalNoPatologico;
use App\Models\HabitoAlimenticio;
use Illuminate\Validation\Rule;

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
     * Crea o actualiza los hábitos alimenticios.
     */
    public function updateHabitosAlimenticios(
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
            'comidas' => [
                'nullable',
                'array',
            ],

            'alimentos' => [
                'nullable',
                'array',
            ],
        ];

        foreach (
            array_keys(HabitoAlimenticio::COMIDAS)
            as $campo
        ) {
            $rules["comidas.{$campo}"] = [
                'nullable',
                'boolean',
            ];
        }

        foreach (
            array_keys(HabitoAlimenticio::ALIMENTOS)
            as $campo
        ) {
            $rules["alimentos.{$campo}"] = [
                'nullable',
                'string',
                'max:500',
            ];
        }

        $validated = $request->validateWithBag(
            'habitosAlimenticios',
            $rules
        );

        /*
    |--------------------------------------------------------------------------
    | Normalizar comidas
    |--------------------------------------------------------------------------
    */

        $comidas = [];

        foreach (
            array_keys(HabitoAlimenticio::COMIDAS)
            as $campo
        ) {
            $comidas[$campo] = $request->boolean(
                "comidas.{$campo}"
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Normalizar alimentos
    |--------------------------------------------------------------------------
    */

        $alimentos = [];

        foreach (
            array_keys(HabitoAlimenticio::ALIMENTOS)
            as $campo
        ) {
            $valor = trim(
                (string) data_get(
                    $validated,
                    "alimentos.{$campo}",
                    ''
                )
            );

            $alimentos[$campo] = $valor !== ''
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
    | Crear o actualizar hábitos alimenticios
    |--------------------------------------------------------------------------
    */

        $historiaClinica
            ->habitoAlimenticio()
            ->updateOrCreate(
                [
                    'historia_clinica_id' =>
                    $historiaClinica->id,
                ],
                [
                    'comidas' => $comidas,
                    'alimentos' => $alimentos,
                ]
            );

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with(
                'success',
                'Hábitos alimenticios actualizados correctamente.'
            );
    }

    /**
     * Crea o actualiza los antecedentes ginecoobstétricos.
     */
    public function updateGinecoobstetricos(
        Request $request,
        Pacientes $paciente
    ): RedirectResponse {
        /*
    |--------------------------------------------------------------------------
    | Autorización
    |--------------------------------------------------------------------------
    */

        $this->autorizarEdicionClinica(
            $request,
            $paciente
        );

        /*
    |--------------------------------------------------------------------------
    | Restricción por sexo
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $paciente->sexo === 'femenino',
            422,
            'Los antecedentes ginecoobstétricos solo aplican a pacientes femeninas.'
        );

        /*
    |--------------------------------------------------------------------------
    | Validación
    |--------------------------------------------------------------------------
    */

        $validated = $request->validateWithBag(
            'ginecoobstetricos',
            [
                'edad_menarca' => [
                    'nullable',
                    'integer',
                    'min:5',
                    'max:25',
                ],

                'ritmo_menstrual' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'duracion_menstruacion_dias' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'max:30',
                ],

                'fecha_ultima_menstruacion' => [
                    'nullable',
                    'date',
                    'before_or_equal:today',
                ],

                'edad_inicio_vida_sexual' => [
                    'nullable',
                    'integer',
                    'min:5',
                    'max:100',
                ],

                'numero_parejas_sexuales' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:1000',
                ],

                'gestas' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:100',
                ],

                'partos' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:100',
                ],

                'cesareas' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:100',
                ],

                'abortos' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:100',
                ],

                'embarazos_ectopicos' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:100',
                ],

                'hijos_vivos' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:100',
                ],

                'embarazo_actual' => [
                    'nullable',
                    Rule::in(['0', '1']),
                ],

                'metodo_anticonceptivo' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'menopausia' => [
                    'nullable',
                    Rule::in(['0', '1']),
                ],

                'edad_menopausia' => [
                    'nullable',
                    'integer',
                    'min:20',
                    'max:100',
                ],

                'fecha_ultimo_papanicolaou' => [
                    'nullable',
                    'date',
                    'before_or_equal:today',
                ],

                'resultado_papanicolaou' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],

                'fecha_ultima_mastografia' => [
                    'nullable',
                    'date',
                    'before_or_equal:today',
                ],

                'resultado_mastografia' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],

                'infecciones_transmision_sexual' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],

                'observaciones' => [
                    'nullable',
                    'string',
                    'max:10000',
                ],
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | Normalizar campos booleanos
    |--------------------------------------------------------------------------
    */

        foreach (
            ['embarazo_actual', 'menopausia']
            as $campo
        ) {
            $validated[$campo] = array_key_exists(
                $campo,
                $validated
            )
                ? (bool) $validated[$campo]
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
    | Crear o actualizar antecedentes
    |--------------------------------------------------------------------------
    */

        $historiaClinica
            ->antecedenteGinecoobstetrico()
            ->updateOrCreate(
                [
                    'historia_clinica_id' =>
                    $historiaClinica->id,
                ],
                $validated
            );

        return redirect()
            ->route(
                'pacientes.show',
                $paciente
            )
            ->with(
                'success',
                'Antecedentes ginecoobstétricos actualizados correctamente.'
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
