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

class CrearEvolucionClinicaAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_medico_asignado_puede_crear_evolucion(): void
    {
        $datos = $this->crearEscenario();

        $respuesta = $this
            ->actingAs($datos['usuario_medico'])
            ->post(
                $this->rutaCrear(
                    $datos['cita'],
                    $datos['caso']
                ),
                $this->contenidoClinico()
            );

        $respuesta
            ->assertRedirect(
                route(
                    'citas.show',
                    $datos['cita']
                )
            )
            ->assertSessionHas('success');

        $this->assertDatabaseHas(
            'evoluciones_clinicas',
            [
                'caso_clinico_id' =>
                    $datos['caso']->id,

                'cita_id' =>
                    $datos['cita']->id,

                'paciente_id' =>
                    $datos['paciente']->id,

                'medico_id' =>
                    $datos['medico']->id,

                'fecha' =>
                    $datos['cita']
                        ->fecha
                        ->format('Y-m-d')
                        . ' 00:00:00',

                'evolucion_clinica' =>
                    'El paciente presenta una evolución favorable.',

                'created_by' =>
                    $datos['usuario_medico']->id,
            ]
        );
    }

    public function test_medico_ajeno_no_puede_crear_evolucion(): void
    {
        $datos = $this->crearEscenario();

        $this
            ->actingAs(
                $datos['usuario_medico_ajeno']
            )
            ->post(
                $this->rutaCrear(
                    $datos['cita'],
                    $datos['caso']
                ),
                $this->contenidoClinico()
            )
            ->assertForbidden();

        $this->assertDatabaseCount(
            'evoluciones_clinicas',
            0
        );
    }

    public function test_administracion_no_puede_crear_evolucion(): void
    {
        $datos = $this->crearEscenario();

        $administracion =
            User::factory()->create([
                'name' => 'Administración',
                'role' => 'admin',
                'status' => true,
            ]);

        $this
            ->actingAs($administracion)
            ->post(
                $this->rutaCrear(
                    $datos['cita'],
                    $datos['caso']
                ),
                $this->contenidoClinico()
            )
            ->assertForbidden();

        $this->assertDatabaseCount(
            'evoluciones_clinicas',
            0
        );
    }

  public function test_recepcion_y_enfermeria_no_pueden_crear_evolucion(): void
{
    $datos = $this->crearEscenario();

    foreach (
        ['recepcionista', 'enfermero']
        as $rol
    ) {
        $usuario = User::factory()->create([
            'name' => ucfirst($rol),
            'role' => $rol,
            'status' => true,
        ]);

        $this
            ->actingAs($usuario)
            ->post(
                $this->rutaCrear(
                    $datos['cita'],
                    $datos['caso']
                ),
                $this->contenidoClinico()
            )
            ->assertForbidden();
    }

    $this->assertDatabaseCount(
        'evoluciones_clinicas',
        0
    );
}

    public function test_caso_de_otro_paciente_es_rechazado(): void
    {
        $datos = $this->crearEscenario();

        $citaOtroPaciente =
            $this->crearCita(
                paciente:
                    $datos['paciente_ajeno'],

                medico:
                    $datos['medico'],

                creadoPor:
                    $datos['usuario_medico'],

                fecha:
                    now()
                        ->addDays(3)
                        ->toDateString(),

                hora: '12:00'
            );

        $this
            ->actingAs(
                $datos['usuario_medico']
            )
            ->post(
                $this->rutaCrear(
                    $citaOtroPaciente,
                    $datos['caso']
                ),
                $this->contenidoClinico()
            )
            ->assertForbidden();

        $this->assertDatabaseCount(
            'evoluciones_clinicas',
            0
        );
    }

    public function test_segunda_evolucion_en_la_misma_cita_es_rechazada(): void
    {
        $datos = $this->crearEscenario();

        EvolucionClinica::query()->create([
            'caso_clinico_id' =>
                $datos['caso']->id,

            'cita_id' =>
                $datos['cita']->id,

            'paciente_id' =>
                $datos['paciente']->id,

            'medico_id' =>
                $datos['medico']->id,

            'fecha' =>
                $datos['cita']->fecha,

            'evolucion_clinica' =>
                'Primera evolución registrada.',

            'created_by' =>
                $datos['usuario_medico']->id,
        ]);

        $this
            ->actingAs(
                $datos['usuario_medico']
            )
            ->post(
                $this->rutaCrear(
                    $datos['cita'],
                    $datos['caso']
                ),
                $this->contenidoClinico()
            )
            ->assertForbidden();

        $this->assertDatabaseCount(
            'evoluciones_clinicas',
            1
        );

        $this->assertDatabaseMissing(
            'evoluciones_clinicas',
            [
                'evolucion_clinica' =>
                    'El paciente presenta una evolución favorable.',
            ]
        );
    }

    public function test_cita_cancelada_no_admite_evolucion(): void
    {
        $datos = $this->crearEscenario();

        $datos['cita']->update([
            'estado' => 'cancelada',
        ]);

        $this
            ->actingAs(
                $datos['usuario_medico']
            )
            ->post(
                $this->rutaCrear(
                    $datos['cita'],
                    $datos['caso']
                ),
                $this->contenidoClinico()
            )
            ->assertForbidden();

        $this->assertDatabaseCount(
            'evoluciones_clinicas',
            0
        );
    }

    public function test_paciente_medico_fecha_y_creador_proceden_del_servidor(): void
    {
        $datos = $this->crearEscenario();

        /*
         * Estos valores falsos simulan una manipulación
         * manual de la solicitud desde el navegador.
         */
        $contenido = array_merge(
            $this->contenidoClinico(),
            [
                'caso_clinico_id' => 999999,
                'cita_id' => 999999,
                'paciente_id' =>
                    $datos['paciente_ajeno']->id,

                'medico_id' =>
                    $datos['medico_ajeno']->id,

                'fecha' => '1999-01-01',
                'created_by' =>
                    $datos[
                        'usuario_medico_ajeno'
                    ]->id,
            ]
        );

        $this
            ->actingAs(
                $datos['usuario_medico']
            )
            ->post(
                $this->rutaCrear(
                    $datos['cita'],
                    $datos['caso']
                ),
                $contenido
            )
            ->assertRedirect(
                route(
                    'citas.show',
                    $datos['cita']
                )
            );

        $evolucion =
            EvolucionClinica::query()
                ->sole();

        $this->assertSame(
            $datos['caso']->id,
            $evolucion->caso_clinico_id
        );

        $this->assertSame(
            $datos['cita']->id,
            $evolucion->cita_id
        );

        $this->assertSame(
            $datos['paciente']->id,
            $evolucion->paciente_id
        );

        $this->assertSame(
            $datos['medico']->id,
            $evolucion->medico_id
        );

        $this->assertSame(
            $datos['cita']
                ->fecha
                ->format('Y-m-d'),
            $evolucion
                ->fecha
                ->format('Y-m-d')
        );

        $this->assertSame(
            $datos['usuario_medico']->id,
            $evolucion->created_by
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function crearEscenario(): array
    {
        $usuarioMedico =
            User::factory()->create([
                'name' => 'Médico responsable',
                'role' => 'medico',
                'status' => true,
            ]);

        $usuarioMedicoAjeno =
            User::factory()->create([
                'name' => 'Médico ajeno',
                'role' => 'medico',
                'status' => true,
            ]);

        $medico = $this->crearMedico(
            usuario: $usuarioMedico,
            cedula: 'CED-EVOLUCION-1',
            telefono: '5550000301',
            apellido: 'Responsable'
        );

        $medicoAjeno = $this->crearMedico(
            usuario: $usuarioMedicoAjeno,
            cedula: 'CED-EVOLUCION-2',
            telefono: '5550000302',
            apellido: 'Ajeno'
        );

        $paciente =
            Pacientes::query()->create([
                'nombre' => 'Paciente',
                'apellido' => 'Principal',
                'fecha_nacimiento' =>
                    '1990-01-01',

                'sexo' => 'masculino',
                'categoria' =>
                    'sin_categoria',

                'status' => true,
            ]);

        $pacienteAjeno =
            Pacientes::query()->create([
                'nombre' => 'Paciente',
                'apellido' => 'Ajeno',
                'fecha_nacimiento' =>
                    '1985-05-10',

                'sexo' => 'femenino',
                'categoria' =>
                    'sin_categoria',

                'status' => true,
            ]);

        $fecha = now()
            ->addDays(2)
            ->toDateString();

        $cita = $this->crearCita(
            paciente: $paciente,
            medico: $medico,
            creadoPor: $usuarioMedico,
            fecha: $fecha,
            hora: '10:00'
        );

        $caso =
            CasoClinico::query()->create([
                'paciente_id' =>
                    $paciente->id,

                'nombre' =>
                    'Caso clínico activo',

                'descripcion_inicial' =>
                    'Caso utilizado para pruebas de autorización.',

                'fecha_inicio' =>
                    now()
                        ->subDay()
                        ->toDateString(),

                'estado' =>
                    CasoClinico::ESTADO_ACTIVO,

                'created_by' =>
                    $usuarioMedico->id,
            ]);

        return [
            'usuario_medico' =>
                $usuarioMedico,

            'usuario_medico_ajeno' =>
                $usuarioMedicoAjeno,

            'medico' => $medico,
            'medico_ajeno' => $medicoAjeno,
            'paciente' => $paciente,
            'paciente_ajeno' => $pacienteAjeno,
            'cita' => $cita,
            'caso' => $caso,
        ];
    }

    private function crearMedico(
        User $usuario,
        string $cedula,
        string $telefono,
        string $apellido
    ): Medicos {
        return Medicos::query()->create([
            'user_id' => $usuario->id,
            'nombre' => 'Médico',
            'apellido_paterno' => $apellido,
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => $cedula,
            'telefono' => $telefono,
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);
    }

    private function crearCita(
        Pacientes $paciente,
        Medicos $medico,
        User $creadoPor,
        string $fecha,
        string $hora
    ): Citas {
        return Citas::query()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => $fecha,
            'hora' => $hora,
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_subsecuente',
            'notas' =>
                'Cita para prueba de evolución.',

            'estado' => 'programada',
            'created_by' => $creadoPor->id,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function contenidoClinico(): array
    {
        return [
            'evolucion_clinica' =>
                'El paciente presenta una evolución favorable.',

            'diagnostico' =>
                'Diagnóstico clínico de seguimiento.',

            'tratamiento' =>
                'Continuar con el tratamiento indicado.',

            'plan_recomendaciones' =>
                'Realizar nueva valoración posteriormente.',

            'indicaciones_enfermeria' =>
                'Vigilar signos vitales durante la consulta.',

            'observaciones' =>
                'Sin complicaciones adicionales.',
        ];
    }

    private function rutaCrear(
        Citas $cita,
        CasoClinico $caso
    ): string {
        return route(
            'citas.casos-clinicos.evoluciones.store',
            [
                'cita' => $cita,
                'casoClinico' => $caso,
            ]
        );
    }
}