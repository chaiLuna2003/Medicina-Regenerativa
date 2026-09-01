<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\HistoriaClinica;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitasShowHistoriaClinicaTest extends TestCase
{
    use RefreshDatabase;

    private User $administrador;
    private User $usuarioMedico;
    private User $recepcion;
    private Pacientes $paciente;
    private Citas $cita;

    protected function setUp(): void
    {
        parent::setUp();

        $this->administrador = User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);

        $this->usuarioMedico = User::factory()->create([
            'role' => 'medico',
            'status' => true,
        ]);

        $this->recepcion = User::factory()->create([
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $this->usuarioMedico->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Autorizado',
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-HISTORIA-1',
            'telefono' => '5550000601',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        $this->paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Historia',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $this->cita = Citas::query()->create([
            'paciente_id' => $this->paciente->id,
            'medico_id' => $medico->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_inicial',
            'estado' => 'programada',
            'created_by' => $this->administrador->id,
        ]);

        HistoriaClinica::query()->create([
            'paciente_id' => $this->paciente->id,
            'patologia_base' => 'Patología clínica protegida',
            'padecimiento_actual' => 'Padecimiento visible autorizado',
            'tratamientos_actuales' => 'Tratamiento de prueba',
            'prioridad_analisis_medico' => 'Prioridad de prueba',
        ]);
    }

    public function test_administracion_consulta_historia_en_cita(): void
    {
        $this
            ->actingAs($this->administrador)
            ->get(route('citas.show', $this->cita))
            ->assertOk()
            ->assertSee('Historia clínica')
            ->assertSee('Patología clínica protegida')
            ->assertSee('Padecimiento visible autorizado');
    }

    public function test_recepcion_no_consulta_historia_en_cita(): void
    {
        $this
            ->actingAs($this->recepcion)
            ->get(route('citas.show', $this->cita))
            ->assertOk()
            ->assertDontSee('Información clínica de la cita')
            ->assertDontSee('Patología clínica protegida')
            ->assertDontSee('Padecimiento visible autorizado');
    }

    public function test_administracion_no_modifica_historia_clinica(): void
    {
        $this
            ->actingAs($this->administrador)
            ->put(
                route(
                    'pacientes.historia-clinica.update',
                    $this->paciente
                ),
                [
                    'patologia_base' =>
                        'Alteración no autorizada',
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseHas('historias_clinicas', [
            'paciente_id' => $this->paciente->id,
            'patologia_base' =>
                'Patología clínica protegida',
        ]);
    }

    public function test_medico_asignado_modifica_historia_clinica(): void
    {
        $this
            ->actingAs($this->usuarioMedico)
            ->put(
                route(
                    'pacientes.historia-clinica.update',
                    $this->paciente
                ),
                [
                    'patologia_base' =>
                        'Patología actualizada por el médico',
                    'padecimiento_actual' =>
                        'Padecimiento actualizado',
                ]
            )
            ->assertRedirect(
                route('pacientes.show', $this->paciente)
            );

        $this->assertDatabaseHas('historias_clinicas', [
            'paciente_id' => $this->paciente->id,
            'patologia_base' =>
                'Patología actualizada por el médico',
            'padecimiento_actual' =>
                'Padecimiento actualizado',
        ]);
    }
}