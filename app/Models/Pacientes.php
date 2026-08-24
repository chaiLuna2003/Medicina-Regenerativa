<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pacientes extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'telefono',
        'email',
        'notas',
        'foto',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'status' => 'boolean',
        ];
    }

    public function fotoUrl(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/default.webp');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Citas::class, 'paciente_id');
    }

    public function signosVitales(): HasMany
    {
        return $this->hasMany(SignoVital::class, 'paciente_id');
    }

    public function historiaClinica(): HasOne
    {
        return $this->hasOne(
            HistoriaClinica::class,
            'paciente_id'
        );
    }

    protected function edad(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (!$this->fecha_nacimiento) {
                    return null;
                }

                $nacimiento = Carbon::parse(
                    $this->fecha_nacimiento
                )->startOfDay();

                $hoy = now();

                if ($nacimiento->isFuture()) {
                    return null;
                }

                $anios = (int) floor(
                    $nacimiento->diffInYears($hoy)
                );

                if ($anios >= 1) {
                    return $anios === 1
                        ? '1 año'
                        : "{$anios} años";
                }

                $meses = (int) floor(
                    $nacimiento->diffInMonths($hoy)
                );

                if ($meses >= 1) {
                    return $meses === 1
                        ? '1 mes'
                        : "{$meses} meses";
                }

                $dias = (int) floor(
                    $nacimiento->diffInDays($hoy)
                );

                if ($dias >= 14) {
                    $semanas = intdiv($dias, 7);

                    return $semanas === 1
                        ? '1 semana'
                        : "{$semanas} semanas";
                }

                if ($dias >= 1) {
                    return $dias === 1
                        ? '1 día de nacido'
                        : "{$dias} días de nacido";
                }

                return 'Recién nacido';
            }
        );
    }

    /**
     * Historial de recetas médicas del paciente a través de sus citas.
     */
    public function recetas(): HasManyThrough
    {
        return $this->hasManyThrough(
            Receta::class,
            Citas::class,
            'paciente_id', // Llave foránea en la tabla citas
            'cita_id',     // Llave foránea en la tabla recetas
            'id',          // Llave primaria en pacientes
            'id'           // Llave primaria en citas
        );
    }

    public function estudios(): HasManyThrough
    {
        return $this->hasManyThrough(
            Estudio::class,
            Citas::class,
            'paciente_id',
            'cita_id',
            'id',
            'id'
        );
    }
}
