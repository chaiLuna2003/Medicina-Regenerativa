<?php

namespace Tests\Feature;

use App\Models\CasoClinico;
use App\Models\Citas;
use App\Models\EvolucionAparato;
use App\Models\EvolucionClinica;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CerrarCasoClinicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_medico_que_abrio_el_caso_puede_cerrarlo(): void
    {
        $datos = $this->crearEscenario();

        $respuesta = $this
            ->actingAs($datos['usuario_medico'])
            ->from(
                route(
                    'citas.show',
                    $datos['cita']
                )
            )
            ->patch(
                route(
                    'casos-clinicos.cerrar',
                    $datos['caso']
                ),
                [
                    'motivo_cierre' =>
                    'Alta médica por cumplimiento de objetivos.',

                    'confirmacion_cierre' => '1',
                ]
            );

        $respuesta
            ->assertRedirect(
                route(
                    'citas.show',
                    $datos['cita']
                )
            )
            ->assertSessionHas('success');

        $datos['caso']->refresh();

        $this->assertSame(
            CasoClinico::ESTADO_CERRADO,
            $datos['caso']->estado
        );

        $this->assertSame(
            $datos['usuario_medico']->id,
            $datos['caso']->cerrado_por
        );

        $this->assertSame(
            'Alta médica por cumplimiento de objetivos.',
            $datos['caso']->motivo_cierre
        );

        $this->assertNotNull(
            $datos['caso']->fecha_cierre
        );
    }

    public function test_cierre_requiere_motivo_y_confirmacion(): void
    {
        $datos = $this->crearEscenario();

        $respuesta = $this
            ->actingAs($datos['usuario_medico'])
            ->from(
                route(
                    'citas.show',
                    $datos['cita']
                )
            )
            ->patch(
                route(
                    'casos-clinicos.cerrar',
                    $datos['caso']
                ),
                [
                    'motivo_cierre' => '   ',
                ]
            );

        $respuesta->assertSessionHasErrors(
            [
                'motivo_cierre',
                'confirmacion_cierre',
            ],
            null,
            'cierreCasoClinico'
        );

        $this->assertDatabaseHas(
            'casos_clinicos',
            [
                'id' => $datos['caso']->id,
                'estado' =>
                CasoClinico::ESTADO_ACTIVO,
                'fecha_cierre' => null,
                'cerrado_por' => null,
                'motivo_cierre' => null,
            ]
        );
    }

    public function test_medico_ajeno_no_puede_cerrar_el_caso(): void
    {
        $datos = $this->crearEscenario();

        $this
            ->actingAs(
                $datos['usuario_medico_ajeno']
            )
            ->patch(
                route(
                    'casos-clinicos.cerrar',
                    $datos['caso']
                ),
                [
                    'motivo_cierre' =>
                    'Intento de cierre por otro médico.',

                    'confirmacion_cierre' => '1',
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'casos_clinicos',
            [
                'id' => $datos['caso']->id,
                'estado' =>
                CasoClinico::ESTADO_ACTIVO,
            ]
        );
    }

    public function test_administracion_no_puede_cerrar_el_caso(): void
    {
        $datos = $this->crearEscenario();

        $administrador =
            User::factory()->create([
                'name' => 'Administración',
                'role' => 'admin',
                'status' => true,
            ]);

        $this
            ->actingAs($administrador)
            ->patch(
                route(
                    'casos-clinicos.cerrar',
                    $datos['caso']
                ),
                [
                    'motivo_cierre' =>
                    'Administración intenta cerrar el caso.',

                    'confirmacion_cierre' => '1',
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'casos_clinicos',
            [
                'id' => $datos['caso']->id,
                'estado' =>
                CasoClinico::ESTADO_ACTIVO,
            ]
        );
    }

    public function test_recepcion_y_enfermeria_no_pueden_cerrar_el_caso(): void
    {
        $datos = $this->crearEscenario();

        foreach (
            [
                'recepcionista',
                'enfermero',
            ]
            as $rol
        ) {
            $usuario =
                User::factory()->create([
                    'name' => ucfirst($rol),
                    'role' => $rol,
                    'status' => true,
                ]);

            $this
                ->actingAs($usuario)
                ->patch(
                    route(
                        'casos-clinicos.cerrar',
                        $datos['caso']
                    ),
                    [
                        'motivo_cierre' =>
                        'Intento de cierre sin autorización.',

                        'confirmacion_cierre' => '1',
                    ]
                )
                ->assertForbidden();
        }

        $this->assertDatabaseHas(
            'casos_clinicos',
            [
                'id' => $datos['caso']->id,
                'estado' =>
                CasoClinico::ESTADO_ACTIVO,
            ]
        );
    }

    public function test_caso_cerrado_no_puede_cerrarse_nuevamente(): void
    {
        $datos = $this->crearEscenario();

        $this->marcarCasoComoCerrado(
            $datos
        );

        $this
            ->actingAs(
                $datos['usuario_medico']
            )
            ->patch(
                route(
                    'casos-clinicos.cerrar',
                    $datos['caso']
                ),
                [
                    'motivo_cierre' =>
                    'Segundo intento de cierre del caso.',

                    'confirmacion_cierre' => '1',
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'casos_clinicos',
            [
                'id' => $datos['caso']->id,

                'motivo_cierre' =>
                'Cierre previo para prueba automatizada.',
            ]
        );
    }

    public function test_caso_cerrado_no_permite_editar_evolucion(): void
    {
        $datos = $this->crearEscenario();

        $this->marcarCasoComoCerrado(
            $datos
        );

        $this
            ->actingAs(
                $datos['usuario_medico']
            )
            ->put(
                route(
                    'evoluciones.update',
                    $datos['evolucion']
                ),
                [
                    'evolucion_clinica' =>
                    'Este cambio no debe guardarse.',
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'evoluciones_clinicas',
            [
                'id' => $datos['evolucion']->id,

                'evolucion_clinica' =>
                'Evolución clínica inicial de prueba.',
            ]
        );
    }

    public function test_caso_cerrado_no_permite_editar_aparatos(): void
    {
        $datos = $this->crearEscenario();

        $this->marcarCasoComoCerrado(
            $datos
        );

        $aparatos = [];

        foreach (
            EvolucionAparato::APARATOS
            as $clave => $configuracion
        ) {
            $aparatos[$clave] = [
                'estado' =>
                EvolucionAparato::ESTADO_NORMAL,

                'observaciones' => null,
            ];
        }

        $this
            ->actingAs(
                $datos['usuario_medico']
            )
            ->put(
                route(
                    'evoluciones.aparatos.update',
                    $datos['evolucion']
                ),
                [
                    'aparatos' => $aparatos,
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseCount(
            'evolucion_aparatos',
            0
        );
    }

    public function test_caso_cerrado_no_admite_nueva_evolucion(): void
    {
        $datos = $this->crearEscenario();

        $this->marcarCasoComoCerrado(
            $datos
        );

        $citaPosterior =
            Citas::query()->create([
                'paciente_id' =>
                $datos['paciente']->id,

                'medico_id' =>
                $datos['medico']->id,

                'fecha' => now()
                    ->addDays(2)
                    ->toDateString(),

                'hora' => '11:00',

                'duracion_minutos' => 30,

                'modalidad' => 'presencial',

                'motivo' =>
                'consulta_subsecuente',

                'notas' =>
                'Cita posterior de prueba.',

                'estado' => 'programada',

                'created_by' =>
                $datos['usuario_medico']->id,
            ]);

        $this
            ->actingAs(
                $datos['usuario_medico']
            )
            ->post(
                route(
                    'citas.casos-clinicos.evoluciones.store',
                    [
                        'cita' =>
                        $citaPosterior,

                        'casoClinico' =>
                        $datos['caso'],
                    ]
                ),
                [
                    'evolucion_clinica' =>
                    'Seguimiento que no debe crearse.',
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseCount(
            'evoluciones_clinicas',
            1
        );
    }

    /**
     * Crea un escenario clínico completo.
     *
     * @return array<string, mixed>
     */
    private function crearEscenario(): array
    {
        $usuarioMedico =
            User::factory()->create([
                'name' =>
                'Médico responsable',

                'role' => 'medico',

                'status' => true,
            ]);

        $usuarioMedicoAjeno =
            User::factory()->create([
                'name' => 'Médico ajeno',

                'role' => 'medico',

                'status' => true,
            ]);

        $medico =
            Medicos::query()->create([
                'user_id' =>
                $usuarioMedico->id,

                'nombre' => 'Médico',

                'apellido_paterno' =>
                'Responsable',

                'apellido_materno' =>
                'Prueba',

                'especialidad' =>
                'Medicina general',

                'cedula' =>
                'CED-CIERRE-1',

                'telefono' =>
                '5550000101',

                'consultorio' =>
                'Consultorio 1',

                'status' => true,
            ]);

        $medicoAjeno =
            Medicos::query()->create([
                'user_id' =>
                $usuarioMedicoAjeno->id,

                'nombre' => 'Médico',

                'apellido_paterno' =>
                'Ajeno',

                'apellido_materno' =>
                'Prueba',

                'especialidad' =>
                'Medicina general',

                'cedula' =>
                'CED-CIERRE-2',

                'telefono' =>
                '5550000102',

                'consultorio' =>
                'Consultorio 2',

                'status' => true,
            ]);

        $paciente =
            Pacientes::query()->create([
                'nombre' => 'Paciente',

                'apellido' => 'Cierre',

                'fecha_nacimiento' =>
                '1990-01-01',

                'sexo' => 'masculino',

                'categoria' =>
                'sin_categoria',

                'status' => true,
            ]);

        $fecha = now()
            ->addDay()
            ->toDateString();

        $cita =
            Citas::query()->create([
                'paciente_id' =>
                $paciente->id,

                'medico_id' =>
                $medico->id,

                'fecha' => $fecha,

                'hora' => '10:00',

                'duracion_minutos' => 30,

                'modalidad' =>
                'presencial',

                'motivo' =>
                'consulta_inicial',

                'notas' =>
                'Cita para probar cierre de caso.',

                'estado' => 'programada',

                'created_by' =>
                $usuarioMedico->id,
            ]);

        $caso =
            CasoClinico::query()->create([
                'paciente_id' =>
                $paciente->id,

                'nombre' =>
                'Caso clínico de prueba',

                'descripcion_inicial' =>
                'Seguimiento creado para pruebas automatizadas.',

                'fecha_inicio' => $fecha,

                'estado' =>
                CasoClinico::ESTADO_ACTIVO,

                'created_by' =>
                $usuarioMedico->id,
            ]);

        $evolucion =
            EvolucionClinica::query()->create([
                'caso_clinico_id' =>
                $caso->id,

                'cita_id' =>
                $cita->id,

                'paciente_id' =>
                $paciente->id,

                'medico_id' =>
                $medico->id,

                'fecha' => $fecha,

                'evolucion_clinica' =>
                'Evolución clínica inicial de prueba.',

                'diagnostico' =>
                'Diagnóstico de prueba.',

                'created_by' =>
                $usuarioMedico->id,
            ]);

        return [
            'usuario_medico' =>
            $usuarioMedico,

            'usuario_medico_ajeno' =>
            $usuarioMedicoAjeno,

            'medico' =>
            $medico,

            'medico_ajeno' =>
            $medicoAjeno,

            'paciente' =>
            $paciente,

            'cita' =>
            $cita,

            'caso' =>
            $caso,

            'evolucion' =>
            $evolucion,
        ];
    }

    public function test_cierre_entre_autorizacion_y_guardado_impide_editar_evolucion(): void
    {
        $datos = $this->crearEscenario();

        $contenidoOriginal = $datos['evolucion']->evolucion_clinica;
        $cierreSimulado = false;

        \Illuminate\Support\Facades\Gate::after(
            function ($usuario, $habilidad, $resultado, $argumentos) use (
                $datos,
                &$cierreSimulado
            ): void {
                if (
                    $cierreSimulado
                    || $habilidad !== 'update'
                    || $resultado !== true
                    || ! (($argumentos[0] ?? null) instanceof EvolucionClinica)
                    || (int) $argumentos[0]->id
                    !== (int) $datos['evolucion']->id
                ) {
                    return;
                }

                /*
             * La autorización inicial ya aprobó la operación.
             * Simulamos que el cierre ocurre antes del guardado.
             */
                $cierreSimulado = true;

                $this->marcarCasoComoCerrado($datos);
            }
        );

        $respuesta = $this
            ->actingAs($datos['usuario_medico'])
            ->put(
                route('evoluciones.update', $datos['evolucion']),
                [
                    'evolucion_clinica' =>
                    'Este cambio no debe guardarse después del cierre.',
                ]
            );

        $respuesta->assertForbidden();

        $this->assertTrue(
            $cierreSimulado,
            'La prueba debe cerrar el caso después de autorizar inicialmente.'
        );

        $this->assertTrue(
            $datos['caso']->fresh()->estaCerrado()
        );

        $this->assertSame(
            $contenidoOriginal,
            $datos['evolucion']->fresh()->evolucion_clinica
        );
    }

    public function test_cierre_entre_autorizacion_y_guardado_impide_editar_aparatos(): void
    {
        $datos = $this->crearEscenario();

        $claves = array_keys(EvolucionAparato::APARATOS);
        $primeraClave = $claves[0];

        $valoracionOriginal = $datos['evolucion']
            ->aparatos()
            ->create([
                'aparato' => $primeraClave,
                'estado' => EvolucionAparato::ESTADO_NORMAL,
                'observaciones' => 'Observación original que debe conservarse.',
            ]);

        $atributosOriginales = $valoracionOriginal
    ->fresh()
    ->getAttributes();

        $aparatos = [];

        foreach ($claves as $clave) {
            $aparatos[$clave] = [
                'estado' => EvolucionAparato::ESTADO_NORMAL,
                'observaciones' => 'Este cambio no debe guardarse.',
            ];
        }

        $cierreSimulado = false;

        \Illuminate\Support\Facades\Gate::after(
            function ($usuario, $habilidad, $resultado, $argumentos) use (
                $datos,
                &$cierreSimulado
            ): void {
                if (
                    $cierreSimulado
                    || $habilidad !== 'gestionarAparatos'
                    || $resultado !== true
                    || ! (($argumentos[0] ?? null) instanceof EvolucionClinica)
                    || (int) $argumentos[0]->id
                    !== (int) $datos['evolucion']->id
                ) {
                    return;
                }

                $cierreSimulado = true;

                $this->marcarCasoComoCerrado($datos);
            }
        );

        $respuesta = $this
            ->actingAs($datos['usuario_medico'])
            ->put(
                route('evoluciones.aparatos.update', $datos['evolucion']),
                [
                    'aparatos' => $aparatos,
                ]
            );

        $respuesta->assertForbidden();

        $this->assertTrue(
            $cierreSimulado,
            'La prueba debe cerrar el caso después de autorizar inicialmente.'
        );

        $this->assertTrue(
            $datos['caso']->fresh()->estaCerrado()
        );

        $this->assertSame(
            $atributosOriginales,
            $valoracionOriginal->fresh()->getAttributes()
        );

        $this->assertSame(
            1,
            $datos['evolucion']->aparatos()->count()
        );
    }

    /**
     * Marca el caso como cerrado sin pasar por el endpoint.
     *
     * @param array<string, mixed> $datos
     */
    private function marcarCasoComoCerrado(
        array $datos
    ): void {
        $datos['caso']->update([
            'estado' =>
            CasoClinico::ESTADO_CERRADO,

            'fecha_cierre' => now(),

            'cerrado_por' =>
            $datos['usuario_medico']->id,

            'motivo_cierre' =>
            'Cierre previo para prueba automatizada.',
        ]);
    }
}
