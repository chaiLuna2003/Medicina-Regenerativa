<?php

namespace App\Policies;

use App\Models\Citas;
use App\Models\EvolucionClinica;
use App\Models\User;

class EvolucionClinicaPolicy
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
     * Administración puede consultar cualquier evolución.
     *
     * El médico necesita una relación clínica no cancelada
     * con el paciente.
     */
    public function view(
        User $user,
        EvolucionClinica $evolucionClinica
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

        return Citas::query()
            ->where(
                'paciente_id',
                $evolucionClinica->paciente_id
            )
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
     * Solamente usuarios médicos pueden iniciar
     * el proceso de creación.
     */
    public function create(User $user): bool
    {
        return $user->isMedico()
            && $user->medico !== null;
    }

    /**
     * Comprueba que la evolución se cree desde una cita
     * asignada al médico autenticado.
     */
    public function crearDesdeCita(
        User $user,
        Citas $cita
    ): bool {
        return $user->isMedico()
            && $user->medico !== null
            && (int) $cita->medico_id
                === (int) $user->medico->id
            && $cita->estado !== 'cancelada'
            && ! $cita
                ->evolucionClinica()
                ->exists();
    }

    /**
     * Solamente el médico responsable de la cita puede
     * modificar la evolución.
     */
    public function update(
        User $user,
        EvolucionClinica $evolucionClinica
    ): bool {
        return $user->isMedico()
            && $user->medico !== null
            && (int) $evolucionClinica->medico_id
                === (int) $user->medico->id
            && $evolucionClinica->cita !== null
            && $evolucionClinica->cita->estado
                !== 'cancelada';
    }

    /**
     * Los aparatos utilizan la misma autorización
     * de modificación que la evolución.
     */
    public function gestionarAparatos(
        User $user,
        EvolucionClinica $evolucionClinica
    ): bool {
        return $this->update(
            $user,
            $evolucionClinica
        );
    }

    /**
     * Las evoluciones no se eliminan físicamente.
     */
    public function delete(
        User $user,
        EvolucionClinica $evolucionClinica
    ): bool {
        return false;
    }

    public function restore(
        User $user,
        EvolucionClinica $evolucionClinica
    ): bool {
        return false;
    }

    public function forceDelete(
        User $user,
        EvolucionClinica $evolucionClinica
    ): bool {
        return false;
    }
}