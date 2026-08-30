<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvolucionClinica extends Model
{
    protected $table = 'evoluciones_clinicas';

    protected $fillable = [
        'caso_clinico_id',
        'cita_id',
        'paciente_id',
        'medico_id',
        'fecha',
        'evolucion_clinica',
        'diagnostico',
        'tratamiento',
        'plan_recomendaciones',
        'indicaciones_enfermeria',
        'observaciones',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    /**
     * Caso clínico al que pertenece el seguimiento.
     */
    public function casoClinico(): BelongsTo
    {
        return $this->belongsTo(
            CasoClinico::class,
            'caso_clinico_id'
        );
    }

    /**
     * Cita donde se registró la evolución.
     */
    public function cita(): BelongsTo
    {
        return $this->belongsTo(
            Citas::class,
            'cita_id'
        );
    }

    /**
     * Paciente de la evolución.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(
            Pacientes::class,
            'paciente_id'
        );
    }

    /**
     * Médico responsable de la cita.
     */
    public function medico(): BelongsTo
    {
        return $this->belongsTo(
            Medicos::class,
            'medico_id'
        );
    }

    /**
     * Usuario médico que creó el registro.
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * Aparatos evaluados en esta evolución.
     */
    public function aparatos(): HasMany
    {
        return $this->hasMany(
            EvolucionAparato::class,
            'evolucion_clinica_id'
        );
    }
}