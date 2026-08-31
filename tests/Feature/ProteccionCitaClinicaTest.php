<?php

namespace Tests\Feature;

use App\Models\CasoClinico;
use App\Models\Citas;
use App\Models\EvolucionClinica;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProteccionCitaClinicaTest extends TestCase
{
    use RefreshDatabase;

    public function test_cita_con_evolucion_no_puede_eliminarse(): void
    {
        $datos = $this->crearEscenario();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->from(
                route(
                    'citas.show',
                    $datos['cita']
                )
            )
            ->delete(
                route(
                    'citas.destroy',
                    $datos['cita']
                )
            );

        $respuesta
            ->assertRedirect(
                route(
                    'citas.show',
                    $datos['cita']
                )
            )
            ->assertSessionHas('error');

        $this->assertDatabaseHas(
            'citas',
            [
                'id' => $datos['cita']->id,
            ]
        );

        $this->assertDatabaseHas(
            'evoluciones_clinicas',
            [
                'id' => $datos['evolucion']->id,
                'cita_id' => $datos['cita']->id,
            ]
        );
    }

    public function test_cita_con_evolucion_no_puede_reasignarse_ni_cambiar_fecha_u_hora(): void
    {
        $datos = $this->crearEscenario();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->from(
                route(
                    'citas.edit',
                    $datos['cita']
                )
            )
            ->put(
                route(
                    'citas.update',
                    $datos['cita']
                ),
                [
                    'paciente_id' =>
                        $datos['paciente_ajeno']->id,

                    'medico_id' =>
                        $datos['medico_ajeno']->id,

                    'fecha' =>
                        now()
                            ->addDays(10)
                            ->toDateString(),

                    'hora' => '15:30',
                    'duracion_minutos' => 60,
                    'modalidad' => 'presencial',
                    'direccion_cita' => null,
                    'motivo' =>
                        'consulta_subsecuente',

                    'notas' =>
                        'Intento de reasignación.',

                    'estado' => 'programada',
                ]
            );

        $respuesta->assertSessionHasErrors(
            ['cita']
        );

        $datos['cita']->refresh();

        $this->assertSame(
            $datos['paciente']->id,
            $datos['cita']->paciente_id
        );

        $this->assertSame(
            $datos['medico']->id,
            $datos['cita']->medico_id
        );

        $this->assertSame(
            $datos['fecha_original'],
            $datos['cita']
                ->fecha
                ->format('Y-m-d')
        );

        $this->assertSame(
            '10:00',
            Carbon::parse(
                $datos['cita']->hora
            )->format('H:i')
        );

        $this->assertSame(
            30,
            $datos['cita']->duracion_minutos
        );
    }

    public function test_cita_con_evolucion_no_puede_cancelarse(): void
    {
        $datos = $this->crearEscenario();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->from(
                route(
                    'citas.edit',
                    $datos['cita']
                )
            )
            ->put(
                route(
                    'citas.update',
                    $datos['cita']
                ),
                [
                    'paciente_id' =>
                        $datos['paciente']->id,

                    'medico_id' =>
                        $datos['medico']->id,

                    'fecha' =>
                        $datos['fecha_original'],

                    'hora' => '10:00',
                    'duracion_minutos' => 30,
                    'modalidad' => 'presencial',
                    'direccion_cita' => null,
                    'motivo' =>
                        'consulta_subsecuente',

                    'notas' =>
                        'Intento de cancelación.',

                    'estado' => 'cancelada',
                ]
            );

        $respuesta->assertSessionHasErrors(
            ['estado']
        );

        $datos['cita']->refresh();

        $this->assertSame(
            'programada',
            $datos['cita']->estado
        );

        $this->assertDatabaseHas(
            'evoluciones_clinicas',
            [
                'id' => $datos['evolucion']->id,
                'cita_id' => $datos['cita']->id,
            ]
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function crearEscenario(): array
    {
        $recepcion =
            User::factory()->create([
                'name' => 'Recepción',
                'role' => 'recepcionista',
                'status' => true,
            ]);

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
            cedula: 'CED-PROTECCION-1',
            telefono: '5550000801',
            apellido: 'Responsable'
        );

        $medicoAjeno = $this->crearMedico(
            usuario: $usuarioMedicoAjeno,
            cedula: 'CED-PROTECCION-2',
            telefono: '5550000802',
            apellido: 'Ajeno'
        );

        $paciente =
            Pacientes::query()->create([
                'nombre' => 'Paciente',
                'apellido' => 'Protegido',
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

        $cita =
            Citas::query()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'hora' => '10:00',
                'duracion_minutos' => 30,
                'modalidad' => 'presencial',
                'motivo' =>
                    'consulta_subsecuente',

                'notas' =>
                    'Cita con información clínica.',

                'estado' => 'programada',
                'created_by' => $recepcion->id,
            ]);

        $caso =
            CasoClinico::query()->create([
                'paciente_id' => $paciente->id,
                'nombre' => 'Caso clínico protegido',

                'descripcion_inicial' =>
                    'Caso que protege la cita asociada.',

                'fecha_inicio' => $fecha,
                'estado' =>
                    CasoClinico::ESTADO_ACTIVO,

                'created_by' =>
                    $usuarioMedico->id,
            ]);

        $evolucion =
            EvolucionClinica::query()->create([
                'caso_clinico_id' => $caso->id,
                'cita_id' => $cita->id,
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => $fecha,

                'evolucion_clinica' =>
                    'Evolución que protege la cita.',

                'created_by' =>
                    $usuarioMedico->id,
            ]);

        return [
            'recepcion' => $recepcion,
            'usuario_medico' =>
                $usuarioMedico,

            'usuario_medico_ajeno' =>
                $usuarioMedicoAjeno,

            'medico' => $medico,
            'medico_ajeno' => $medicoAjeno,
            'paciente' => $paciente,
            'paciente_ajeno' => $pacienteAjeno,
            'fecha_original' => $fecha,
            'cita' => $cita,
            'caso' => $caso,
            'evolucion' => $evolucion,
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
}