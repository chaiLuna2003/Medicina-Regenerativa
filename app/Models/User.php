<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atributos que pueden asignarse de forma masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * Atributos ocultos durante la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión automática de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMedico(): bool
    {
        return $this->role === 'medico';
    }

    public function isEnfermero(): bool
    {
        return $this->role === 'enfermero';
    }

    public function isRecepcionista(): bool
    {
        return $this->role === 'recepcionista';
    }

    public function isActive(): bool
    {
        return $this->status;
    }

    public function createdAppointments(): HasMany
    {
        return $this->hasMany(Citas::class, 'created_by');
    }

    public function medico(): HasOne
    {
        return $this->hasOne(Medicos::class, 'user_id');
    }

    public function signosVitalesRegistrados(): HasMany
    {
        return $this->hasMany(SignoVital::class, 'enfermero_id');
    }
}