<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaBloqueo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'medico_id',
        'creado_por',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'motivo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(
            Medicos::class,
            'medico_id'
        );
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'creado_por'
        );
    }
}
