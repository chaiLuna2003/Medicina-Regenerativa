<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExploracionFisicaJsonTest extends TestCase
{
    use RefreshDatabase;

    public function test_ficha_genera_json_valido_para_exploracion_fisica(): void
    {
        $usuarioMedico = User::factory()->create([
            'name' => 'Médico de exploración',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Exploración',
            'apellido_materno' => 'Física',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-EXPLORACION-001',
            'telefono' => '5512345678',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Exploración',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'femenino',
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
            'estado' => 'programada',
            'created_by' => $usuarioMedico->id,
        ]);

        $respuesta = $this
            ->actingAs($usuarioMedico)
            ->get(route('pacientes.show', $paciente));

        $respuesta->assertOk();

        $coincidio = preg_match(
            '/<script\s+id="datos-exploraciones-fisicas"'
                .'\s+type="application\/json">\s*'
                .'(.*?)\s*<\/script>/s',
            $respuesta->getContent(),
            $coincidencias
        );

        $this->assertSame(1, $coincidio);

        $datos = json_decode(
            $coincidencias[1],
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertArrayHasKey(
            (string) $cita->id,
            $datos
        );
    }
}
