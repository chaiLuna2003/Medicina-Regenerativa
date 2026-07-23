<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Citas extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'fecha',
        'hora',
        'motivo',
        'notas',
        'estado',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Pacientes::class, 'paciente_id');
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medicos::class, 'medico_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}