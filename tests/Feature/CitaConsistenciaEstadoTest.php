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
            ->assertSee('data-abrir-modal-edicion-cita', false)
            ->assertSee('data-formulario-edicion-cita', false)
            ->assertSeeText('Regresar al listado de citas')
            ->assertSee(
                'href="'.route('dashboard').'"',
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

    public function test_recepcion_puede_abrir_edicion_de_cita_finalizada(): void
    {
        $datos = $this->crearEscenarioFinalizado();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->get(route('citas.edit', $datos['cita']));

        $respuesta->assertOk()
            ->assertSee('name="estado"', false);
    }

    public function test_agenda_de_recepcion_ofrece_modificar_y_cancelar_cita_finalizada(): void
    {
        $datos = $this->crearEscenarioFinalizado();

        $this->actingAs($datos['recepcion'])
            ->get(route('dashboard', ['fecha' => '2026-09-10']))
            ->assertOk()
            ->assertSee('"puede_modificar":true', false)
            ->assertSee('"puede_cancelar":true', false);
    }

    public function test_recepcion_puede_reprogramar_cita_finalizada(): void
    {
        $datos = $this->crearEscenarioFinalizado();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->from(route('citas.show', $datos['cita']))
            ->put(route('citas.update', $datos['cita']), [
                'paciente_id' => $datos['paciente']->id,
                'medico_id' => $datos['medico']->id,
                'fecha' => '2026-09-11',
                'hora' => '11:00',
                'duracion_minutos' => 30,
                'modalidad' => 'presencial',
                'motivo' => 'consulta_inicial',
                'notas' => 'Cita reprogramada.',
                'estado' => 'confirmada',
            ]);

        $respuesta
            ->assertRedirect(route('citas.show', $datos['cita']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'confirmada',
            'hora' => '11:00',
            'duracion_minutos' => 30,
        ]);

        $this->assertSame('confirmada', $datos['cita']->refresh()->estado_actual);
    }

    public function test_recepcion_puede_corregir_hora_pasada_de_cita_finalizada(): void
    {
        $datos = $this->crearEscenarioFinalizado();

        $this->actingAs($datos['recepcion'])
            ->put(route('citas.update', $datos['cita']), [
                'paciente_id' => $datos['paciente']->id,
                'medico_id' => $datos['medico']->id,
                'fecha' => '2026-09-10',
                'hora' => '11:00',
                'duracion_minutos' => 30,
                'modalidad' => 'presencial',
                'motivo' => 'consulta_inicial',
                'notas' => 'Hora corregida después de finalizar.',
                'estado' => 'en_espera',
            ])
            ->assertRedirect(route('citas.show', $datos['cita']))
            ->assertSessionHasNoErrors();

        $this->assertSame('11:00', $datos['cita']->refresh()->hora);
        $this->assertSame('finalizada', $datos['cita']->estado_actual);
    }

    public function test_recepcion_puede_cancelar_cita_finalizada_y_conservarla_en_hoja_diaria(): void
    {
        $datos = $this->crearEscenarioFinalizado();

        $this->actingAs($datos['recepcion'])
            ->patch(route('citas.cancelar', $datos['cita']))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertSame('cancelada', $datos['cita']->refresh()->estado_actual);

        $this->actingAs($datos['recepcion'])
            ->view('hoja-diaria.pdf', [
                'citas' => collect([$datos['cita']->load(['paciente', 'medico.user'])]),
                'fecha' => Carbon::parse('2026-09-10'),
                'totalCitas' => 1,
                'totalCitasActivas' => 0,
                'totalCitasCanceladas' => 1,
                'totalPacientes' => 0,
                'medicoSeleccionado' => null,
            ])
            ->assertSeeText('Cancelada')
            ->assertSeeText('Paciente Finalizado');

        $this->assertDatabaseHas('citas', [
            'id' => $datos['cita']->id,
            'estado' => 'cancelada',
            'hora' => '10:00',
        ]);

        $this->assertSame(
            '2026-09-10',
            $datos['cita']->refresh()->fecha->format('Y-m-d')
        );
    }

    public function test_detalle_de_cita_editable_muestra_modal_con_formulario_precargado(): void
    {
        $datos = $this->crearEscenarioFinalizado();

        Carbon::setTestNow('2026-09-10 09:00:00');

        $datos['cita']->update([
            'estado' => 'confirmada',
        ]);

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->get(route('citas.show', $datos['cita']));

        $respuesta
            ->assertOk()
            ->assertViewHas(
                'pacientes',
                fn ($pacientes): bool => $pacientes->contains(
                    'id',
                    $datos['paciente']->id
                )
            )
            ->assertViewHas(
                'medicos',
                fn ($medicos): bool => $medicos->contains(
                    'id',
                    $datos['medico']->id
                )
            )
            ->assertSee('data-abrir-modal-edicion-cita', false)
            ->assertSee('data-modal-edicion-cita', false)
            ->assertSee('data-formulario-edicion-cita', false)
            ->assertDontSee(
                'href="'.route('citas.edit', $datos['cita']).'"',
                false
            )
            ->assertSee(
                'action="'.route('citas.update', $datos['cita']).'"',
                false
            )
            ->assertSee('name="_method" value="PUT"', false)
            ->assertSee('name="paciente_id"', false)
            ->assertSee('name="medico_id"', false)
            ->assertSee('name="fecha"', false)
            ->assertSee('value="2026-09-10"', false)
            ->assertSee('data-valor-anterior="10:00"', false)
            ->assertSee('name="duracion_minutos"', false)
            ->assertSee('data-valor-anterior="30"', false);
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
