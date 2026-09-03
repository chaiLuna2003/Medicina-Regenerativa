<?php

namespace App\Support\Pacientes;

use App\Models\User;

class PermisosEdicionPaciente
{
    private const CAMPOS_IDENTIDAD = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'sexo',
    ];

    private const CAMPOS_CLASIFICACION = [
        'categoria',
        'status',
    ];

    private const CAMPOS_TELEFONICOS = [
        'telefono',
        'telefono_fijo',
        'telefono_secundario',
    ];

    private const CAMPOS_CONTACTO_NO_TELEFONICO = [
        'email',
    ];

    private const CAMPOS_DOMICILIO = [
        'domicilio',
        'ciudad',
        'estado',
        'codigo_postal',
        'lugar_nacimiento',
    ];

    private const CAMPOS_COMPLEMENTARIOS = [
        'ocupacion',
        'religion',
        'estado_civil',
        'escolaridad',
        'tipo_sangre',
        'alergias',
        'costo_consulta_personalizado',
        'finado',
    ];

    private const CAMPOS_ADMINISTRATIVOS = [
        'notas',
        'foto',
    ];

    private const CAMPOS_COMPLETOS = [
        ...self::CAMPOS_IDENTIDAD,
        ...self::CAMPOS_CLASIFICACION,
        ...self::CAMPOS_TELEFONICOS,
        ...self::CAMPOS_CONTACTO_NO_TELEFONICO,
        ...self::CAMPOS_DOMICILIO,
        ...self::CAMPOS_COMPLEMENTARIOS,
        ...self::CAMPOS_ADMINISTRATIVOS,
    ];

    private const CAMPOS_MEDICO = [
        ...self::CAMPOS_IDENTIDAD,
        ...self::CAMPOS_CLASIFICACION,
        ...self::CAMPOS_CONTACTO_NO_TELEFONICO,
        ...self::CAMPOS_DOMICILIO,
        ...self::CAMPOS_COMPLEMENTARIOS,
        ...self::CAMPOS_ADMINISTRATIVOS,
    ];

    /**
     * @var array<string, list<string>>
     */
    private const CAMPOS_POR_ROL = [
        'admin' => self::CAMPOS_COMPLETOS,
        'recepcionista' => self::CAMPOS_COMPLETOS,
        'medico' => self::CAMPOS_MEDICO,

        /*
         * Enfermería todavía no tiene autorización para modificar
         * los datos generales del paciente.
         *
         * Cuando se habilite, sus campos deberán declararse aquí
         * expresamente.
         */
        'enfermero' => [],
    ];

    /**
     * @return list<string>
     */
    public function camposPara(User $user): array
    {
        return self::CAMPOS_POR_ROL[
            $user->role
        ] ?? [];
    }

    public function puedeEditar(
        User $user,
        string $campo
    ): bool {
        return in_array(
            $campo,
            $this->camposPara($user),
            true
        );
    }
}
