<?php

namespace Tests\Feature;

use App\Models\CasoClinico;
use App\Models\Citas;
use App\Models\EvolucionClinica;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class TransaccionCasoClinicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_fallo_en_primera_evolucion_no_deja_caso_huerfano(): void
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
                'apellido_paterno' => 'Transacción',
                'apellido_materno' => 'Prueba',
                'especialidad' => 'Medicina general',
                'cedula' => 'CED-TRANSACCION-1',
                'telefono' => '5550000601',
                'consultorio' => 'Consultorio 1',
                'status' => true,
            ]);

        $paciente =
            Pacientes::query()->create([
                'nombre' => 'Paciente',
                'apellido' => 'Transacción',
                'fecha_nacimiento' => '1990-01-01',
                'sexo' => 'masculino',
                'categoria' => 'sin_categoria',
                'status' => true,
            ]);

        $cita =
            Citas::query()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,

                'fecha' => now()
                    ->addDay()
                    ->toDateString(),

                'hora' => '10:00',
                'duracion_minutos' => 30,
                'modalidad' => 'presencial',
                'motivo' => 'consulta_inicial',

                'notas' =>
                    'Cita para probar la transacción.',

                'estado' => 'programada',
                'created_by' => $usuarioMedico->id,
            ]);

        /*
         * Simula una falla de base de datos justo cuando
         * se intenta crear la primera evolución.
         *
         * Para este momento el controlador ya creó el caso,
         * pero ambos registros están dentro de la misma
         * transacción y deben revertirse juntos.
         */
        EvolucionClinica::creating(
            function (): void {
                throw new RuntimeException(
                    'Fallo simulado al crear evolución.'
                );
            }
        );

        $this->withoutExceptionHandling();

        $excepcionCapturada = null;

        try {
            $this
                ->actingAs($usuarioMedico)
                ->post(
                    route(
                        'citas.casos-clinicos.store',
                        $cita
                    ),
                    [
                        'nombre' =>
                            'Caso que debe revertirse',

                        'descripcion_inicial' =>
                            'Este caso no debe permanecer guardado.',

                        'evolucion_clinica' =>
                            'Primera evolución que fallará.',

                        'diagnostico' =>
                            'Diagnóstico para prueba.',

                        'tratamiento' =>
                            'Tratamiento para prueba.',

                        'plan_recomendaciones' =>
                            'Plan para prueba.',

                        'indicaciones_enfermeria' =>
                            'Indicaciones para prueba.',

                        'observaciones' =>
                            'Observaciones para prueba.',
                    ]
                );
        } catch (RuntimeException $excepcion) {
            $excepcionCapturada = $excepcion;
        }

        $this->assertNotNull(
            $excepcionCapturada
        );

        $this->assertSame(
            'Fallo simulado al crear evolución.',
            $excepcionCapturada?->getMessage()
        );

        /*
         * La transacción debe revertir tanto el caso
         * como la evolución fallida.
         */
        $this->assertDatabaseCount(
            'casos_clinicos',
            0
        );

        $this->assertDatabaseCount(
            'evoluciones_clinicas',
            0
        );

        /*
         * La cita permanece intacta y sin evolución.
         */
        $this->assertDatabaseHas(
            'citas',
            [
                'id' => $cita->id,
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
            ]
        );

        $this->assertFalse(
            $cita
                ->evolucionClinica()
                ->exists()
        );
    }
}