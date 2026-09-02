<?php

namespace Tests\Feature;

use App\Models\AntecedenteGinecoobstetrico;
use App\Models\Citas;
use App\Models\HabitoAlimenticio;
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
            'patologia_base' =>
            'Patología clínica protegida',
            'padecimiento_actual' =>
            'Padecimiento visible autorizado',
            'tratamientos_actuales' =>
            'Tratamiento de prueba',
            'prioridad_analisis_medico' =>
            'Prioridad de prueba',
        ]);
    }

    public function test_administracion_consulta_historia_en_ficha(): void
    {
        $this
            ->actingAs($this->administrador)
            ->get(route('pacientes.show', $this->paciente))
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

    public function test_medico_consulta_habitos_alimenticios_en_ficha(): void
    {
        $historiaClinica = $this->paciente
            ->historiaClinica()
            ->firstOrFail();

        HabitoAlimenticio::query()->create([
            'historia_clinica_id' => $historiaClinica->id,

            'comidas' => [
                'desayuno' => true,
                'comida' => true,
                'cena' => false,
            ],

            'alimentos' => [
                'frutas' => 'Diario',
                'agua_natural' => 'Dos litros al día',
            ],
        ]);

        $this
            ->actingAs($this->usuarioMedico)
            ->get(route('pacientes.show', $this->paciente))
            ->assertOk()
            ->assertSee('Hábitos alimenticios')
            ->assertSee('Desayuno')
            ->assertSee('Comida')
            ->assertSee('Frutas')
            ->assertSee('Diario')
            ->assertSee('Agua natural')
            ->assertSee('Dos litros al día');
    }

    public function test_medico_ve_estado_vacio_de_habitos_en_ficha(): void
    {
        $this
            ->actingAs($this->usuarioMedico)
            ->get(route('pacientes.show', $this->paciente))
            ->assertOk()
            ->assertSee(
                'Sin hábitos alimenticios registrados'
            );
    }

    public function test_medico_consulta_ginecoobstetricos_de_paciente_femenina(): void
    {
        $this->paciente->update([
            'sexo' => 'femenino',
        ]);

        $historiaClinica = $this->paciente
            ->historiaClinica()
            ->firstOrFail();

        AntecedenteGinecoobstetrico::query()->create([
            'historia_clinica_id' => $historiaClinica->id,
            'edad_menarca' => 12,
            'ritmo_menstrual' => '28 x 5, regular',
            'gestas' => 2,
            'partos' => 1,
            'cesareas' => 1,
            'abortos' => 0,
            'embarazo_actual' => false,

            'fecha_ultimo_papanicolaou' =>
            '2026-06-15',

            'resultado_papanicolaou' =>
            'Resultado preventivo normal',

            'observaciones' =>
            'Seguimiento ginecoobstétrico anual',
        ]);

        $this
            ->actingAs($this->usuarioMedico)
            ->get(route('pacientes.show', $this->paciente))
            ->assertOk()
            ->assertSee('Antecedentes ginecoobstétricos')
            ->assertSee('28 x 5, regular')
            ->assertSee('Gestas')
            ->assertSee('Partos')
            ->assertSee('Cesáreas')
            ->assertSee(
                'Seguimiento ginecoobstétrico anual'
            );
    }

    public function test_paciente_femenina_muestra_estado_vacio_ginecoobstetrico(): void
    {
        $this->paciente->update([
            'sexo' => 'femenino',
        ]);

        $this
            ->actingAs($this->usuarioMedico)
            ->get(route('pacientes.show', $this->paciente))
            ->assertOk()
            ->assertSee('Antecedentes ginecoobstétricos')
            ->assertSee(
                'Sin antecedentes ginecoobstétricos'
            );
    }

    public function test_paciente_masculino_no_muestra_seccion_ginecoobstetrica(): void
    {
        $this
            ->actingAs($this->usuarioMedico)
            ->get(route('pacientes.show', $this->paciente))
            ->assertOk()
            ->assertDontSee(
                'Historia menstrual, obstétrica y estudios preventivos'
            )
            ->assertDontSee(
                'Sin antecedentes ginecoobstétricos'
            );
    }

    public function test_administracion_consulta_ficha_sin_formularios_clinicos(): void
    {
        $this->paciente->update([
            'sexo' => 'femenino',
        ]);

        $respuesta = $this
            ->actingAs($this->administrador)
            ->get(route('pacientes.show', $this->paciente));

        $respuesta
            ->assertOk()
            ->assertSee('Patología clínica protegida')
            ->assertDontSee('Editar historia clínica');

        $rutasClinicas = [
            'pacientes.historia-clinica.update',

            'pacientes.historia-clinica.'
                . 'heredofamiliares.update',

            'pacientes.historia-clinica.'
                . 'personales-patologicos.update',

            'pacientes.historia-clinica.'
                . 'personales-no-patologicos.update',

            'pacientes.historia-clinica.'
                . 'habitos-alimenticios.update',

            'pacientes.historia-clinica.'
                . 'ginecoobstetricos.update',
        ];

        foreach ($rutasClinicas as $ruta) {
            $respuesta->assertDontSee(
                route($ruta, $this->paciente),
                false
            );
        }
    }

    public function test_medico_asignado_recibe_formularios_clinicos_en_ficha(): void
    {
        $this->paciente->update([
            'sexo' => 'femenino',
        ]);

        $respuesta = $this
            ->actingAs($this->usuarioMedico)
            ->get(route('pacientes.show', $this->paciente));

        $respuesta
            ->assertOk()
            ->assertSee('Editar historia clínica');

        $rutasClinicas = [
            'pacientes.historia-clinica.update',

            'pacientes.historia-clinica.'
                . 'heredofamiliares.update',

            'pacientes.historia-clinica.'
                . 'personales-patologicos.update',

            'pacientes.historia-clinica.'
                . 'personales-no-patologicos.update',

            'pacientes.historia-clinica.'
                . 'habitos-alimenticios.update',

            'pacientes.historia-clinica.'
                . 'ginecoobstetricos.update',
        ];

        foreach ($rutasClinicas as $ruta) {
            $respuesta->assertSee(
                route($ruta, $this->paciente),
                false
            );
        }
    }

    public function test_solo_medico_ve_boton_de_ficha_desde_cita(): void
    {
        $rutaPaciente = route(
            'pacientes.show',
            $this->paciente
        );

        $this
            ->actingAs($this->usuarioMedico)
            ->get(route('citas.show', $this->cita))
            ->assertOk()
            ->assertSee('Ver ficha del paciente')
            ->assertSee($rutaPaciente, false)
            ->assertSee('target="_blank"', false)
            ->assertSee(
                'rel="noopener noreferrer"',
                false
            );

        $this
            ->actingAs($this->administrador)
            ->get(route('citas.show', $this->cita))
            ->assertOk()
            ->assertDontSee('Ver ficha del paciente');

        $this
            ->actingAs($this->recepcion)
            ->get(route('citas.show', $this->cita))
            ->assertOk()
            ->assertDontSee('Ver ficha del paciente');
    }
}
