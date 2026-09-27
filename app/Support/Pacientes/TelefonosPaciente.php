<?php

namespace App\Support\Pacientes;

class TelefonosPaciente
{
    public static function normalizar(?string $telefono): ?string
    {
        if ($telefono === null || trim($telefono) === '') {
            return null;
        }

        $telefono = trim($telefono);

        if (! preg_match('/^\+?[0-9()\s.\-]+$/u', $telefono)) {
            return $telefono;
        }

        $digitos = preg_replace('/\D/', '', $telefono);

        if (str_starts_with($digitos, '521') && strlen($digitos) === 13) {
            $digitos = substr($digitos, 3);
        } elseif (str_starts_with($digitos, '52') && strlen($digitos) === 12) {
            $digitos = substr($digitos, 2);
        }

        return $digitos;
    }
}
