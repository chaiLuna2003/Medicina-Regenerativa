<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_recepcion_puede_cancelar_una_cita_programada(): void
    {
        $datos = $this->escenario('programada');

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
