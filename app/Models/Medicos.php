<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicos extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'especialidad',
        'cedula',
        'telefono',
        'correo',
        'consultorio',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Citas::class, 'medico_id');
    }
}