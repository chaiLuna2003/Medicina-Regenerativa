<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    protected function estadoActual(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->estado === 'cancelada') {
                    return 'cancelada';
                }

                $inicio = Carbon::parse(
                    $this->fecha->format('Y-m-d') . ' ' . $this->hora
                );

                $fin = $inicio->copy()->addMinutes(15);
                $ahora = now();

                if ($ahora->lt($inicio)) {
                    return 'programada';
                }

                if ($ahora->lt($fin)) {
                    return 'en_curso';
                }

                return 'finalizada';
            }
        );
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

    public function signoVital(): HasOne
    {
        return $this->hasOne(SignoVital::class, 'cita_id');
    }
}