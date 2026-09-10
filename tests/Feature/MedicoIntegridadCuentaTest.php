<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\Universidad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicoIntegridadCuentaTest extends TestCase
{
    use RefreshDatabase;

    public function test_desactivar_medico_desactiva_su_cuenta_de_usuario(): void
    {
        $administrador = User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);

        $universidad = Universidad::query()->create([
            'nombre' => 'Universidad de prueba',
            'abreviatura' => 'UP',
            'status' => true,
        ]);

        [$usuarioMedico, $medico] = $this->crearMedico();

        $respuesta = $this
            ->actingAs($administrador)
            ->put(
                route('medicos.update', $medico),
                [
                    'especialidad' => 'Medicina regenerativa',
                    'universidad_id' => $universidad->id,
                    'cedula' => $medico->cedula,
                    'consultorio' => 'Consultorio 2',
                    'direccion' => 'Dirección actualizada',
                    'telefono' => '5512345678',
                ]
            );

        $respuesta
            ->assertRedirect(route('medicos.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('medicos', [
            'id' => $medico->id,
            'status' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $usuarioMedico->id,
            'status' => false,
        ]);
    }

    public function test_medico_con_historial_clinico_no_puede_eliminarse(): void
    {
        $administrador = User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);

        [$usuarioMedico, $medico] = $this->crearMedico();

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Historial',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        Citas::query()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => now()->subDay()->toDateString(),
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_inicial',
            'estado' => 'finalizada',
            'created_by' => $administrador->id,
        ]);

        $respuesta = $this
            ->actingAs($administrador)
            ->delete(route('medicos.destroy', $medico));

        $respuesta
            ->assertRedirect(route('medicos.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('medicos', [
            'id' => $medico->id,
            'status' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $usuarioMedico->id,
            'status' => true,
        ]);
    }

    public function test_eliminar_medico_sin_historial_desactiva_su_cuenta(): void
    {
        $administrador = User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);

        [$usuarioMedico, $medico] = $this->crearMedico();

        $respuesta = $this
            ->actingAs($administrador)
            ->delete(route('medicos.destroy', $medico));

        $respuesta
            ->assertRedirect(route('medicos.index'))
            ->assertSessionHas('success')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('medicos', [
            'id' => $medico->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $usuarioMedico->id,
            'status' => false,
        ]);
    }

    public function test_reactivar_medico_reactiva_su_cuenta_de_usuario(): void
    {
        $administrador = User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);

        $universidad = Universidad::query()->create([
            'nombre' => 'Universidad para reactivación',
            'abreviatura' => 'UR',
            'status' => true,
        ]);

        [$usuarioMedico, $medico] = $this->crearMedico();

        $medico->update([
            'status' => false,
        ]);

        $usuarioMedico->update([
            'status' => false,
        ]);

        $respuesta = $this
            ->actingAs($administrador)
            ->put(
                route('medicos.update', $medico),
                [
                    'especialidad' => $medico->especialidad,
                    'cedula' => $medico->cedula,
                    'universidad_id' => $universidad->id,
                    'consultorio' => $medico->consultorio,
                    'direccion' => $medico->direccion,
                    'telefono' => $medico->telefono,
                    'status' => true,
                ]
            );

        $respuesta
            ->assertRedirect(route('medicos.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('medicos', [
            'id' => $medico->id,
            'status' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $usuarioMedico->id,
            'status' => true,
        ]);
    }

    /**
     * @return array{0: User, 1: Medicos}
     */
    private function crearMedico(): array
    {
        $usuario = User::factory()->create([
            'name' => 'Médico de prueba',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuario->id,
            'nombre' => $usuario->name,
            'apellido_paterno' => null,
            'apellido_materno' => null,
            'especialidad' => 'Medicina general',
            'cedula' => '12345678',
            'consultorio' => 'Consultorio 1',
            'direccion' => 'Dirección inicial',
            'telefono' => '5512345678',
            'status' => true,
        ]);

        return [$usuario, $medico];
    }
}
