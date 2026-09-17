<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlPeso extends Model
{
    protected $table = 'controles_peso';

    protected $fillable = [
        'cita_id', 'creado_por', 'actualizado_por', 'diagnostico',
        'peso', 'talla', 'imc', 'porcentaje_grasa', 'porcentaje_musculo',
        'porcentaje_agua', 'porcentaje_hueso', 'indice_antioxidante',
        'objetivos', 'tratamiento_base', 'tratamiento_complementario',
        'peptidos', 'suplementos', 'indicacion_dietetica', 'actividad_fisica',
    ];

    protected function casts(): array
    {
        return [
            'objetivos' => 'array',
            'peptidos' => 'array',
            'suplementos' => 'array',
            'peso' => 'decimal:2',
            'talla' => 'decimal:2',
            'imc' => 'decimal:2',
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

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
