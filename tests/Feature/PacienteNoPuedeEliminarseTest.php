<?php

namespace Tests\Feature;

use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PacienteNoPuedeEliminarseTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_existe_ruta_para_eliminar_pacientes(): void
    {
        $this->assertFalse(
            Route::has('pacientes.destroy')
        );
    }

    public function test_administrador_no_puede_eliminar_un_paciente(): void
    {
        $administrador = User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Protegido',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $this
            ->actingAs($administrador)
            ->delete("/pacientes/{$paciente->id}")
            ->assertStatus(405);

        $this->assertDatabaseHas('pacientes', [
            'id' => $paciente->id,
            'nombre' => 'Paciente',
            'apellido' => 'Protegido',
        ]);
    }
}