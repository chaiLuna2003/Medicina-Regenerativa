<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receta extends Model
{
    /**
     * Campos permitidos para asignación masiva.
     */
    protected $fillable = [
        'cita_id',
        'contenido',
        'fecha_expedicion',
    ];

    /**
     * Conversión automática de tipos.
     */
    protected function casts(): array
    {
        return [
            'fecha_expedicion' => 'datetime',
        ];
    }

    /**
     * Cita a la que pertenece la receta.
     */
    public function cita(): BelongsTo
    {
        return $this->belongsTo(Citas::class, 'cita_id');
    }
}