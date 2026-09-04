<?php

namespace App\Policies;

use App\Models\Citas;
use App\Models\SignoVital;
use App\Models\User;

class SignoVitalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isEnfermero();
    }

    public function view(
        User $user,
        SignoVital $signoVital
    ): bool {
        if (
            $user->isAdmin()
            || $user->isEnfermero()
        ) {
            return true;
        }

        if (
            ! $user->isMedico()
            || $user->medico === null
            || $signoVital->cita === null
        ) {
            return false;
        }

        return $signoVital->cita->medico_id
            === $user->medico->id;
    }

    public function create(
        User $user,
        Citas $cita
    ): bool {
        if ($user->isEnfermero()) {
            return true;
        }

        if (
            ! $user->isMedico()
            || $user->medico === null
        ) {
            return false;
        }

        return $cita->medico_id
            === $user->medico->id;
    }

    public function update(
        User $user,
        SignoVital $signoVital
    ): bool {
        return false;
    }

    public function delete(
        User $user,
        SignoVital $signoVital
    ): bool {
        return false;
    }

    public function restore(
        User $user,
        SignoVital $signoVital
    ): bool {
        return false;
    }

    public function forceDelete(
        User $user,
        SignoVital $signoVital
    ): bool {
        return false;
    }
}
