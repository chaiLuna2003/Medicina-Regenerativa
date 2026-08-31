<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CasoClinico extends Model
{
    public const ESTADO_ACTIVO = 'activo';

    public const ESTADO_CERRADO = 'cerrado';

    public const ESTADOS = [
        self::ESTADO_ACTIVO => 'Activo',
        self::ESTADO_CERRADO => 'Cerrado',
    ];

    protected $table = 'casos_clinicos';

    protected $fillable = [
        'paciente_id',
        'nombre',
        'descripcion_inicial',
        'fecha_inicio',
        'estado',
        'created_by',
        'fecha_cierre',
        'cerrado_por',
        'motivo_cierre',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_cierre' => 'datetime',
        ];
    }

    /**
     * Paciente propietario del caso clínico.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(
            Pacientes::class,
            'paciente_id'
        );
    }

    /**
     * Usuario médico que abrió el caso.
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * Usuario médico que cerró el caso clínico.
     */
    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'cerrado_por'
        );
    }

    /**
     * Evoluciones registradas durante el seguimiento.
     */
    public function evoluciones(): HasMany
    {
        return $this->hasMany(
            EvolucionClinica::class,
            'caso_clinico_id'
        );
    }

    /**
     * Determina si el caso todavía admite seguimientos.
     */
    public function estaActivo(): bool
    {
        return $this->estado
            === self::ESTADO_ACTIVO;
    }

    /**
     * Determina si el seguimiento ya fue cerrado.
     */
    public function estaCerrado(): bool
    {
        return $this->estado
            === self::ESTADO_CERRADO;
    }
}