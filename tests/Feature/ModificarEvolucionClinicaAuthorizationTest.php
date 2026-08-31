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

class ModificarEvolucionClinicaAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_medico_asignado_puede_modificar_evolucion(): void
    {
        $datos = $this->crearEscenario();

        $respuesta = $this
            ->actingAs($datos['usuario_medico'])
            ->put(
                route(
                    'evoluciones.update',
                    $datos['evolucion']
                ),
                $this->contenidoActualizado()
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
                'id' => $datos['evolucion']->id,

                'evolucion_clinica' =>
                    'La evolución clínica fue actualizada.',

                'diagnostico' =>
                    'Diagnóstico actualizado.',

                'tratamiento' =>
                    'Tratamiento actualizado.',

                'plan_recomendaciones' =>
                    'Plan actualizado.',

                'indicaciones_enfermeria' =>
                    'Indicaciones actualizadas.',

                'observaciones' =>
                    'Observaciones actualizadas.',
            ]
        );
    }

    public function test_medico_ajeno_no_puede_modificar_evolucion(): void
    {
        $datos = $this->crearEscenario();

        $this
            ->actingAs(
                $datos['usuario_medico_ajeno']
            )
            ->put(
                route(
                    'evoluciones.update',
                    $datos['evolucion']
                ),
                $this->contenidoActualizado()
            )
            ->assertForbidden();

        $this->assertEvolucionSinCambios(
            $datos['evolucion']
        );
    }

    public function test_administracion_no_puede_modificar_evolucion(): void
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
            ->put(
                route(
                    'evoluciones.update',
                    $datos['evolucion']
                ),
                $this->contenidoActualizado()
            )
            ->assertForbidden();

        $this->assertEvolucionSinCambios(
            $datos['evolucion']
        );
    }

    public function test_recepcion_y_enfermeria_no_pueden_modificar_evolucion(): void
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
                ->put(
                    route(
                        'evoluciones.update',
                        $datos['evolucion']
                    ),
                    $this->contenidoActualizado()
                )
                ->assertForbidden();
        }

        $this->assertEvolucionSinCambios(
            $datos['evolucion']
        );
    }

    public function test_relaciones_y_auditoria_no_pueden_modificarse_desde_solicitud(): void
    {
        $datos = $this->crearEscenario();

        $contenidoManipulado = array_merge(
            $this->contenidoActualizado(),
            [
                'caso_clinico_id' => 999999,
                'cita_id' => 999999,
                'paciente_id' => 999999,
                'medico_id' => 999999,
                'fecha' => '1999-01-01',
                'created_by' => 999999,
            ]
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
                $contenidoManipulado
            )
            ->assertRedirect(
                route(
                    'citas.show',
                    $datos['cita']
                )
            );

        $datos['evolucion']->refresh();

        $this->assertSame(
            $datos['caso']->id,
            $datos['evolucion']->caso_clinico_id
        );

        $this->assertSame(
            $datos['cita']->id,
            $datos['evolucion']->cita_id
        );

        $this->assertSame(
            $datos['paciente']->id,
            $datos['evolucion']->paciente_id
        );

        $this->assertSame(
            $datos['medico']->id,
            $datos['evolucion']->medico_id
        );

        $this->assertSame(
            $datos['cita']
                ->fecha
                ->format('Y-m-d'),
            $datos['evolucion']
                ->fecha
                ->format('Y-m-d')
        );

        $this->assertSame(
            $datos['usuario_medico']->id,
            $datos['evolucion']->created_by
        );

        $this->assertSame(
            'La evolución clínica fue actualizada.',
            $datos['evolucion']->evolucion_clinica
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
            cedula: 'CED-MODIFICAR-1',
            telefono: '5550000401',
            apellido: 'Responsable'
        );

        $medicoAjeno = $this->crearMedico(
            usuario: $usuarioMedicoAjeno,
            cedula: 'CED-MODIFICAR-2',
            telefono: '5550000402',
            apellido: 'Ajeno'
        );

        $paciente =
            Pacientes::query()->create([
                'nombre' => 'Paciente',
                'apellido' => 'Modificación',
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
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'hora' => '10:00',
                'duracion_minutos' => 30,
                'modalidad' => 'presencial',
                'motivo' =>
                    'consulta_subsecuente',

                'notas' =>
                    'Cita para modificar evolución.',

                'estado' => 'programada',
                'created_by' =>
                    $usuarioMedico->id,
            ]);

        $caso =
            CasoClinico::query()->create([
                'paciente_id' => $paciente->id,
                'nombre' =>
                    'Caso clínico modificable',

                'descripcion_inicial' =>
                    'Caso para pruebas de modificación.',

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
                    'Evolución clínica original.',

                'diagnostico' =>
                    'Diagnóstico original.',

                'tratamiento' =>
                    'Tratamiento original.',

                'plan_recomendaciones' =>
                    'Plan original.',

                'indicaciones_enfermeria' =>
                    'Indicaciones originales.',

                'observaciones' =>
                    'Observaciones originales.',

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

    /**
     * @return array<string, string>
     */
    private function contenidoActualizado(): array
    {
        return [
            'evolucion_clinica' =>
                'La evolución clínica fue actualizada.',

            'diagnostico' =>
                'Diagnóstico actualizado.',

            'tratamiento' =>
                'Tratamiento actualizado.',

            'plan_recomendaciones' =>
                'Plan actualizado.',

            'indicaciones_enfermeria' =>
                'Indicaciones actualizadas.',

            'observaciones' =>
                'Observaciones actualizadas.',
        ];
    }

    private function assertEvolucionSinCambios(
        EvolucionClinica $evolucion
    ): void {
        $this->assertDatabaseHas(
            'evoluciones_clinicas',
            [
                'id' => $evolucion->id,

                'evolucion_clinica' =>
                    'Evolución clínica original.',

                'diagnostico' =>
                    'Diagnóstico original.',

                'tratamiento' =>
                    'Tratamiento original.',

                'plan_recomendaciones' =>
                    'Plan original.',

                'indicaciones_enfermeria' =>
                    'Indicaciones originales.',

                'observaciones' =>
                    'Observaciones originales.',
            ]
        );
    }
}