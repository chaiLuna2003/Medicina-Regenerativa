<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AntecedenteHeredofamiliar extends Model
{
    protected $table = 'antecedentes_heredofamiliares';

    /**
     * Catálogo oficial de antecedentes heredofamiliares.
     *
     * La clave se utiliza en la base de datos.
     * El valor es el nombre que aparecerá en la vista.
     */
    public const CAMPOS = [
        'diabetes' => 'Diabetes',
        'hipertension_arterial' => 'Hip. arterial',
        'cardiopatias' => 'Cardiopatías',
        'hepatopatias' => 'Hepatopatías',
        'urologicos' => 'Urológicos',
        'neurologicas' => 'Neurológicas',
        'respiratorias' => 'Respiratorias',
        'cancer' => 'Cáncer',
        'alergicas' => 'Alérgicas',
        'metabolicas' => 'Metabólicas',
        'sanguineas' => 'Sanguíneas',
        'articulares' => 'Articulares',
        'inmunologicas' => 'Inmunológicas',
        'malformaciones' => 'Malformaciones',
        'dermatologicas' => 'Dermatológicas',
        'otros' => 'Otros',
    ];

    protected $fillable = [
        'historia_clinica_id',
        'numero_hermanos',
        'antecedentes',
    ];

    protected function casts(): array
    {
        return [
            'numero_hermanos' => 'integer',
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