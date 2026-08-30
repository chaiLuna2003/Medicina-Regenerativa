<?php

namespace App\Policies;

use App\Models\CasoClinico;
use App\Models\Citas;
use App\Models\User;

class CasoClinicoPolicy
{
    /**
     * Administración y médicos pueden acceder al módulo.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || (
                $user->isMedico()
                && $user->medico !== null
            );
    }

    /**
     * Administración puede consultar cualquier caso.
     *
     * El médico necesita una relación clínica no cancelada
     * con el paciente propietario del caso.
     */
    public function view(
        User $user,
        CasoClinico $casoClinico
    ): bool {
        if ($user->isAdmin()) {
            return true;
        }

        if (
            ! $user->isMedico()
            || $user->medico === null
        ) {
            return false;
        }

        return $casoClinico
            ->paciente
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

    /**
     * Solamente un usuario con perfil médico puede
     * iniciar el proceso de creación.
     */
    public function create(User $user): bool
    {
        return $user->isMedico()
            && $user->medico !== null;
    }

    /**
     * Comprueba que el médico sea responsable de la cita
     * utilizada para abrir el caso.
     */
    public function crearDesdeCita(
        User $user,
        Citas $cita
    ): bool {
        return $user->isMedico()
            && $user->medico !== null
            && (int) $cita->medico_id
                === (int) $user->medico->id
            && $cita->estado !== 'cancelada';
    }

    /**
     * Comprueba que una cita pueda agregarse como nuevo
     * seguimiento de un caso existente.
     */
    public function agregarEvolucion(
        User $user,
        CasoClinico $casoClinico,
        Citas $cita
    ): bool {
        return $this->crearDesdeCita(
            $user,
            $cita
        )
            && $casoClinico->estaActivo()
            && (int) $casoClinico->paciente_id
                === (int) $cita->paciente_id
            && ! $cita
                ->evolucionClinica()
                ->exists();
    }

    /**
     * Solamente el médico que abrió el caso puede modificar
     * su nombre, descripción o estado.
     */
    public function update(
        User $user,
        CasoClinico $casoClinico
    ): bool {
        return $user->isMedico()
            && $user->medico !== null
            && (int) $casoClinico->created_by
                === (int) $user->id;
    }

    /**
     * Los casos clínicos no se eliminan físicamente.
     */
    public function delete(
        User $user,
        CasoClinico $casoClinico
    ): bool {
        return false;
    }

    public function restore(
        User $user,
        CasoClinico $casoClinico
    ): bool {
        return false;
    }

    public function forceDelete(
        User $user,
        CasoClinico $casoClinico
    ): bool {
        return false;
    }
}