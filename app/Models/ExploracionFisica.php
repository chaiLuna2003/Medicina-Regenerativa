<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExploracionFisica extends Model
{
    protected $table = 'exploraciones_fisicas';

    /**
     * Campos generales de la consulta.
     */
    public const CAMPOS = [
        'interrogatorio' => 'Interrogatorio',
        'anotaciones' => 'Anotaciones',
        'recomendaciones' => 'Recomendaciones',
    ];

    /**
     * Sistemas y órganos evaluados durante
     * la exploración física.
     */
    public const SISTEMAS = [
        'cerebro' => 'Cerebro',

        'sistema_nervioso' =>
            'Sistema nervioso',

        'sistema_ocular' =>
            'Sistema ocular',

        'sistema_endocrino' =>
            'Sistema endócrino',

        'corazon' => 'Corazón',

        'sistema_circulatorio' =>
            'Sistema circulatorio',

        'sistema_respiratorio' =>
            'Sistema respiratorio',

        'sistema_hepatico' =>
            'Sistema hepático',

        'pancreas' => 'Páncreas',

        'sistema_renal' =>
            'Sistema renal',

        'gastrointestinal' =>
            'Gastrointestinal',

        'osteoarticular' =>
            'Osteoarticular',

        'tendomuscular' =>
            'Tendomuscular',

        'sistema_reproductor' =>
            'Sistema reproductor',

        'sistema_inmunologico' =>
            'Sistema inmunológico',

        'extremidades' =>
            'Extremidades',

        'piel_tegumentos' =>
            'Piel y tegumentos',

        'otros' => 'Otros',
    ];

    protected $fillable = [
        'historia_clinica_id',
        'cita_id',
        'medico_id',
        'interrogatorio',
        'anotaciones',

        // Se conserva para compatibilidad con
        // registros narrativos anteriores.
        'exploracion_fisica',

        'sistemas',
        'recomendaciones',
    ];

    protected function casts(): array
    {
        return [
            'sistemas' => 'array',
        ];
    }

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