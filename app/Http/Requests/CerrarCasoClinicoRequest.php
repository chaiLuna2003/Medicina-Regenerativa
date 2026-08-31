<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CerrarCasoClinicoRequest extends FormRequest
{
    /**
     * Bolsa independiente para mostrar los errores
     * dentro del modal de cierre.
     */
    protected $errorBag = 'cierreCasoClinico';

    /**
     * La autorización se comprueba mediante la Policy
     * dentro del controlador.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Evita aceptar espacios como motivo de cierre.
     */
    protected function prepareForValidation(): void
    {
        $motivo = $this->input(
            'motivo_cierre'
        );

        if (! is_string($motivo)) {
            return;
        }

        $motivo = trim($motivo);

        $this->merge([
            'motivo_cierre' =>
            $motivo !== ''
                ? $motivo
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'motivo_cierre' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'confirmacion_cierre' => [
                'required',
                'accepted',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo_cierre.required' =>
            'Debes indicar el motivo de cierre del caso clínico.',

            'motivo_cierre.min' =>
            'El motivo de cierre debe tener al menos 10 caracteres.',

            'motivo_cierre.max' =>
            'El motivo de cierre no puede superar los 5000 caracteres.',

                        'confirmacion_cierre.required' =>
                'Debes confirmar que deseas cerrar el caso clínico.',

            'confirmacion_cierre.accepted' =>
                'Debes confirmar que deseas cerrar el caso clínico.',
        ];
    }
}
