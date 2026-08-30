<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvolucionClinicaRequest extends FormRequest
{

    /**
     * Bolsa independiente para el seguimiento
     * de un caso clínico existente.
     */
    protected $errorBag = 'seguimientoClinico';
    /**
     * La autorización se comprobará mediante las Policies
     * dentro del controlador.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Elimina espacios innecesarios y transforma
     * textos vacíos en valores nulos.
     */
    protected function prepareForValidation(): void
    {
        $campos = [
            'evolucion_clinica',
            'diagnostico',
            'tratamiento',
            'plan_recomendaciones',
            'indicaciones_enfermeria',
            'observaciones',
        ];

        $datos = [];

        foreach ($campos as $campo) {
            $valor = $this->input($campo);

            if (! is_string($valor)) {
                continue;
            }

            $valor = trim($valor);

            $datos[$campo] =
                $valor !== ''
                ? $valor
                : null;
        }

        $this->merge($datos);
    }

    /**
     * Reglas para agregar una evolución
     * a un caso clínico existente.
     */
    public function rules(): array
    {
        return [
            'evolucion_clinica' => [
                'required',
                'string',
                'max:50000',
            ],

            'diagnostico' => [
                'nullable',
                'string',
                'max:50000',
            ],

            'tratamiento' => [
                'nullable',
                'string',
                'max:50000',
            ],

            'plan_recomendaciones' => [
                'nullable',
                'string',
                'max:50000',
            ],

            'indicaciones_enfermeria' => [
                'nullable',
                'string',
                'max:50000',
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:50000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'evolucion_clinica.required' =>
            'Debes escribir la evolución clínica de la consulta.',

            'evolucion_clinica.max' =>
            'La evolución clínica es demasiado extensa.',

            '*.max' =>
            'Uno de los campos supera la longitud permitida.',
        ];
    }
}
