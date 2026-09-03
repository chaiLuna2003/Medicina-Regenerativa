<?php

namespace App\Http\Requests;

use App\Models\Pacientes;
use App\Support\Pacientes\PermisosEdicionPaciente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UpdatePacienteRequest extends FormRequest
{
    /**
     * Comprueba el acceso al paciente y valida que el usuario
     * solamente envíe campos permitidos para su rol.
     */
    public function authorize(): bool
    {
        $usuario = $this->user();
        $paciente = $this->route('pacientes');

        if (
            ! $usuario
            || ! $paciente instanceof Pacientes
            || ! $usuario->can(
                'update',
                $paciente
            )
        ) {
            return false;
        }

        $permisos = app(
            PermisosEdicionPaciente::class
        );

        $camposEnviados = array_keys(
            $this->except([
                '_token',
                '_method',
                'seccion',
            ])
        );

        foreach ($camposEnviados as $campo) {
            if (
                ! $permisos->puedeEditar(
                    $usuario,
                    $campo
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Devuelve únicamente las reglas correspondientes a la
     * sección solicitada y a los permisos del usuario.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $reglas = [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'apellido' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'fecha_nacimiento' => [
                'sometimes',
                'required',
                'date',
                'before_or_equal:today',
            ],

            'sexo' => [
                'sometimes',
                'required',
                Rule::in(
                    array_keys(Pacientes::SEXOS)
                ),
            ],

            'categoria' => [
                'sometimes',
                'required',
                Rule::in(
                    array_keys(Pacientes::CATEGORIAS)
                ),
            ],

            'status' => [
                'sometimes',
                'required',
                'boolean',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'telefono_fijo' => [
                'nullable',
                'string',
                'max:20',
            ],

            'telefono_secundario' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'domicilio' => [
                'nullable',
                'string',
                'max:500',
            ],

            'ciudad' => [
                'nullable',
                'string',
                'max:150',
            ],

            'estado' => [
                'nullable',
                'string',
                'max:150',
            ],

            'codigo_postal' => [
                'nullable',
                'string',
                'max:10',
            ],

            'lugar_nacimiento' => [
                'nullable',
                'string',
                'max:200',
            ],

            'ocupacion' => [
                'nullable',
                'string',
                'max:200',
            ],

            'religion' => [
                'nullable',
                'string',
                'max:150',
            ],

            'estado_civil' => [
                'nullable',
                Rule::in(
                    array_keys(
                        Pacientes::ESTADOS_CIVILES
                    )
                ),
            ],

            'escolaridad' => [
                'nullable',
                Rule::in(
                    array_keys(
                        Pacientes::ESCOLARIDADES
                    )
                ),
            ],

            'tipo_sangre' => [
                'nullable',
                Rule::in(
                    array_keys(
                        Pacientes::TIPOS_SANGRE
                    )
                ),
            ],

            'alergias' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'costo_consulta_personalizado' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'finado' => [
                'nullable',
                'boolean',
            ],

            'notas' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'foto' => [
                'nullable',
                'image',
                'max:4096',
            ],
        ];

        $camposSolicitados = match (
            $this->input('seccion')
        ) {
            'contacto' => [
                'telefono',
                'telefono_fijo',
                'telefono_secundario',
                'email',
            ],

            'notas' => [
                'notas',
            ],

            'generales' => [
                'nombre',
                'apellido',
                'fecha_nacimiento',
                'sexo',
                'categoria',
                'lugar_nacimiento',
                'ocupacion',
                'religion',
                'estado_civil',
                'escolaridad',
                'tipo_sangre',
                'alergias',
                'status',
                'finado',
            ],

            default => array_keys($reglas),
        };

        $permisos = app(
            PermisosEdicionPaciente::class
        );

        $camposPermitidos = $permisos->camposPara(
            $this->user()
        );

        $camposValidados = array_values(
            array_intersect(
                $camposSolicitados,
                $camposPermitidos
            )
        );

        return [
            'seccion' => [
                'nullable',
                Rule::in([
                    'contacto',
                    'notas',
                    'generales',
                ]),
            ],

            ...Arr::only(
                $reglas,
                $camposValidados
            ),
        ];
    }
}
