<?php

namespace App\Policies;

use App\Models\Pacientes;
use App\Models\User;

class PacientesPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isRecepcionista()
            || (
                $user->isMedico()
                && $user->medico !== null
            );
    }

    public function view(
        User $user,
        Pacientes $pacientes
    ): bool {
        if (
            $user->isAdmin()
            || $user->isRecepcionista()
        ) {
            return true;
        }

        return $this->medicoTieneRelacionClinica(
            $user,
            $pacientes
        );
    }

    public function create(User $user): bool
    {
        return $user->isAdmin()
            || $user->isRecepcionista();
    }

    public function update(
        User $user,
        Pacientes $pacientes
    ): bool {
        if (
            $user->isAdmin()
            || $user->isRecepcionista()
        ) {
            return true;
        }

        return $this->medicoTieneRelacionClinica(
            $user,
            $pacientes
        );
    }

    public function delete(
        User $user,
        Pacientes $pacientes
    ): bool {
        return false;
    }

    public function restore(
        User $user,
        Pacientes $pacientes
    ): bool {
        return false;
    }

    public function forceDelete(
        User $user,
        Pacientes $pacientes
    ): bool {
        return false;
    }

    private function medicoTieneRelacionClinica(
        User $user,
        Pacientes $pacientes
    ): bool {
        if (
            ! $user->isMedico()
            || $user->medico === null
        ) {
            return false;
        }

        return $pacientes
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
}
