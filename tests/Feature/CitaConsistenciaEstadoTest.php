<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitaConsistenciaEstadoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_detalle_muestra_estado_finalizado_segun_el_horario(): void
    {
        $datos = $this->crearEscenarioFinalizado();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->get(route('citas.show', $datos['cita']));

        $respuesta
            ->assertOk()
            ->assertSeeText('Finalizada')
            ->assertDontSeeText('En espera')
            ->assertDontSee(
                route('citas.edit', $datos['cita']),
                false
            );
    }

    public function test_hoja_diaria_muestra_estado_finalizado_segun_el_horario(): void
    {
        $datos = $this->crearEscenarioFinalizado();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->view('hoja-diaria.pdf', [
                'citas' => collect([
                    $datos['cita']->load([
                        'paciente',
                        'medico.user',
                    ]),
                ]),
                'fecha' => Carbon::parse('2026-09-10'),
                'totalCitas' => 1,
                'totalCitasActivas' => 1,
                'totalCitasCanceladas' => 0,
                'totalPacientes' => 1,
                'medicoSeleccionado' => null,
            ]);

        $respuesta
            ->assertSeeText('Finalizada')
            ->assertDontSeeText('En espera');
    }

    public function test_recepcion_no_puede_abrir_edicion_de_cita_finalizada(): void
    {
        $datos = $this->crearEscenarioFinalizado();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->get(route('citas.edit', $datos['cita']));

        $respuesta
            ->assertRedirect(route('citas.show', $datos['cita']))
            ->assertSessionHas('error');
    }

    public function test_recepcion_no_puede_actualizar_cita_finalizada_directamente(): void
    {
        $datos = $this->crearEscenarioFinalizado();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->from(route('citas.show', $datos['cita']))
            ->put(route('citas.update', $datos['cita']), []);

        $respuesta
            ->assertRedirect(route('citas.show', $datos['cita']))
            ->assertSessionHasErrors('cita');

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'en_espera',
            'hora' => '10:00',
            'duracion_minutos' => 30,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function crearEscenarioFinalizado(): array
    {
        Carbon::setTestNow('2026-09-10 12:00:00');

        $recepcion = User::factory()->create([
            'name' => 'Recepción',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'name' => 'Médico de consistencia',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Consistencia',
            'apellido_materno' => 'Estados',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-CONSISTENCIA-001',
            'telefono' => '5512345678',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Finalizado',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $cita = Citas::query()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => '2026-09-10',
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_inicial',
            'notas' => 'Prueba de consistencia de estados.',
            'estado' => 'en_espera',
            'created_by' => $recepcion->id,
        ]);

        return compact(
            'recepcion',
            'medico',
            'paciente',
            'cita'
        );
    }
}
