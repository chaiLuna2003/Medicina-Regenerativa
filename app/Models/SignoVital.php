<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignoVital extends Model
{
    protected $table = 'signos_vitales';

    protected $fillable = [
        'paciente_id',
        'cita_id',
        'enfermero_id',
        'peso',
        'estatura',
        'temperatura',
        'presion_sistolica',
        'presion_diastolica',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'saturacion_oxigeno',
        'glucosa',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'peso' => 'decimal:2',
            'estatura' => 'decimal:2',
            'temperatura' => 'decimal:1',
            'presion_sistolica' => 'integer',
            'presion_diastolica' => 'integer',
            'frecuencia_cardiaca' => 'integer',
            'frecuencia_respiratoria' => 'integer',
            'saturacion_oxigeno' => 'integer',
            'glucosa' => 'decimal:2',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Pacientes::class, 'paciente_id');
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Citas::class, 'cita_id');
    }

    public function enfermero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enfermero_id');
    }

    public function getImcAttribute(): ?float
    {
        $peso = (float) $this->peso;
        $estaturaMetros = (float) $this->estatura / 100;

        if ($peso <= 0 || $estaturaMetros <= 0) {
            return null;
        }

        return round(
            $peso / ($estaturaMetros ** 2),
            2
        );
    }
}