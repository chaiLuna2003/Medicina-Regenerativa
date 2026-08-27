<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AntecedenteGinecoobstetrico extends Model
{
    protected $table =
        'antecedentes_ginecoobstetricos';

    protected $fillable = [
        'historia_clinica_id',

        /*
        |--------------------------------------------------------------------------
        | Historia menstrual y sexual
        |--------------------------------------------------------------------------
        */

        'edad_menarca',
        'ritmo_menstrual',
        'duracion_menstruacion_dias',
        'fecha_ultima_menstruacion',
        'edad_inicio_vida_sexual',
        'numero_parejas_sexuales',

        /*
        |--------------------------------------------------------------------------
        | Historia obstétrica
        |--------------------------------------------------------------------------
        */

        'gestas',
        'partos',
        'cesareas',
        'abortos',
        'embarazos_ectopicos',
        'hijos_vivos',
        'embarazo_actual',

        /*
        |--------------------------------------------------------------------------
        | Anticoncepción y menopausia
        |--------------------------------------------------------------------------
        */

        'metodo_anticonceptivo',
        'menopausia',
        'edad_menopausia',

        /*
        |--------------------------------------------------------------------------
        | Prevención y antecedentes
        |--------------------------------------------------------------------------
        */

        'fecha_ultimo_papanicolaou',
        'resultado_papanicolaou',
        'fecha_ultima_mastografia',
        'resultado_mastografia',
        'infecciones_transmision_sexual',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ultima_menstruacion' => 'date',

            'fecha_ultimo_papanicolaou' => 'date',

            'fecha_ultima_mastografia' => 'date',

            'embarazo_actual' => 'boolean',

            'menopausia' => 'boolean',
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