<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CitaAccionesRecepcionTest extends TestCase
{
    use RefreshDatabase;

    public function test_recepcion_puede_confirmar_una_cita_programada(): void
    {
        $datos = $this->escenario('programada');

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->patch(
                route('citas.confirmar', $datos['cita'])
            );

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'confirmada',
        ]);
    }

    public function test_recepcion_puede_marcar_asistencia_de_cita_confirmada(): void
    {
        $datos = $this->escenario('confirmada');

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->patch(
                route('citas.asistencia', $datos['cita'])
            );

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'en_espera',
        ]);
    }

    public function test_cita_programada_no_puede_marcar_asistencia_directamente(): void
    {
        $datos = $this->escenario('programada');

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->patch(
                route('citas.asistencia', $datos['cita'])
            );

        $respuesta->assertSessionHasErrors('estado');

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'programada',
        ]);
    }

    public function test_cita_cancelada_no_puede_confirmarse(): void
    {
        $datos = $this->escenario('cancelada');

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->patch(
                route('citas.confirmar', $datos['cita'])
            );

        $respuesta->assertSessionHasErrors('estado');

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'cancelada',
        ]);
    }

    public function test_cita_finalizada_no_puede_marcar_asistencia(): void
    {
        $datos = $this->escenario('confirmada');

        $datos['cita']->update([
            'fecha' => now()->subDay()->toDateString(),
        ]);

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->patch(
                route('citas.asistencia', $datos['cita'])
            );

        $respuesta->assertSessionHasErrors('estado');

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'confirmada',
        ]);
    }

    public function test_recepcion_cancela_videoconsulta_y_elimina_evento_de_google(): void
    {
        $datos = $this->escenario('programada');

        $datos['cita']->update([
            'modalidad' => 'videoconsulta',
            'google_event_id' => 'evento-google-123',
            'google_meet_url' => 'https://meet.google.com/abc-defg-hij',
            'google_calendar_url' => 'https://calendar.google.com/evento',
            'estado_videoconferencia' => 'disponible',
            'meet_generado_at' => now(),
        ]);

        $this->mock(
            GoogleCalendarService::class,
            function ($mock) use ($datos): void {
                $mock
                    ->shouldReceive('cancelarVideoconsulta')
                    ->once()
                    ->with(
                        Mockery::on(
                            fn (Citas $cita): bool => $cita->is(
                                $datos['cita']
                            )
                        )
                    )
                    ->andReturnNull();
            }
        );

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->patch(
                route('citas.cancelar', $datos['cita'])
            );

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'cancelada',
            'google_event_id' => null,
            'google_meet_url' => null,
            'google_calendar_url' => null,
            'estado_videoconferencia' => 'cancelado',
            'meet_generado_at' => null,
        ]);
    }

    public function test_fallo_de_google_no_cancela_la_cita_localmente(): void
    {
        $datos = $this->escenario('confirmada');

        $datos['cita']->update([
            'modalidad' => 'videoconsulta',
            'google_event_id' => 'evento-google-123',
            'google_meet_url' => 'https://meet.google.com/abc-defg-hij',
            'google_calendar_url' => 'https://calendar.google.com/evento',
            'estado_videoconferencia' => 'disponible',
            'meet_generado_at' => now(),
        ]);

        $this->mock(
            GoogleCalendarService::class,
            function ($mock): void {
                $mock
                    ->shouldReceive('cancelarVideoconsulta')
                    ->once()
                    ->andThrow(
                        new \RuntimeException(
                            'Google Calendar no disponible.'
                        )
                    );
            }
        );

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->from(route('dashboard'))
            ->patch(
                route('citas.cancelar', $datos['cita'])
            );

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('videoconsulta');

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'confirmada',
            'google_event_id' => 'evento-google-123',
            'google_meet_url' => 'https://meet.google.com/abc-defg-hij',
            'google_calendar_url' => 'https://calendar.google.com/evento',
            'estado_videoconferencia' => 'disponible',
        ]);
    }

    public function test_cita_finalizada_no_puede_cancelarse(): void
    {
        $datos = $this->escenario('confirmada');

        $datos['cita']->update([
            'fecha' => now()->subDay()->toDateString(),
        ]);

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->patch(
                route('citas.cancelar', $datos['cita'])
            );

        $respuesta->assertSessionHasErrors('estado');

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'confirmada',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function escenario(string $estado): array
    {
        $recepcion = User::factory()->create([
            'name' => 'Recepción',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'name' => 'Médico de estados',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Estados',
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-ESTADOS-001',
            'telefono' => '5512345678',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Estados',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $cita = Citas::query()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_inicial',
            'notas' => 'Prueba de acciones desde recepción.',
            'estado' => $estado,
            'created_by' => $recepcion->id,
        ]);

        return [
            'recepcion' => $recepcion,
            'medico' => $medico,
            'paciente' => $paciente,
            'cita' => $cita,
        ];
    }
}
