<?php

namespace Tests\Feature;

use App\Models\CasoClinico;
use App\Models\Citas;
use App\Models\EvolucionClinica;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultaAdministrativaCasoClinicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_administracion_puede_consultar_caso_y_evolucion(): void
    {
        $administracion =
            User::factory()->create([
                'name' => 'Administración',
                'role' => 'admin',
                'status' => true,
            ]);

        $usuarioMedico =
            User::factory()->create([
                'name' => 'Médico responsable',
                'role' => 'medico',
                'status' => true,
            ]);

        $medico =
            Medicos::query()->create([
                'user_id' => $usuarioMedico->id,
                'nombre' => 'Médico',
                'apellido_paterno' => 'Responsable',
                'apellido_materno' => 'Prueba',
                'especialidad' => 'Medicina general',
                'cedula' => 'CED-CONSULTA-1',
                'telefono' => '5550000501',
                'consultorio' => 'Consultorio 1',
                'status' => true,
            ]);

        $paciente =
            Pacientes::query()->create([
                'nombre' => 'Paciente',
                'apellido' => 'Consulta',
                'fecha_nacimiento' => '1990-01-01',
                'sexo' => 'masculino',
                'categoria' => 'sin_categoria',
                'status' => true,
            ]);

        $fecha = now()
            ->addDay()
            ->toDateString();

        $cita =
            Citas::query()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'hora' => '10:00',
                'duracion_minutos' => 30,
                'modalidad' => 'presencial',
                'motivo' => 'consulta_subsecuente',
                'notas' =>
                    'Cita para consulta administrativa.',

                'estado' => 'programada',
                'created_by' => $usuarioMedico->id,
            ]);

        $caso =
            CasoClinico::query()->create([
                'paciente_id' => $paciente->id,
                'nombre' =>
                    'Fractura de tobillo administrativa',

                'descripcion_inicial' =>
                    'Caso disponible únicamente para consulta.',

                'fecha_inicio' => $fecha,
                'estado' =>
                    CasoClinico::ESTADO_ACTIVO,

                'created_by' => $usuarioMedico->id,
            ]);

        EvolucionClinica::query()->create([
            'caso_clinico_id' => $caso->id,
            'cita_id' => $cita->id,
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => $fecha,

            'evolucion_clinica' =>
                'Evolución clínica visible para administración.',

            'diagnostico' =>
                'Diagnóstico visible para administración.',

            'tratamiento' =>
                'Tratamiento visible para administración.',

            'created_by' => $usuarioMedico->id,
        ]);

        $respuesta = $this
            ->actingAs($administracion)
            ->get(
                route(
                    'citas.show',
                    $cita
                )
            );

        $respuesta
            ->assertOk()
            ->assertViewHas(
                'puedeConsultarInformacionClinica',
                true
            )
            ->assertSee(
                'Fractura de tobillo administrativa'
            )
            ->assertSee(
                'Evolución clínica visible para administración.'
            )
            ->assertSee(
                'Diagnóstico visible para administración.'
            );
    }
}