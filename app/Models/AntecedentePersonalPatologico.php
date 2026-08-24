<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AntecedentePersonalPatologico extends Model
{
    protected $table =
        'antecedentes_personales_patologicos';

    /**
     * Catálogo oficial de antecedentes personales patológicos.
     */
    public const CAMPOS = [
        'enfermedades_infancia' =>
            'Enf. prop. de la infancia',

        'diabetes' =>
            'Diabetes',

        'hipertension' =>
            'Hipertensión',

        'respiratorias' =>
            'Respiratorias',

        'oftalmico' =>
            'Oftálmico',

        'cardiovasculares' =>
            'Cardiovasculares',

        'neurologicos' =>
            'Neurológicos',

        'gastrointestinales' =>
            'Gastrointestinales',

        'hepatopatias' =>
            'Hepatopatías',

        'metabolicas' =>
            'Metabólicas',

        'urologicos' =>
            'Urológicos',

        'circulatorio' =>
            'Circulatorio',

        'traumaticas' =>
            'Traumáticas',

        'articulares' =>
            'Articulares',

        'dermatologicas' =>
            'Dermatológicas',

        'quirurgicos' =>
            'Quirúrgicos',

        'transfusionales' =>
            'Transfusionales',

        'alergias' =>
            'Alergias',

        'vectores' =>
            'Vectores',

        'autoinmunes' =>
            'Autoinmunes',

        'emocionales' =>
            'Emocionales',

        'adicciones' =>
            'Adicciones',

        'hospitalizaciones_previas' =>
            'Hospitalizaciones previas',

        'pesticidas' =>
            'Pesticidas',

        'diagnostico_cancer' =>
            'Dx. Cáncer',

        'otros' =>
            'Otros',
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