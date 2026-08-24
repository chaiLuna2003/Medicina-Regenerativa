<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AntecedentePersonalNoPatologico extends Model
{
    protected $table =
        'antecedentes_personales_no_patologicos';

    /**
     * Catálogo oficial de antecedentes personales
     * no patológicos.
     */
    public const CAMPOS = [
        'casa_habitacion' =>
            'Casa habitación',

        'lavado_dientes_diario' =>
            'Lavado de dientes diario',

        'tipo_pasta_dental' =>
            'Tipo de pasta dental',

        'amalgamas_puentes' =>
            'Amalgamas o puentes',

        'uso_brackets' =>
            'Uso de brackets',

        'actividad_fisica' =>
            'Actividad física',

        'inmunizaciones' =>
            'Inmunizaciones',

        'ultima_desparasitacion' =>
            'Última desparasitación',

        'checkup' =>
            'Check-up',
    ];

    protected $fillable = [
        'historia_clinica_id',
        'antecedentes',
    ];

    protected function casts(): array
    {
        return [
            'antecedentes' => 'array',
        ];
    }

    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(
            HistoriaClinica::class,
            'historia_clinica_id'
        );
    }
}