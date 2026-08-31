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

class ValidacionEvolucionAparatosTest extends TestCase
{
    use RefreshDatabase;

    public function test_aparato_fuera_del_catalogo_es_rechazado(): void
    {
        $datos = $this->crearEscenario();

        $aparatos = $this->aparatosValidos();

        $aparatos['aparato_inventado'] = [
            'estado' =>
                EvolucionAparato::ESTADO_NORMAL,

            'observaciones' => null,
        ];

        $respuesta = $this
            ->actingAs($datos['usuario_medico'])
            ->from(
                route(
                    'citas.show',
                    $datos['cita']
                )
            )
            ->put(
                route(
                    'evoluciones.aparatos.update',
                    $datos['evolucion']
                ),
                [
                    'aparatos' => $aparatos,
                ]
            );

        $respuesta->assertSessionHasErrors(
            ['aparatos'],
            null,
            'aparatosEvolucion'
        );

        $this->assertDatabaseCount(
            'evolucion_aparatos',
            0
        );
    }

    public function test_estado_de_aparato_invalido_es_rechazado(): void
    {
        $datos = $this->crearEscenario();

        $aparatos = $this->aparatosValidos();

        $aparatos['cerebro']['estado'] =
            'estado_inventado';

        $respuesta = $this
            ->actingAs($datos['usuario_medico'])
            ->from(
                route(
                    'citas.show',
                    $datos['cita']
                )
            )
            ->put(
                route(
                    'evoluciones.aparatos.update',
                    $datos['evolucion']
                ),
                [
                    'aparatos' => $aparatos,
                ]
            );

        $respuesta->assertSessionHasErrors(
            ['aparatos.cerebro.estado'],
            null,
            'aparatosEvolucion'
        );

        $this->assertDatabaseCount(
            'evolucion_aparatos',
            0
        );
    }

    public function test_requiere_atencion_exige_observacion(): void
    {
        $datos = $this->crearEscenario();

        $aparatos = $this->aparatosValidos();

        $aparatos['corazon'] = [
            'estado' =>
                EvolucionAparato::ESTADO_REQUIERE_ATENCION,

            /*
             * Los espacios deben convertirse en null
             * durante la normalización del request.
             */
            'observaciones' => '   ',
        ];

        $respuesta = $this
            ->actingAs($datos['usuario_medico'])
            ->from(
                route(
                    'citas.show',
                    $datos['cita']
                )
            )
            ->put(
                route(
                    'evoluciones.aparatos.update',
                    $datos['evolucion']
                ),
                [
                    'aparatos' => $aparatos,
                ]
            );

        $respuesta->assertSessionHasErrors(
            ['aparatos.corazon.observaciones'],
            null,
            'aparatosEvolucion'
        );

        $this->assertDatabaseCount(
            'evolucion_aparatos',
            0
        );
    }

    public function test_estado_critico_exige_observacion(): void
    {
        $datos = $this->crearEscenario();

        $aparatos = $this->aparatosValidos();

        $aparatos['sistema_respiratorio'] = [
            'estado' =>
                EvolucionAparato::ESTADO_CRITICO,

            'observaciones' => '',
        ];

        $respuesta = $this
            ->actingAs($datos['usuario_medico'])
            ->from(
                route(
                    'citas.show',
                    $datos['cita']
                )
            )
            ->put(
                route(
                    'evoluciones.aparatos.update',
                    $datos['evolucion']
                ),
                [
                    'aparatos' => $aparatos,
                ]
            );

        $respuesta->assertSessionHasErrors(
            [
                'aparatos.sistema_respiratorio'
                    . '.observaciones',
            ],
            null,
            'aparatosEvolucion'
        );

        $this->assertDatabaseCount(
            'evolucion_aparatos',
            0
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

        $medico =
            Medicos::query()->create([
                'user_id' => $usuarioMedico->id,
                'nombre' => 'Médico',
                'apellido_paterno' => 'Aparatos',
                'apellido_materno' => 'Prueba',
                'especialidad' => 'Medicina general',
                'cedula' => 'CED-APARATOS-1',
                'telefono' => '5550000701',
                'consultorio' => 'Consultorio 1',
                'status' => true,
            ]);

        $paciente =
            Pacientes::query()->create([
                'nombre' => 'Paciente',
                'apellido' => 'Aparatos',
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
                    'Cita para validar aparatos.',

                'estado' => 'programada',
                'created_by' => $usuarioMedico->id,
            ]);

        $caso =
            CasoClinico::query()->create([
                'paciente_id' => $paciente->id,
                'nombre' =>
                    'Caso para validar aparatos',

                'descripcion_inicial' =>
                    'Caso clínico para pruebas automatizadas.',

                'fecha_inicio' => $fecha,
                'estado' =>
                    CasoClinico::ESTADO_ACTIVO,

                'created_by' => $usuarioMedico->id,
            ]);

        $evolucion =
            EvolucionClinica::query()->create([
                'caso_clinico_id' => $caso->id,
                'cita_id' => $cita->id,
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => $fecha,

                'evolucion_clinica' =>
                    'Evolución para validar aparatos.',

                'created_by' => $usuarioMedico->id,
            ]);

        return [
            'usuario_medico' => $usuarioMedico,
            'medico' => $medico,
            'paciente' => $paciente,
            'cita' => $cita,
            'caso' => $caso,
            'evolucion' => $evolucion,
        ];
    }

    /**
     * @return array<string, array{
     *     estado: string,
     *     observaciones: null
     * }>
     */
    private function aparatosValidos(): array
    {
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

        return $aparatos;
    }
}