<?php

namespace Tests\Feature;

use App\Models\AgendaBloqueo;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaBloqueoTest extends TestCase
{
    use RefreshDatabase;

    public function test_recepcion_puede_bloquear_varias_horas_de_la_agenda(): void
    {
        $recepcion = User::factory()->create([
            'name' => 'Usuario recepcion',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'name' => 'Medico con bloqueo',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Medico',
            'apellido_paterno' => 'Agenda',
            'apellido_materno' => 'Bloqueada',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-BLOQUEO-001',
            'telefono' => '5512345678',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        $fecha = now()
            ->addDays(10)
            ->toDateString();

        $respuesta = $this
            ->actingAs($recepcion)
            ->from(route('dashboard'))
            ->post(route('agenda-bloqueos.store'), [
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'hora_inicio' => '09:00',
                'hora_fin' => '15:00',
                'motivo' => 'Procedimiento fuera de la clinica.',
            ]);

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $bloqueoExiste = AgendaBloqueo::query()
            ->where('medico_id', $medico->id)
            ->whereDate('fecha', $fecha)
            ->where('hora_inicio', '09:00')
            ->where('hora_fin', '15:00')
            ->where(
                'motivo',
                'Procedimiento fuera de la clinica.'
            )
            ->where('creado_por', $recepcion->id)
            ->exists();

        $this->assertTrue($bloqueoExiste);
    }

    public function test_cita_no_puede_crearse_sobre_un_bloqueo_de_agenda(): void
    {
        $recepcion = User::factory()->create([
            'name' => 'Usuario recepcion',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'name' => 'Medico bloqueado',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Medico',
            'apellido_paterno' => 'Con',
            'apellido_materno' => 'Bloqueo',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-BLOQUEO-002',
            'telefono' => '5512345679',
            'consultorio' => 'Consultorio 2',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Bloqueo',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $fecha = now()
            ->addDays(11)
            ->toDateString();

        /*
     * El médico no estará disponible
     * desde las 10:00 hasta las 13:00.
     */
        AgendaBloqueo::query()->create([
            'medico_id' => $medico->id,
            'creado_por' => $recepcion->id,
            'fecha' => $fecha,
            'hora_inicio' => '10:00',
            'hora_fin' => '13:00',
            'motivo' => 'Actividad externa.',
        ]);

        /*
     * La cita intenta comenzar dentro
     * del periodo bloqueado.
     */
        $respuesta = $this
            ->actingAs($recepcion)
            ->from(route('dashboard'))
            ->post(route('citas.store'), [
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'hora' => '11:00',
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

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_horarios_disponibles_identifica_bloques_de_agenda(): void
    {
        $recepcion = User::factory()->create([
            'name' => 'Usuario recepcion',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'name' => 'Medico con horario bloqueado',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Medico',
            'apellido_paterno' => 'Horario',
            'apellido_materno' => 'Bloqueado',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-BLOQUEO-003',
            'telefono' => '5512345680',
            'consultorio' => 'Consultorio 3',
            'status' => true,
        ]);

        $fecha = now()
            ->addDays(12)
            ->toDateString();

        AgendaBloqueo::query()->create([
            'medico_id' => $medico->id,
            'creado_por' => $recepcion->id,
            'fecha' => $fecha,
            'hora_inicio' => '10:00',
            'hora_fin' => '13:00',
            'motivo' => 'Actividad externa.',
        ]);

        $respuesta = $this
            ->actingAs($recepcion)
            ->getJson(route(
                'citas.horarios-disponibles',
                [
                    'medico_id' => $medico->id,
                    'fecha' => $fecha,
                ]
            ));

        $respuesta
            ->assertOk()

            /*
         * El inicio y el último intervalo contenido
         * en el bloqueo deben estar ocupados.
         */
            ->assertJsonFragment([
                'hora' => '10:00',
                'disponible' => false,
            ])
            ->assertJsonFragment([
                'hora' => '12:45',
                'disponible' => false,
            ])

            /*
         * El bloque termina a las 13:00,
         * por lo que esa hora vuelve a estar libre.
         */
            ->assertJsonFragment([
                'hora' => '13:00',
                'disponible' => true,
            ]);
    }

    public function test_bloqueo_no_puede_cruzarse_con_una_cita_existente(): void
    {
        $recepcion = User::factory()->create([
            'name' => 'Usuario recepcion',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'name' => 'Medico con cita',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Medico',
            'apellido_paterno' => 'Con',
            'apellido_materno' => 'Cita',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-BLOQUEO-004',
            'telefono' => '5512345681',
            'consultorio' => 'Consultorio 4',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Con cita',
            'fecha_nacimiento' => '1988-03-03',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $fecha = now()
            ->addDays(13)
            ->toDateString();

        /*
     * Creamos una cita de 10:00 a 11:00.
     */
        $respuestaCita = $this
            ->actingAs($recepcion)
            ->post(route('citas.store'), [
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'hora' => '10:00',
                'duracion_minutos' => 60,
                'modalidad' => 'presencial',
                'direccion_cita' => null,
                'motivo' => 'consulta_inicial',
                'notas' => 'Cita previamente registrada.',
                'estado' => 'programada',
            ]);

        $respuestaCita->assertSessionHasNoErrors();

        /*
     * El bloqueo intenta comenzar dentro de la cita.
     */
        $respuestaBloqueo = $this
            ->actingAs($recepcion)
            ->from(route('dashboard'))
            ->post(route('agenda-bloqueos.store'), [
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'hora_inicio' => '10:30',
                'hora_fin' => '12:00',
                'motivo' => 'Bloqueo que debe rechazarse.',
            ]);

        $respuestaBloqueo
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrorsIn(
                'bloquearAgenda',
                'hora_inicio'
            );

        $this->assertDatabaseCount(
            'agenda_bloqueos',
            0
        );
    }

    public function test_recepcion_puede_liberar_un_bloqueo_de_agenda(): void
    {
        $recepcion = User::factory()->create([
            'name' => 'Usuario recepcion',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'name' => 'Medico con bloqueo liberable',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Medico',
            'apellido_paterno' => 'Bloqueo',
            'apellido_materno' => 'Liberable',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-BLOQUEO-005',
            'telefono' => '5512345682',
            'consultorio' => 'Consultorio 5',
            'status' => true,
        ]);

        $bloqueo = AgendaBloqueo::query()->create([
            'medico_id' => $medico->id,
            'creado_por' => $recepcion->id,
            'fecha' => now()
                ->addDays(14)
                ->toDateString(),
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'motivo' => 'Bloqueo que sera liberado.',
        ]);

        $respuesta = $this
            ->actingAs($recepcion)
            ->from(route('dashboard'))
            ->delete(route(
                'agenda-bloqueos.destroy',
                $bloqueo
            ));

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        /*
     * El registro permanece en la base de datos,
     * pero deja de considerarse un bloqueo activo.
     */
        $this->assertSoftDeleted(
            'agenda_bloqueos',
            [
                'id' => $bloqueo->id,
            ]
        );

        $this->assertDatabaseCount(
            'agenda_bloqueos',
            1
        );
    }

    public function test_recepcion_puede_modificar_un_bloqueo_de_agenda(): void
    {
        $recepcion = User::factory()->create([
            'name' => 'Usuario recepcion',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'name' => 'Medico con bloqueo editable',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Medico',
            'apellido_paterno' => 'Bloqueo',
            'apellido_materno' => 'Editable',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-BLOQUEO-006',
            'telefono' => '5512345683',
            'consultorio' => 'Consultorio 6',
            'status' => true,
        ]);

        $fecha = now()
            ->addDays(15)
            ->toDateString();

        $bloqueo = AgendaBloqueo::query()->create([
            'medico_id' => $medico->id,
            'creado_por' => $recepcion->id,
            'fecha' => $fecha,
            'hora_inicio' => '15:00',
            'hora_fin' => '17:00',
            'motivo' => 'Horario original.',
        ]);

        /*
     * Extendemos el bloqueo una hora antes
     * y una hora después.
     */
        $respuesta = $this
            ->actingAs($recepcion)
            ->from(route('dashboard'))
            ->put(route(
                'agenda-bloqueos.update',
                $bloqueo
            ), [
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'hora_inicio' => '14:00',
                'hora_fin' => '18:00',
                'motivo' => 'Horario actualizado.',
            ]);

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $bloqueo->refresh();

        $this->assertSame(
            '14:00',
            substr($bloqueo->hora_inicio, 0, 5)
        );

        $this->assertSame(
            '18:00',
            substr($bloqueo->hora_fin, 0, 5)
        );

        $this->assertSame(
            'Horario actualizado.',
            $bloqueo->motivo
        );
    }

    public function test_edicion_de_bloqueo_rechaza_traslape_con_cita(): void
    {
        $recepcion = User::factory()->create([
            'name' => 'Usuario recepcion',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'name' => 'Medico con cita protegida',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Medico',
            'apellido_paterno' => 'Edicion',
            'apellido_materno' => 'Protegida',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-BLOQUEO-007',
            'telefono' => '5512345684',
            'consultorio' => 'Consultorio 7',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Protegido',
            'fecha_nacimiento' => '1985-04-04',
            'sexo' => 'femenino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $fecha = now()
            ->addDays(16)
            ->toDateString();

        /*
     * La cita ocupa el horario de 16:00 a 17:00.
     */
        $respuestaCita = $this
            ->actingAs($recepcion)
            ->post(route('citas.store'), [
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'hora' => '16:00',
                'duracion_minutos' => 60,
                'modalidad' => 'presencial',
                'direccion_cita' => null,
                'motivo' => 'consulta_inicial',
                'notas' => 'Cita protegida contra bloqueos.',
                'estado' => 'programada',
            ]);

        $respuestaCita->assertSessionHasNoErrors();

        /*
     * El bloqueo original termina antes de la cita.
     */
        $bloqueo = AgendaBloqueo::query()->create([
            'medico_id' => $medico->id,
            'creado_por' => $recepcion->id,
            'fecha' => $fecha,
            'hora_inicio' => '14:00',
            'hora_fin' => '16:00',
            'motivo' => 'Bloqueo original.',
        ]);

        /*
     * Se intenta extender hasta las 16:30,
     * invadiendo treinta minutos de la cita.
     */
        $respuesta = $this
            ->actingAs($recepcion)
            ->from(route('dashboard'))
            ->put(route(
                'agenda-bloqueos.update',
                $bloqueo
            ), [
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'hora_inicio' => '14:00',
                'hora_fin' => '16:30',
                'motivo' => 'Intento de extension invalida.',
            ]);

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrorsIn(
                'bloquearAgenda',
                'hora_inicio'
            );

        $bloqueo->refresh();

        $this->assertSame(
            '16:00',
            substr($bloqueo->hora_fin, 0, 5)
        );

        $this->assertSame(
            'Bloqueo original.',
            $bloqueo->motivo
        );
    }

    public function test_horarios_pueden_ignorar_el_bloqueo_que_se_esta_editando(): void
    {
        $recepcion = User::factory()->create([
            'name' => 'Usuario recepcion',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'name' => 'Medico con bloqueo editable',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Medico',
            'apellido_paterno' => 'Ignorar',
            'apellido_materno' => 'Bloqueo',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-BLOQUEO-008',
            'telefono' => '5512345685',
            'consultorio' => 'Consultorio 8',
            'status' => true,
        ]);

        $fecha = now()
            ->addDays(17)
            ->toDateString();

        $bloqueo = AgendaBloqueo::query()->create([
            'medico_id' => $medico->id,
            'creado_por' => $recepcion->id,
            'fecha' => $fecha,
            'hora_inicio' => '14:00',
            'hora_fin' => '16:00',
            'motivo' => 'Bloqueo que se esta editando.',
        ]);

        $respuesta = $this
            ->actingAs($recepcion)
            ->getJson(route(
                'citas.horarios-disponibles',
                [
                    'medico_id' => $medico->id,
                    'fecha' => $fecha,
                    'ignorar_bloqueo' => $bloqueo->id,
                ]
            ));

        $respuesta
            ->assertOk()
            ->assertJsonPath(
                'horarios.20.hora',
                '14:00'
            )
            ->assertJsonPath(
                'horarios.20.disponible',
                true
            )
            ->assertJsonPath(
                'horarios.27.hora',
                '15:45'
            )
            ->assertJsonPath(
                'horarios.27.disponible',
                true
            );
    }
}
