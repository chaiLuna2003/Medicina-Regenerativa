<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaMedicoAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_medico_muestra_solo_sus_citas(): void
    {
        $datos = $this->crearEscenarioClinico();

        $respuesta = $this
            ->actingAs($datos['usuario_uno'])
            ->get(
                route('dashboard', [
                    'fecha' => $datos['fecha'],
                ])
            );

        $respuesta
    ->assertOk()
    ->assertSee('Paciente Propio')
    ->assertSee(
        'Nota visible para el médico asignado.'
    )
    ->assertDontSee('Paciente Ajeno')
    ->assertDontSee(
        'Esta nota no debe ser visible.'
    );
    }

    public function test_medico_no_puede_abrir_una_cita_ajena(): void
    {
        $datos = $this->crearEscenarioClinico();

        $respuesta = $this
            ->actingAs($datos['usuario_uno'])
            ->get(
                route(
                    'citas.show',
                    $datos['cita_ajena']
                )
            );

        $respuesta->assertForbidden();
    }

    public function test_medico_no_puede_abrir_un_paciente_no_vinculado(): void
    {
        $datos = $this->crearEscenarioClinico();

        $respuesta = $this
            ->actingAs($datos['usuario_uno'])
            ->get(
                route(
                    'pacientes.show',
                    $datos['paciente_ajeno']
                )
            );

        $respuesta->assertForbidden();
    }


    public function test_recepcion_no_ve_notas_en_la_agenda(): void
{
    $datos = $this->crearEscenarioClinico();

    $recepcion = User::factory()->create([
        'name' => 'Recepción',
        'role' => 'recepcionista',
        'status' => true,
    ]);

    $respuesta = $this
        ->actingAs($recepcion)
        ->get(
            route('dashboard', [
                'fecha' => $datos['fecha'],
            ])
        );

    $respuesta
        ->assertOk()
        ->assertDontSee(
            'Nota visible para el médico asignado.'
        )
        ->assertDontSee(
            'Esta nota no debe ser visible.'
        );
}
    /**
     * Crea dos médicos completamente independientes,
     * cada uno con su propio paciente y cita.
     *
     * @return array<string, mixed>
     */
    private function crearEscenarioClinico(): array
    {
        $usuarioUno = User::factory()->create([
            'name' => 'Médico Uno',
            'role' => 'medico',
            'status' => true,
        ]);

        $usuarioDos = User::factory()->create([
            'name' => 'Médico Dos',
            'role' => 'medico',
            'status' => true,
        ]);

        $medicoUno = Medicos::query()->create([
            'user_id' => $usuarioUno->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Uno',
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => 'CEDULA-UNO',
            'telefono' => '5550000001',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        $medicoDos = Medicos::query()->create([
            'user_id' => $usuarioDos->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Dos',
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => 'CEDULA-DOS',
            'telefono' => '5550000002',
            'consultorio' => 'Consultorio 2',
            'status' => true,
        ]);

        $pacientePropio = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Propio',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $pacienteAjeno = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Ajeno',
            'fecha_nacimiento' => '1992-02-02',
            'sexo' => 'femenino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $fecha = now()
            ->addDay()
            ->toDateString();

        $citaPropia = Citas::query()->create([
            'paciente_id' => $pacientePropio->id,
            'medico_id' => $medicoUno->id,
            'fecha' => $fecha,
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_inicial',
            'notas' => 'Nota visible para el médico asignado.',
            'estado' => 'programada',
            'created_by' => $usuarioUno->id,
        ]);

        $citaAjena = Citas::query()->create([
            'paciente_id' => $pacienteAjeno->id,
            'medico_id' => $medicoDos->id,
            'fecha' => $fecha,
            'hora' => '11:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_subsecuente',
            'notas' => 'Esta nota no debe ser visible.',
            'estado' => 'programada',
            'created_by' => $usuarioDos->id,
        ]);

        return [
            'usuario_uno' => $usuarioUno,
            'usuario_dos' => $usuarioDos,
            'medico_uno' => $medicoUno,
            'medico_dos' => $medicoDos,
            'paciente_propio' => $pacientePropio,
            'paciente_ajeno' => $pacienteAjeno,
            'cita_propia' => $citaPropia,
            'cita_ajena' => $citaAjena,
            'fecha' => $fecha,
        ];
    }
}