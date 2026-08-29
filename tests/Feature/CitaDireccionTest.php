<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitaDireccionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cita_externa_requiere_direccion(): void
    {
        $datos = $this->escenario();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->post(route('citas.store'), [
                ...$this->datosCita($datos),
                'modalidad' => 'fuera_instalaciones',
                'direccion_cita' => '',
            ]);

        $respuesta->assertSessionHasErrors('direccion_cita');

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_recepcion_guarda_cita_externa_con_direccion(): void
    {
        $datos = $this->escenario();

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->post(route('citas.store'), [
                ...$this->datosCita($datos),
                'modalidad' => 'fuera_instalaciones',
                'direccion_cita' =>
                    'Avenida Reforma 100, Ciudad de México',
            ]);

        $cita = Citas::query()->firstOrFail();

        $respuesta
            ->assertRedirect(route('citas.show', $cita))
            ->assertSessionHas('success');

        $this->assertSame(
            'Avenida Reforma 100, Ciudad de México',
            $cita->direccion_cita
        );
    }

    public function test_cita_normal_descarta_direccion_enviada(): void
    {
        $datos = $this->escenario();

        $this
            ->actingAs($datos['recepcion'])
            ->post(route('citas.store'), [
                ...$this->datosCita($datos),
                'modalidad' => 'presencial',
                'direccion_cita' =>
                    'Esta dirección no debe conservarse',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('citas', [
            'modalidad' => 'presencial',
            'direccion_cita' => null,
        ]);
    }

    public function test_cambiar_modalidad_limpia_direccion_anterior(): void
    {
        $datos = $this->escenario();
        $cita = $this->crearCitaExterna($datos);

        $respuesta = $this
            ->actingAs($datos['recepcion'])
            ->put(route('citas.update', $cita), [
                ...$this->datosCita($datos),
                'modalidad' => 'telefonica',
                'direccion_cita' => $cita->direccion_cita,
            ]);

        $respuesta->assertSessionHasNoErrors();

        $cita->refresh();

        $this->assertSame(
            'telefonica',
            $cita->modalidad
        );

        $this->assertNull(
            $cita->direccion_cita
        );
    }

    public function test_detalle_muestra_ubicacion_y_respeta_acciones_por_rol(): void
    {
        $datos = $this->escenario();
        $cita = $this->crearCitaExterna($datos);

        $this
            ->actingAs($datos['recepcion'])
            ->get(route('citas.show', $cita))
            ->assertOk()
            ->assertSeeText(
                'Avenida Reforma 100, Ciudad de México'
            )
            ->assertSeeText('Abrir en Google Maps')
            ->assertSeeText('Abrir en Waze')
            ->assertSeeText(
                'Enviar dirección al médico'
            );

        $this
            ->actingAs($datos['usuario_medico'])
            ->get(route('citas.show', $cita))
            ->assertOk()
            ->assertSeeText(
                'Avenida Reforma 100, Ciudad de México'
            )
            ->assertSeeText('Abrir en Google Maps')
            ->assertSeeText('Abrir en Waze')
            ->assertDontSeeText(
                'Enviar dirección al médico'
            );
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

        $usuarioMedico = User::factory()->create([
            'name' => 'Médico de prueba',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Amanda',
            'apellido_paterno' => 'Medina',
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-DIRECCION-1',
            'telefono' => '5512345678',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Dirección',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'femenino',
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

    /**
     * @param array<string, mixed> $datos
     *
     * @return array<string, mixed>
     */
    private function datosCita(array $datos): array
    {
        return [
            'paciente_id' => $datos['paciente']->id,
            'medico_id' => $datos['medico']->id,
            'fecha' => now()
                ->addDays(3)
                ->toDateString(),
            'hora' => '09:00',
            'duracion_minutos' => 30,
            'motivo' => 'consulta_inicial',
            'notas' => 'Prueba de cita externa.',
            'estado' => 'programada',
        ];
    }

    /**
     * @param array<string, mixed> $datos
     */
    private function crearCitaExterna(
        array $datos
    ): Citas {
        return Citas::query()->create([
            ...$this->datosCita($datos),
            'modalidad' => 'fuera_instalaciones',
            'direccion_cita' =>
                'Avenida Reforma 100, Ciudad de México',
            'created_by' => $datos['recepcion']->id,
        ]);
    }
}