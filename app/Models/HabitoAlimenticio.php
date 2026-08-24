<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitoAlimenticio extends Model
{
    protected $table = 'habitos_alimenticios';

    /**
     * Comidas realizadas habitualmente.
     */
    public const COMIDAS = [
        'desayuno' => 'Desayuno',
        'almuerzo' => 'Almuerzo',
        'comida' => 'Comida',
        'cena' => 'Cena',
        'colaciones' => 'Colaciones',
    ];

    /**
     * Catálogo de alimentos tomado de la referencia de Maily.
     */
    public const ALIMENTOS = [
        'tortillas' => 'Tortillas',
        'pan' => 'Pan',
        'cereales' => 'Cereales',
        'refrescos' => 'Refrescos',
        'jugos' => 'Jugos',
        'aguas_frescas' => 'Aguas frescas',

        'frutas' => 'Frutas',
        'frutos_secos' => 'Frutos secos',
        'verduras' => 'Verduras',
        'agua_natural' => 'Agua natural',
        'golosinas' => 'Golosinas',
        'carnes_rojas' => 'Carnes rojas',

        'pollo' => 'Pollo',
        'pescado' => 'Pescado',
        'mariscos' => 'Mariscos',
        'leche' => 'Leche',
        'derivados' => 'Derivados',
        'huevo' => 'Huevo',

        'arroz' => 'Arroz',
        'legumbres' => 'Legumbres',
        'miel' => 'Miel',
        'aceite' => 'Aceite',
        'sal' => 'Sal',
        'azucar' => 'Azúcar',

        'cafe' => 'Café',
        'te' => 'Té',
        'sazonadores' => 'Sazonadores',
        'embutidos' => 'Embutidos',
        'enlatados' => 'Enlatados',
        'pastas' => 'Pastas',

        'suplementos_energeticos' =>
            'Suplementos y energéticos',

        'otros' => 'Otros',
    ];

    protected $fillable = [
        'historia_clinica_id',
        'comidas',
        'alimentos',
    ];

    protected function casts(): array
    {
        return [
            'comidas' => 'array',
            'alimentos' => 'array',
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