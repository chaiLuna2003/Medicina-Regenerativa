<?php

namespace App\Http\Requests;

use App\Models\EvolucionAparato;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEvolucionAparatosRequest extends FormRequest
{

    /**
     * Bolsa independiente para la valoración
     * de aparatos de una evolución.
     */
    protected $errorBag = 'aparatosEvolucion';
    /**
     * La Policy realizará la autorización
     * dentro del controlador.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza las observaciones de cada aparato.
     */
    protected function prepareForValidation(): void
    {
        $aparatos = $this->input(
            'aparatos',
            []
        );

        if (! is_array($aparatos)) {
            return;
        }

        foreach (
            array_keys(EvolucionAparato::APARATOS)
            as $clave
        ) {
            if (
                ! isset($aparatos[$clave])
                || ! is_array($aparatos[$clave])
            ) {
                continue;
            }

            $observaciones =
                $aparatos[$clave]['observaciones']
                ?? null;

            if (is_string($observaciones)) {
                $observaciones = trim(
                    $observaciones
                );
            }

            $aparatos[$clave]['observaciones'] =
                $observaciones !== ''
                ? $observaciones
                : null;
        }

        $this->merge([
            'aparatos' => $aparatos,
        ]);
    }

    /**
     * Valida el catálogo completo de aparatos.
     */
    public function rules(): array
    {
        $claves = array_keys(
            EvolucionAparato::APARATOS
        );

        $estados = array_keys(
            EvolucionAparato::ESTADOS
        );

        /*
         * array:clave1,clave2 impide enviar aparatos
         * que no pertenezcan al catálogo oficial.
         */
        $rules = [
            'aparatos' => [
                'required',
                'array:' . implode(',', $claves),
            ],
        ];

        foreach ($claves as $clave) {
            $rules["aparatos.{$clave}"] = [
                'required',
                'array',
            ];

            $rules["aparatos.{$clave}.estado"] = [
                'required',
                'string',
                Rule::in($estados),
            ];

            $rules["aparatos.{$clave}.observaciones"] = [
                Rule::requiredIf(
                    fn(): bool => in_array(
                        $this->input(
                            "aparatos.{$clave}.estado"
                        ),
                        [
                            EvolucionAparato::ESTADO_REQUIERE_ATENCION,

                            EvolucionAparato::ESTADO_CRITICO,
                        ],
                        true
                    )
                ),
                'nullable',
                'string',
                'max:5000',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'aparatos.required' =>
            'Debes enviar la valoración de los aparatos.',

            'aparatos.array' =>
            'La valoración de aparatos no es válida.',

            'aparatos.*.required' =>
            'Debes incluir todos los aparatos.',

            'aparatos.*.estado.required' =>
            'Debes seleccionar el estado del aparato.',

            'aparatos.*.estado.in' =>
            'El estado seleccionado no es válido.',

            'aparatos.*.observaciones.required' =>
            'Describe el hallazgo cuando el aparato requiere atención o está crítico.',

            'aparatos.*.observaciones.max' =>
            'La observación no puede superar los 5000 caracteres.',
        ];
    }

    /**
     * Nombres legibles para los errores de validación.
     */
    public function attributes(): array
    {
        $atributos = [];

        foreach (
            EvolucionAparato::APARATOS
            as $clave => $configuracion
        ) {
            $nombre = $configuracion['nombre'];

            $atributos["aparatos.{$clave}.estado"] = "estado de {$nombre}";

            $atributos["aparatos.{$clave}.observaciones"] = "observaciones de {$nombre}";
        }

        return $atributos;
    }
}
