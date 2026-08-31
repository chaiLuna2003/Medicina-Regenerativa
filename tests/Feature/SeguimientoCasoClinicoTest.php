<?php

namespace Tests\Feature;

use App\Models\CasoClinico;
use App\Models\Citas;
use App\Models\EvolucionClinica;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\SignoVital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeguimientoCasoClinicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_seguimiento_reune_dos_citas_y_excluye_una_cita_ajena(): void
    {
        $usuarioMedico = User::factory()->create([
            'name' => 'Médico responsable',
            'role' => 'medico',
            'status' => true,
        ]);

        $usuarioEnfermeria = User::factory()->create([
            'name' => 'Enfermería',
            'role' => 'enfermero',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Responsable',
            'apellido_materno' => 'Seguimiento',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-SEGUIMIENTO-1',
            'telefono' => '5550000201',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Seguimiento',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $citaInicial = $this->crearCita(
            paciente: $paciente,
            medico: $medico,
            creadoPor: $usuarioMedico,
            fecha: '2026-08-01',
            hora: '10:00',
            notas: 'Primera cita del caso.'
        );

        $citaSeguimiento = $this->crearCita(
            paciente: $paciente,
            medico: $medico,
            creadoPor: $usuarioMedico,
            fecha: '2026-08-15',
            hora: '10:00',
            notas: 'Segunda cita del caso.',
            motivo: 'consulta_subsecuente'
        );

        /*
         * Esta cita pertenece al mismo paciente y médico,
         * pero no será vinculada con el caso clínico.
         */
        $citaAjenaAlCaso = $this->crearCita(
            paciente: $paciente,
            medico: $medico,
            creadoPor: $usuarioMedico,
            fecha: '2026-08-20',
            hora: '10:00',
            notas: 'Cita que no pertenece al caso.',
            motivo: 'consulta_subsecuente'
        );

        $caso = CasoClinico::query()->create([
            'paciente_id' => $paciente->id,
            'nombre' => 'Fractura de tobillo',
            'descripcion_inicial' =>
                'Seguimiento clínico de fractura de tobillo.',
            'fecha_inicio' => $citaInicial->fecha,
            'estado' => CasoClinico::ESTADO_ACTIVO,
            'created_by' => $usuarioMedico->id,
        ]);

        $primeraEvolucion =
            EvolucionClinica::query()->create([
                'caso_clinico_id' => $caso->id,
                'cita_id' => $citaInicial->id,
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => $citaInicial->fecha,
                'evolucion_clinica' =>
                    'Paciente presenta dolor e inflamación.',
                'diagnostico' =>
                    'Fractura de tobillo en seguimiento.',
                'created_by' => $usuarioMedico->id,
            ]);

        SignoVital::query()->create([
            'paciente_id' => $paciente->id,
            'cita_id' => $citaInicial->id,
            'enfermero_id' => $usuarioEnfermeria->id,
            'peso' => 72,
            'estatura' => 170,
            'temperatura' => 36.5,
            'presion_sistolica' => 125,
            'presion_diastolica' => 82,
            'frecuencia_cardiaca' => 78,
            'frecuencia_respiratoria' => 18,
            'saturacion_oxigeno' => 97,
            'glucosa' => 95,
        ]);

        SignoVital::query()->create([
            'paciente_id' => $paciente->id,
            'cita_id' => $citaSeguimiento->id,
            'enfermero_id' => $usuarioEnfermeria->id,
            'peso' => 70,
            'estatura' => 170,
            'temperatura' => 36.4,
            'presion_sistolica' => 120,
            'presion_diastolica' => 80,
            'frecuencia_cardiaca' => 74,
            'frecuencia_respiratoria' => 17,
            'saturacion_oxigeno' => 98,
            'glucosa' => 90,
        ]);

        /*
         * Este valor no debe aparecer en las gráficas
         * porque la cita no tiene evolución dentro del caso.
         */
        SignoVital::query()->create([
            'paciente_id' => $paciente->id,
            'cita_id' => $citaAjenaAlCaso->id,
            'enfermero_id' => $usuarioEnfermeria->id,
            'peso' => 99,
            'estatura' => 170,
            'temperatura' => 39,
            'presion_sistolica' => 180,
            'presion_diastolica' => 120,
            'frecuencia_cardiaca' => 130,
            'frecuencia_respiratoria' => 30,
            'saturacion_oxigeno' => 80,
            'glucosa' => 250,
        ]);

        /*
         * Registrar la segunda evolución mediante
         * el endpoint real de la aplicación.
         */
        $respuestaRegistro = $this
            ->actingAs($usuarioMedico)
            ->post(
                route(
                    'citas.casos-clinicos.evoluciones.store',
                    [
                        'cita' => $citaSeguimiento,
                        'casoClinico' => $caso,
                    ]
                ),
                [
                    'evolucion_clinica' =>
                        'Disminuyó el dolor y mejoró la movilidad.',
                    'diagnostico' =>
                        'Evolución favorable de la fractura.',
                    'tratamiento' =>
                        'Continuar inmovilización y reposo.',
                    'plan_recomendaciones' =>
                        'Nueva valoración en dos semanas.',
                    'indicaciones_enfermeria' =>
                        'Vigilar dolor e inflamación.',
                    'observaciones' =>
                        'Paciente coopera con el tratamiento.',
                ]
            );

        $respuestaRegistro
            ->assertRedirect(
                route('citas.show', $citaSeguimiento)
            )
            ->assertSessionHas('success');

        $segundaEvolucion =
            EvolucionClinica::query()
                ->where(
                    'cita_id',
                    $citaSeguimiento->id
                )
                ->firstOrFail();

        /*
         * El caso debe contener exactamente dos evoluciones.
         */
        $this->assertDatabaseCount(
            'evoluciones_clinicas',
            2
        );

        $this->assertDatabaseHas(
            'evoluciones_clinicas',
            [
                'id' => $segundaEvolucion->id,
                'caso_clinico_id' => $caso->id,
                'cita_id' => $citaSeguimiento->id,
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => '2026-08-15 00:00:00',
                'created_by' => $usuarioMedico->id,
            ]
        );

        $this->assertDatabaseMissing(
            'evoluciones_clinicas',
            [
                'cita_id' => $citaAjenaAlCaso->id,
            ]
        );

        /*
         * Consultar la segunda cita para comprobar
         * el historial y los datos enviados a ApexCharts.
         */
        $respuestaDetalle = $this
            ->actingAs($usuarioMedico)
            ->get(
                route(
                    'citas.show',
                    $citaSeguimiento
                )
            );

        $respuestaDetalle
            ->assertOk()
            ->assertViewHas(
                'datosGraficasCaso',
                function (
                    array $graficas
                ) use (
                    $citaInicial,
                    $citaSeguimiento
                ): bool {
                    return $graficas['categorias'] === [
                        '01/08/2026 · Cita #'
                            . $citaInicial->id,

                        '15/08/2026 · Cita #'
                            . $citaSeguimiento->id,
                    ]
                        && $graficas['peso'] === [
                            72.0,
                            70.0,
                        ]
                        && $graficas[
                            'presion_sistolica'
                        ] === [
                            125,
                            120,
                        ]
                        && $graficas[
                            'presion_diastolica'
                        ] === [
                            82,
                            80,
                        ]
                        && ! in_array(
                            99.0,
                            $graficas['peso'],
                            true
                        );
                }
            );

        /*
         * El historial debe mostrarse del seguimiento
         * más reciente al más antiguo.
         */
        $respuestaDetalle->assertViewHas(
            'cita',
            function (
                Citas $cita
            ) use (
                $segundaEvolucion,
                $primeraEvolucion
            ): bool {
                $evoluciones =
                    $cita
                        ->evolucionClinica
                        ?->casoClinico
                        ?->evoluciones;

                if ($evoluciones === null) {
                    return false;
                }

                return $evoluciones
                    ->pluck('id')
                    ->values()
                    ->all() === [
                        $segundaEvolucion->id,
                        $primeraEvolucion->id,
                    ];
            }
        );
    }

    private function crearCita(
        Pacientes $paciente,
        Medicos $medico,
        User $creadoPor,
        string $fecha,
        string $hora,
        string $notas,
        string $motivo = 'consulta_inicial'
    ): Citas {
        return Citas::query()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => $fecha,
            'hora' => $hora,
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => $motivo,
            'notas' => $notas,
            'estado' => 'programada',
            'created_by' => $creadoPor->id,
        ]);
    }
}