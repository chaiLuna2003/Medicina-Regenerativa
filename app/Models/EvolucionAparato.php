<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvolucionAparato extends Model
{
    public const ESTADO_NO_EVALUADO =
        'no_evaluado';

    public const ESTADO_NORMAL =
        'normal';

    public const ESTADO_REQUIERE_ATENCION =
        'requiere_atencion';

    public const ESTADO_CRITICO =
        'critico';

    public const ESTADOS = [
        self::ESTADO_NO_EVALUADO => [
            'nombre' => 'No evaluado',
            'color' => 'gris',
        ],

        self::ESTADO_NORMAL => [
            'nombre' => 'Normal',
            'color' => 'verde',
        ],

        self::ESTADO_REQUIERE_ATENCION => [
            'nombre' => 'Requiere atención',
            'color' => 'amarillo',
        ],

        self::ESTADO_CRITICO => [
            'nombre' => 'Crítico',
            'color' => 'rojo',
        ],
    ];

    /**
     * Catálogo independiente de aparatos.
     *
     * No está relacionado con los sistemas almacenados
     * en las exploraciones físicas.
     */
    public const APARATOS = [
        'cerebro' => [
            'nombre' => 'Cerebro',
            'imagen' => 'images/default.webp',
        ],

        'sistema_nervioso' => [
            'nombre' => 'Sistema nervioso',
            'imagen' => 'images/default.webp',
        ],

        'sistema_visual' => [
            'nombre' => 'Sistema visual',
            'imagen' => 'images/default.webp',
        ],

        'metabolico' => [
            'nombre' => 'Metabólico',
            'imagen' => 'images/default.webp',
        ],

        'corazon' => [
            'nombre' => 'Corazón',
            'imagen' => 'images/default.webp',
        ],

        'sistema_vascular' => [
            'nombre' => 'Sistema vascular',
            'imagen' => 'images/default.webp',
        ],

        'sistema_respiratorio' => [
            'nombre' => 'Sistema respiratorio',
            'imagen' => 'images/default.webp',
        ],

        'sistema_hepatico' => [
            'nombre' => 'Sistema hepático',
            'imagen' => 'images/default.webp',
        ],

        'pancreas' => [
            'nombre' => 'Páncreas',
            'imagen' => 'images/default.webp',
        ],

        'renal' => [
            'nombre' => 'Renal',
            'imagen' => 'images/default.webp',
        ],

        'gastrointestinal' => [
            'nombre' => 'Gastrointestinal',
            'imagen' => 'images/default.webp',
        ],

        'osteoarticular' => [
            'nombre' => 'Osteoarticular',
            'imagen' => 'images/default.webp',
        ],

        'tendomuscular' => [
            'nombre' => 'Tendomuscular',
            'imagen' => 'images/default.webp',
        ],

        'sistema_reproductor' => [
            'nombre' => 'Sistema reproductor',
            'imagen' => 'images/default.webp',
        ],

        'inmunohematologico' => [
            'nombre' => 'Inmunohematológico',
            'imagen' => 'images/default.webp',
        ],

        'extremidades' => [
            'nombre' => 'Extremidades',
            'imagen' => 'images/default.webp',
        ],

        'piel' => [
            'nombre' => 'Piel',
            'imagen' => 'images/default.webp',
        ],

        'otros' => [
            'nombre' => 'Otros',
            'imagen' => 'images/default.webp',
        ],
    ];

    protected $table = 'evolucion_aparatos';

    protected $fillable = [
        'evolucion_clinica_id',
        'aparato',
        'estado',
        'observaciones',
    ];

    /**
     * Evolución clínica donde se evaluó el aparato.
     */
    public function evolucionClinica(): BelongsTo
    {
        return $this->belongsTo(
            EvolucionClinica::class,
            'evolucion_clinica_id'
        );
    }
}