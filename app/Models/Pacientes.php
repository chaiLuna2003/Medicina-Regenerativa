<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Pacientes extends Model
{
    protected $fillable = [
    'nombre',
    'apellido',
    'fecha_nacimiento',
    'telefono',
    'email',
    'notas',
    'foto',
    'status',
];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function fotoUrl(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/default.webp');
    }

    public function citas()
    {
    return $this->hasMany(Citas::class);
    }

 

protected function casts(): array
{
    return [
        'fecha_nacimiento' => 'date',
        'status' => 'boolean',
    ];
}

protected function edad(): Attribute
{
    return Attribute::make(
        get: function () {
            if (!$this->fecha_nacimiento) {
                return null;
            }

            $nacimiento = Carbon::parse($this->fecha_nacimiento)->startOfDay();
            $hoy = now();

            if ($nacimiento->isFuture()) {
                return null;
            }

            $anios = (int) floor($nacimiento->diffInYears($hoy));

            if ($anios >= 1) {
                return $anios === 1
                    ? '1 año'
                    : "{$anios} años";
            }

            $meses = (int) floor($nacimiento->diffInMonths($hoy));

            if ($meses >= 1) {
                return $meses === 1
                    ? '1 mes'
                    : "{$meses} meses";
            }

            $dias = (int) floor($nacimiento->diffInDays($hoy));

            if ($dias >= 14) {
                $semanas = intdiv($dias, 7);

                return $semanas === 1
                    ? '1 semana'
                    : "{$semanas} semanas";
            }

            if ($dias >= 1) {
                return $dias === 1
                    ? '1 día de nacido'
                    : "{$dias} días de nacido";
            }

            return 'Recién nacido';
        }
    );
}
}