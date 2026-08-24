<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExploracionFisica extends Model
{
    protected $table = 'exploraciones_fisicas';

    /**
     * Campos narrativos de la exploración.
     */
    public const CAMPOS = [
        'interrogatorio' => 'Interrogatorio',
        'anotaciones' => 'Anotaciones',
        'exploracion_fisica' => 'Exploración física',
        'recomendaciones' => 'Recomendaciones',
    ];

    protected $fillable = [
        'historia_clinica_id',
        'cita_id',
        'medico_id',
        'interrogatorio',
        'anotaciones',
        'exploracion_fisica',
        'recomendaciones',
    ];

    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(
            HistoriaClinica::class,
            'historia_clinica_id'
        );
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(
            Citas::class,
            'cita_id'
        );
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(
            Medicos::class,
            'medico_id'
        );
    }
}