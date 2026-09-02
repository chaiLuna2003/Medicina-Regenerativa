<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriaClinicaDocumento extends Model
{
    protected $fillable = [
        'paciente_id',
        'generado_por',
        'archivo_path',
        'archivo_nombre',
        'mime_type',
        'archivo_size',
        'generado_en',
    ];

    protected function casts(): array
    {
        return [
            'archivo_size' => 'integer',
            'generado_en' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(
            Pacientes::class,
            'paciente_id'
        );
    }

    public function generadoPor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'generado_por'
        );
    }
}