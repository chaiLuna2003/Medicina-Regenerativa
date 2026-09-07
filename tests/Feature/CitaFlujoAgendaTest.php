<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitaFlujoAgendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_error_de_creacion_conserva_datos_y_reabre_modal(): void
    {
        $datos = $this->escenario();
        $fecha = now()
            ->addDays(3)
            ->toDateString();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->from(route('dashboard'))
            ->post(route('citas.store'), [
                'paciente_id' => $datos['paciente']->id,

                'medico_id' => $datos['medico']->id,

                'fecha' => $fecha,
                'hora' => '10:00',
                'duracion_minutos' => 30,
                'modalidad' => 'presencial',
                'direccion_cita' => null,

                /*
                 * Se omite motivo para provocar
                 * el error deliberadamente.
                 */
                'notas' => 'La información debe conservarse.',

                'estado' => 'programada',
            ]);

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrorsIn(
                'crearCita',
                'motivo'
            )
            ->assertSessionHasInput(
                'paciente_id',
                $datos['paciente']->id
            )
            ->assertSessionHasInput(
                'medico_id',
                $datos['medico']->id
            )
            ->assertSessionHasInput(
                'fecha',
                $fecha
            )
            ->assertSessionHasInput(
                'hora',
                '10:00'
            )
            ->assertSessionHasInput(
                'duracion_minutos',
                30
            )
            ->assertSessionHasInput(
                'notas',
                'La información debe conservarse.'
            );

        $this->assertDatabaseCount(
            'citas',
            0
        );

        $dashboard = $this
            ->actingAs($datos['recepcion'])
            ->get(route('dashboard'));

        $dashboard
            ->assertOk()
            ->assertSeeText(
                'Revisa los siguientes campos:'
            )
            ->assertSeeText(
                'Paciente Agenda'
            )
            ->assertSee(
                'const debeReabrirModal =',
                false
            )
            ->assertSee(
                'true;',
                false
            );
    }

    public function test_cita_cancelada_libera_bloques_y_permite_nueva_cita(): void
    {
        $datos = $this->escenario();
        $fecha = now()
            ->addDays(4)
            ->toDateString();

        $citaCancelada = Citas::query()->create([
            'paciente_id' => $datos['paciente']->id,

            'medico_id' => $datos['medico']->id,

            'fecha' => $fecha,
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_inicial',
            'notas' => 'Cita cancelada conservada.',
            'estado' => 'cancelada',
            'created_by' => $datos['recepcion']->id,
        ]);

        $otroPaciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Disponible',
            'fecha_nacimiento' => '1992-02-02',
            'sexo' => 'femenino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $dashboard = $this
            ->actingAs($datos['recepcion'])
            ->get(route('dashboard', [
                'fecha' => $fecha,
                'medico_id' => $datos['medico']->id,
            ]));

        $dashboard
            ->assertOk()
            ->assertViewHas(
                'citasAgenda',
                function ($citasAgenda) use ($datos) {
                    $prefijo =
                        $datos['medico']->id
                        .'|';

                    return ! $citasAgenda->has(
                        $prefijo.'10:00'
                    )
                        && ! $citasAgenda->has(
                            $prefijo.'10:15'
                        );
                }
            );

        $horarios = $this
            ->actingAs($datos['recepcion'])
            ->getJson(route(
                'citas.horarios-disponibles',
                [
                    'medico_id' => $datos['medico']->id,

                    'fecha' => $fecha,
                ]
            ))
            ->assertOk()
            ->json('horarios');

        $bloqueDiez =
            collect($horarios)
                ->firstWhere(
                    'hora',
                    '10:00'
                );

        $bloqueDiezQuince =
            collect($horarios)
                ->firstWhere(
                    'hora',
                    '10:15'
                );

        $this->assertTrue(
            $bloqueDiez['disponible']
        );

        $this->assertTrue(
            $bloqueDiezQuince['disponible']
        );

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->post(route('citas.store'), [
                'paciente_id' => $otroPaciente->id,

                'medico_id' => $datos['medico']->id,

                'fecha' => $fecha,
                'hora' => '10:00',
                'duracion_minutos' => 30,
                'modalidad' => 'presencial',
                'direccion_cita' => null,
                'motivo' => 'consulta_inicial',
                'notas' => 'Nueva cita en horario liberado.',

                'estado' => 'programada',
            ]);

        $respuesta->assertSessionHasNoErrors();

        $this->assertDatabaseHas('citas', [
            'id' => $citaCancelada->id,
            'estado' => 'cancelada',
        ]);

        $nuevaCitaExiste = Citas::query()
            ->where(
                'paciente_id',
                $otroPaciente->id
            )
            ->where(
                'medico_id',
                $datos['medico']->id
            )
            ->whereDate(
                'fecha',
                $fecha
            )
            ->where(
                'hora',
                '10:00'
            )
            ->where(
                'estado',
                'programada'
            )
            ->exists();

        $this->assertTrue(
            $nuevaCitaExiste
        );

        $this->assertDatabaseCount(
            'citas',
            2
        );
    }

    public function test_recepcion_visualiza_datos_de_cita_en_modal(): void
    {
        $datos = $this->escenario();

        $fecha = now()
            ->addDays(2)
            ->toDateString();

        $datos['paciente']->update([
            'telefono' => '5512345678',
            'alergias' => 'Dextrometorfano',
        ]);

        $cita = Citas::query()->create([
            'paciente_id' => $datos['paciente']->id,

            'medico_id' => $datos['medico']->id,

            'fecha' => $fecha,
            'hora' => '10:15',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_subsecuente',
            'notas' => 'Control de tratamiento.',
            'estado' => 'confirmada',

            'created_by' => $datos['recepcion']->id,
        ]);

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->get(route('dashboard', [
                'fecha' => $fecha,
            ]));

        $respuesta
            ->assertOk()
            ->assertSee(
                'id="modal-detalle-cita"',
                false
            )
            ->assertSee(
                'data-cita-id="'.$cita->id.'"',
                false
            )
            ->assertSeeText('Ficha del paciente')
            ->assertSeeText('Modificar cita')
            ->assertSeeText('Todas sus citas')
            ->assertSeeText('Detalle de la cita')
            ->assertSeeText(
                'Recordar cita por WhatsApp'
            )
            ->assertSee('Paciente Agenda')
            ->assertSee('Dextrometorfano')
            ->assertSee('Control de tratamiento.')
            ->assertSee('5512345678');
    }

    public function test_cita_puede_extenderse_hasta_las_nueve_de_la_noche(): void
    {
        $datos = $this->escenario();

        $fecha = now()
            ->addDays(5)
            ->toDateString();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->post(route('citas.store'), [
                'paciente_id' => $datos['paciente']->id,
                'medico_id' => $datos['medico']->id,
                'fecha' => $fecha,

                /*
             * Desde las 09:00 hasta las 21:00:
             * 12 horas equivalen a 720 minutos.
             */
                'hora' => '09:00',
                'duracion_minutos' => 720,

                'modalidad' => 'presencial',
                'direccion_cita' => null,
                'motivo' => 'consulta_inicial',
                'notas' => 'Cita extendida hasta el cierre.',
                'estado' => 'programada',
            ]);

        $respuesta->assertSessionHasNoErrors();

        $citaExtendidaExiste = Citas::query()
            ->where(
                'paciente_id',
                $datos['paciente']->id
            )
            ->where(
                'medico_id',
                $datos['medico']->id
            )
            ->whereDate('fecha', $fecha)
            ->where('hora', '09:00')
            ->where('duracion_minutos', 720)
            ->where('estado', 'programada')
            ->exists();

        $this->assertTrue(
            $citaExtendidaExiste
        );
    }

    public function test_cita_no_puede_terminar_despues_de_las_nueve_de_la_noche(): void
    {
        $datos = $this->escenario();

        $fecha = now()
            ->addDays(6)
            ->toDateString();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->from(route('dashboard'))
            ->post(route('citas.store'), [
                'paciente_id' => $datos['paciente']->id,
                'medico_id' => $datos['medico']->id,
                'fecha' => $fecha,

                /*
             * Inicia a las 20:45 y pretende durar
             * 30 minutos, por lo que terminaría
             * a las 21:15.
             */
                'hora' => '20:45',
                'duracion_minutos' => 30,

                'modalidad' => 'presencial',
                'direccion_cita' => null,
                'motivo' => 'consulta_inicial',
                'notas' => 'Esta cita debe ser rechazada.',
                'estado' => 'programada',
            ]);

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrorsIn(
                'crearCita',
                'hora'
            );

        $this->assertDatabaseCount(
            'citas',
            0
        );
    }

    public function test_cita_rechaza_duracion_fuera_de_intervalos_de_quince_minutos(): void
    {
        $datos = $this->escenario();

        $fecha = now()
            ->addDays(7)
            ->toDateString();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->from(route('dashboard'))
            ->post(route('citas.store'), [
                'paciente_id' => $datos['paciente']->id,
                'medico_id' => $datos['medico']->id,
                'fecha' => $fecha,
                'hora' => '10:00',

                /*
             * Veinte minutos no corresponden
             * a un intervalo válido de 15.
             */
                'duracion_minutos' => 20,

                'modalidad' => 'presencial',
                'direccion_cita' => null,
                'motivo' => 'consulta_inicial',
                'notas' => 'Duración inválida.',
                'estado' => 'programada',
            ]);

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrorsIn(
                'crearCita',
                'duracion_minutos'
            );

        $this->assertDatabaseCount(
            'citas',
            0
        );
    }

    public function test_edicion_de_cita_actualiza_los_bloques_ocupados(): void
    {
        $datos = $this->escenario();

        $fecha = now()
            ->addDays(8)
            ->toDateString();

        /*
     * Creamos inicialmente una cita de 09:00 a 21:00.
     */
        $respuestaCreacion = $this
            ->actingAs($datos['recepcion'])
            ->post(route('citas.store'), [
                'paciente_id' => $datos['paciente']->id,
                'medico_id' => $datos['medico']->id,
                'fecha' => $fecha,
                'hora' => '09:00',
                'duracion_minutos' => 720,
                'modalidad' => 'presencial',
                'direccion_cita' => null,
                'motivo' => 'consulta_inicial',
                'notas' => 'Cita originalmente extendida.',
                'estado' => 'programada',
            ]);

        $respuestaCreacion
            ->assertSessionHasNoErrors();

        $cita = Citas::query()->sole();

        /*
     * Reducimos la cita para que termine a las 15:00.
     */
        $respuestaEdicion = $this
            ->actingAs($datos['recepcion'])
            ->put(route('citas.update', $cita), [
                'paciente_id' => $datos['paciente']->id,
                'medico_id' => $datos['medico']->id,
                'fecha' => $fecha,
                'hora' => '09:00',
                'duracion_minutos' => 360,
                'modalidad' => 'presencial',
                'direccion_cita' => null,
                'motivo' => 'consulta_inicial',
                'notas' => 'Cita reducida hasta las 15:00.',
                'estado' => 'programada',
            ]);

        $respuestaEdicion
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'hora' => '09:00',
            'duracion_minutos' => 360,
        ]);

        $segundoPaciente = Pacientes::query()->create([
            'nombre' => 'Segundo',
            'apellido' => 'Paciente',
            'fecha_nacimiento' => '1992-02-02',
            'sexo' => 'femenino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        /*
     * Las 14:45 siguen dentro de la cita editada.
     */
        $respuestaTraslape = $this
            ->actingAs($datos['recepcion'])
            ->from(route('dashboard'))
            ->post(route('citas.store'), [
                'paciente_id' => $segundoPaciente->id,
                'medico_id' => $datos['medico']->id,
                'fecha' => $fecha,
                'hora' => '14:45',
                'duracion_minutos' => 30,
                'modalidad' => 'presencial',
                'direccion_cita' => null,
                'motivo' => 'consulta_inicial',
                'notas' => 'Debe rechazarse por traslape.',
                'estado' => 'programada',
            ]);

        $respuestaTraslape
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrorsIn(
                'crearCita',
                'hora'
            );

        $this->assertDatabaseCount('citas', 1);

        /*
     * A las 15:00 el medico vuelve a estar disponible.
     */
        $respuestaDisponible = $this
            ->actingAs($datos['recepcion'])
            ->post(route('citas.store'), [
                'paciente_id' => $segundoPaciente->id,
                'medico_id' => $datos['medico']->id,
                'fecha' => $fecha,
                'hora' => '15:00',
                'duracion_minutos' => 30,
                'modalidad' => 'presencial',
                'direccion_cita' => null,
                'motivo' => 'consulta_inicial',
                'notas' => 'Comienza al terminar la cita anterior.',
                'estado' => 'programada',
            ]);

        $respuestaDisponible
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('citas', 2);
    }

    /**
     * @return array<string, mixed>
     */
    private function escenario(): array
    {
        $recepcion = User::factory()->create([
            'name' => 'Recepción',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico =
            User::factory()->create([
                'name' => 'Médico de agenda',
                'role' => 'medico',
                'status' => true,
            ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Agenda',
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-AGENDA-001',
            'telefono' => '5512345678',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Agenda',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        return [
            'recepcion' => $recepcion,
            'usuario_medico' => $usuarioMedico,

            'medico' => $medico,
            'paciente' => $paciente,
        ];
    }
}
