<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Citas extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'fecha',
        'hora',
        'duracion_minutos',
        'modalidad',
        'direccion_cita',
        'google_event_id',
        'google_meet_url',
        'google_calendar_url',
        'estado_videoconferencia',
        'meet_generado_at',
        'motivo',
        'notas',
        'estado',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'duracion_minutos' => 'integer',
            'meet_generado_at' => 'datetime',
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

                $fin = $inicio
                    ->copy()
                    ->addMinutes(
                        $this->duracion_minutos ?? 15
                    );
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

    /**
     * Fecha y hora exactas en las que termina la cita.
     */
    protected function horaFin(): Attribute
    {
        return Attribute::make(
            get: function (): Carbon {
                $inicio = Carbon::parse(
                    $this->fecha->format('Y-m-d')
                        . ' '
                        . $this->hora
                );

                return $inicio->addMinutes(
                    $this->duracion_minutos ?? 15
                );
            }
        );
    }

    /**
     * Nombre visible de la modalidad de atención.
     */
    protected function motivoTexto(): Attribute
    {
        return Attribute::make(
            get: fn(): string => match ($this->motivo) {
                'consulta_inicial' =>
                'Consulta inicial',

                'consulta_subsecuente' =>
                'Consulta subsecuente',

                'consulta_emergencia' =>
                'Consulta de emergencia',

                default =>
                $this->motivo
                    ? ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $this->motivo
                        )
                    )
                    : 'No especificado',
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

    public function exploracionFisica(): HasOne
    {
        return $this->hasOne(
            ExploracionFisica::class,
            'cita_id'
        );
    }

    /**
     * Receta médica asociada con la cita.
     */
    public function receta(): HasOne
    {
        return $this->hasOne(Receta::class, 'cita_id');
    }

    public function estudios(): HasMany
    {
        return $this->hasMany(Estudio::class, 'cita_id');
    }
}
