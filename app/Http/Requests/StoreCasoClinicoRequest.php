<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCasoClinicoRequest extends FormRequest
{

/**
 * Bolsa independiente para no mezclar estos errores
 * con Estudios u otros formularios de la cita.
 */
protected $errorBag = 'casoClinico';

    /**
     * La autorización clínica se comprobará mediante
     * CasoClinicoPolicy dentro del controlador.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza los textos antes de validarlos.
     */
    protected function prepareForValidation(): void
    {
        $campos = [
            'nombre',
            'descripcion_inicial',
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
     * Reglas para abrir un caso y registrar
     * su primera evolución.
     */
    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'descripcion_inicial' => [
                'nullable',
                'string',
                'max:20000',
            ],

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
            'nombre.required' =>
                'El nombre o motivo del caso clínico es obligatorio.',

            'nombre.max' =>
                'El nombre no puede superar los 150 caracteres.',

            'evolucion_clinica.required' =>
                'Debes escribir la evolución clínica de la consulta.',

            'evolucion_clinica.max' =>
                'La evolución clínica es demasiado extensa.',

            '*.max' =>
                'Uno de los campos supera la longitud permitida.',
        ];
    }
}