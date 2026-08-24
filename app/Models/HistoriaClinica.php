<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistoriaClinica extends Model
{
    protected $table = 'historias_clinicas';

    protected $fillable = [
        'paciente_id',
        'patologia_base',
        'padecimiento_actual',
        'tratamientos_actuales',
        'prioridad_analisis_medico',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(
            Pacientes::class,
            'paciente_id'
        );
    }

    public function antecedentesHeredofamiliares(): HasOne
    {
        return $this->hasOne(
            AntecedenteHeredofamiliar::class,
            'historia_clinica_id'
        );
    }

    public function antecedentesPersonalesPatologicos(): HasOne
    {
        return $this->hasOne(
            AntecedentePersonalPatologico::class,
            'historia_clinica_id'
        );
    }

    public function antecedentesPersonalesNoPatologicos(): HasOne
    {
        return $this->hasOne(
            AntecedentePersonalNoPatologico::class,
            'historia_clinica_id'
        );
    }

    public function habitoAlimenticio(): HasOne
    {
        return $this->hasOne(
            HabitoAlimenticio::class,
            'historia_clinica_id'
        );
    }

    public function exploracionesFisicas(): HasMany
    {
        return $this->hasMany(
            ExploracionFisica::class,
            'historia_clinica_id'
        );
    }
}
