<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\SignoVital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicoSignosVitalesAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_medico_asignado_registra_y_consulta_signos_vitales(): void
    {
        [$usuarioMedico, $medico] =
            $this->crearMedico(
                'Medico asignado',
                'CED-MEDICO-001'
            );

        $cita = $this->crearCita(
            $medico,
            $usuarioMedico
        );

        $this
            ->actingAs($usuarioMedico)
            ->get(route('citas.show', $cita))
            ->assertOk()
            ->assertSee(
                'Registrar signos vitales'
            )
            ->assertSee(
                route(
                    'signos-vitales.create',
                    $cita
                ),
                false
            );

        $this
            ->actingAs($usuarioMedico)
            ->get(
                route(
                    'signos-vitales.create',
                    $cita
                )
            )
            ->assertOk();

        $respuesta = $this
            ->actingAs($usuarioMedico)
            ->post(
                route(
                    'signos-vitales.store',
                    $cita
                ),
                $this->datosSignosVitales()
            );

        $signoVital = SignoVital::query()
            ->where('cita_id', $cita->id)
            ->firstOrFail();

        $this->assertTrue(
            $signoVital
                ->registradoPor
                ->is($usuarioMedico)
        );

        $respuesta
            ->assertRedirect(
                route(
                    'signos-vitales.show',
                    $signoVital
                )
            )
            ->assertSessionHas(
                'success',
                'Los signos vitales se registraron correctamente.'
            );

        $this->assertDatabaseHas(
            'signos_vitales',
            [
                'id' => $signoVital->id,
                'paciente_id' => $cita->paciente_id,
                'cita_id' => $cita->id,
                'enfermero_id' => $usuarioMedico->id,
                'peso' => 72.5,
                'estatura' => 175,
            ]
        );

        $this
            ->actingAs($usuarioMedico)
            ->get(
                route(
                    'signos-vitales.show',
                    $signoVital
                )
            )
            ->assertOk()
            ->assertSee('Medico asignado');

        $this
            ->actingAs($usuarioMedico)
            ->get(route('citas.show', $cita))
            ->assertOk()
            ->assertSee(
                'Ver valoración'
            )
            ->assertSee(
                route(
                    'signos-vitales.show',
                    $signoVital
                ),
                false
            )
            ->assertDontSee(
                'Registrar signos vitales'
            );
    }

    public function test_medico_ajeno_no_registra_signos_vitales(): void
    {
        [$usuarioAsignado, $medicoAsignado] =
            $this->crearMedico(
                'Medico asignado',
                'CED-MEDICO-002'
            );

        [$usuarioAjeno] = $this->crearMedico(
            'Medico ajeno',
            'CED-MEDICO-003'
        );

        $cita = $this->crearCita(
            $medicoAsignado,
            $usuarioAsignado
        );

        $this
            ->actingAs($usuarioAjeno)
            ->get(
                route(
                    'signos-vitales.create',
                    $cita
                )
            )
            ->assertForbidden();

        $this
            ->actingAs($usuarioAjeno)
            ->post(
                route(
                    'signos-vitales.store',
                    $cita
                ),
                $this->datosSignosVitales()
            )
            ->assertForbidden();

        $this->assertDatabaseMissing(
            'signos_vitales',
            [
                'cita_id' => $cita->id,
            ]
        );

        $signoVital = SignoVital::query()->create([
            'paciente_id' => $cita->paciente_id,
            'cita_id' => $cita->id,
            'enfermero_id' => $usuarioAsignado->id,
            ...$this->datosSignosVitales(),
        ]);

        $this
            ->actingAs($usuarioAjeno)
            ->get(
                route(
                    'signos-vitales.show',
                    $signoVital
                )
            )
            ->assertForbidden();
    }

    public function test_medico_sin_perfil_no_registra_signos_vitales(): void
    {
        [$usuarioAsignado, $medicoAsignado] =
            $this->crearMedico(
                'Medico asignado',
                'CED-MEDICO-004'
            );

        $medicoSinPerfil = User::factory()->create([
            'name' => 'Medico sin perfil',
            'role' => 'medico',
            'status' => true,
        ]);

        $cita = $this->crearCita(
            $medicoAsignado,
            $usuarioAsignado
        );

        $this
            ->actingAs($medicoSinPerfil)
            ->get(
                route(
                    'signos-vitales.create',
                    $cita
                )
            )
            ->assertForbidden();

        $this
            ->actingAs($medicoSinPerfil)
            ->post(
                route(
                    'signos-vitales.store',
                    $cita
                ),
                $this->datosSignosVitales()
            )
            ->assertForbidden();

        $this->assertDatabaseMissing(
            'signos_vitales',
            [
                'cita_id' => $cita->id,
            ]
        );
    }

    /**
     * @return array{0: User, 1: Medicos}
     */
    private function crearMedico(
        string $nombre,
        string $cedula
    ): array {
        $usuario = User::factory()->create([
            'name' => $nombre,
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuario->id,
            'nombre' => $nombre,
            'apellido_paterno' => 'Prueba',
            'apellido_materno' => 'Clinica',
            'especialidad' => 'Medicina general',
            'cedula' => $cedula,
            'telefono' => '5550000100',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        return [
            $usuario,
            $medico,
        ];
    }

    private function crearCita(
        Medicos $medico,
        User $creador
    ): Citas {
        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Signos vitales',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        return Citas::query()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_subsecuente',
            'notas' => 'Cita para registrar signos vitales.',
            'estado' => 'programada',
            'created_by' => $creador->id,
        ]);
    }

    /**
     * @return array<string, int|float|string>
     */
    private function datosSignosVitales(): array
    {
        return [
            'peso' => 72.5,
            'estatura' => 175,
            'temperatura' => 36.5,
            'presion_sistolica' => 120,
            'presion_diastolica' => 80,
            'frecuencia_cardiaca' => 75,
            'frecuencia_respiratoria' => 18,
            'saturacion_oxigeno' => 98,
            'glucosa' => 95,
            'observaciones' => 'Valoracion registrada por el medico.',
        ];
    }
}
