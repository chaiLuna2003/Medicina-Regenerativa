<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicos extends Model
{
    protected $fillable = [
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

     public function citas()
    {
    return $this->hasMany(Citas::class);
    }
}