<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pacientes extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'edad',
        'telefono',
        'email',
        'notas',
        'foto',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function fotoUrl(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/avatar-default.png');
    }

    public function citas()
    {
    return $this->hasMany(Citas::class);
    }
}