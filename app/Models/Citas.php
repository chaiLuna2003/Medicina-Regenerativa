<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * Fecha y hora exactas en las que comienza la cita.
     */
    public function fechaHoraInicio(): Carbon
    {
        return Carbon::parse(
            $this->fecha->format('Y-m-d').' '.$this->hora
        );
    }

    /**
     * Fecha y hora exactas en las que termina la cita.
     */
    public function fechaHoraFin(): Carbon
    {
        return $this->fechaHoraInicio()
            ->addMinutes($this->duracion_minutos ?? 15);
    }

    /**
     * Determina el estado efectivo de la cita.
     *
     * Esta regla no modifica la base de datos. Solamente calcula
     * el estado que debe utilizarse en interfaces y reportes.
     */
    public function estadoEfectivo(): string
    {
        if (
            in_array(
                $this->estado,
                ['cancelada', 'finalizada'],
                true
            )
        ) {
            return $this->estado;
        }

        $inicio = $this->fechaHoraInicio();
        $fin = $this->fechaHoraFin();
        $ahora = now();

        if ($ahora->gte($fin)) {
            return 'finalizada';
        }

        if ($ahora->gte($inicio)) {
            if (
                in_array(
                    $this->estado,
                    ['en_espera', 'en_consulta'],
                    true
                )
            ) {
                return $this->estado;
            }

            return 'en_curso';
        }

        if (
            in_array(
                $this->estado,
                ['confirmada', 'en_espera'],
                true
            )
        ) {
            return $this->estado;
        }

        return 'programada';
    }

    /**
     * Indica si recepción puede modificar administrativamente la cita.
     */
    public function puedeEditarAdministrativamente(): bool
    {
        if (! now()->lt($this->fechaHoraInicio())) {
            return false;
        }

        return in_array(
            $this->estadoEfectivo(),
            ['programada', 'confirmada'],
            true
        );
    }

    protected function estadoActual(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->estadoEfectivo()
        );
    }

    /**
     * Fecha y hora exactas en las que termina la cita.
     */
    protected function horaFin(): Attribute
    {
        return Attribute::make(
            get: fn (): Carbon => $this->fechaHoraFin()
        );
    }

    /**
     * Nombre visible del motivo de la cita.
     */
    protected function motivoTexto(): Attribute
    {
        return Attribute::make(
            get: fn (): string => match ($this->motivo) {
                'consulta_inicial' => 'Consulta inicial',
                'consulta_subsecuente' => 'Consulta subsecuente',
                'consulta_emergencia' => 'Consulta de emergencia',

                default => $this->motivo
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
        return $this->belongsTo(
            Pacientes::class,
            'paciente_id'
        );
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(
            Medicos::class,
            'medico_id'
        );
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function signoVital(): HasOne
    {
        return $this->hasOne(
            SignoVital::class,
            'cita_id'
        );
    }

    public function exploracionFisica(): HasOne
    {
        return $this->hasOne(
            ExploracionFisica::class,
            'cita_id'
        );
    }

    /**
     * Evolución clínica registrada durante la cita.
     */
    public function evolucionClinica(): HasOne
    {
        return $this->hasOne(
            EvolucionClinica::class,
            'cita_id'
        );
    }

    /**
     * Receta médica asociada con la cita.
     */
    public function receta(): HasOne
    {
        return $this->hasOne(
            Receta::class,
            'cita_id'
        );
    }

    public function estudios(): HasMany
    {
        return $this->hasMany(
            Estudio::class,
            'cita_id'
        );
    }
}
