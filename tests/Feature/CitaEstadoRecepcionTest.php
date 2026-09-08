<?php

namespace Tests\Feature;

use App\Models\Citas;
use Carbon\Carbon;
use Tests\TestCase;

class CitaEstadoRecepcionTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_confirmacion_se_conserva_antes_de_iniciar_la_cita(): void
    {
        Carbon::setTestNow('2026-09-10 09:00:00');

        $cita = $this->crearCita([
            'fecha' => '2026-09-10',
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'estado' => 'confirmada',
        ]);

        $this->assertSame(
            'confirmada',
            $cita->estado_actual
        );
    }

    public function test_asistencia_se_conserva_mientras_la_cita_no_finalice(): void
    {
        Carbon::setTestNow('2026-09-10 10:05:00');

        $cita = $this->crearCita([
            'fecha' => '2026-09-10',
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'estado' => 'en_espera',
        ]);

        $this->assertSame(
            'en_espera',
            $cita->estado_actual
        );
    }

    public function test_cita_no_cancelada_finaliza_automaticamente(): void
    {
        Carbon::setTestNow('2026-09-10 10:31:00');

        $cita = $this->crearCita([
            'fecha' => '2026-09-10',
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'estado' => 'confirmada',
        ]);

        $this->assertSame(
            'finalizada',
            $cita->estado_actual
        );
    }

    public function test_cita_cancelada_conserva_su_estado(): void
    {
        Carbon::setTestNow('2026-09-10 12:00:00');

        $cita = $this->crearCita([
            'fecha' => '2026-09-10',
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'estado' => 'cancelada',
        ]);

        $this->assertSame(
            'cancelada',
            $cita->estado_actual
        );
    }

    /**
     * @param  array<string, mixed>  $atributos
     */
    private function crearCita(array $atributos): Citas
    {
        return new Citas($atributos);
    }
}
