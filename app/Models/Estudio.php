<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estudio extends Model
{
    protected $fillable = [
        'cita_id',
        'uploaded_by',
        'nombre',
        'descripcion',
        'fecha_estudio',
        'archivo_path',
        'archivo_original',
        'mime_type',
        'archivo_size',
    ];

    protected function casts(): array
    {
        return [
            'fecha_estudio' => 'date',
            'archivo_size' => 'integer',
        ];
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Citas::class, 'cita_id');
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}