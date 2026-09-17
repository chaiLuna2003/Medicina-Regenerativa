<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\ControlPeso;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ControlPesoTest extends TestCase
{
    use RefreshDatabase;

    public function test_enfermeria_registra_edita_y_descarga_un_control_ligado_a_cita(): void
    {
        [$paciente, $cita] = $this->pacienteYCita();
        $enfermero = $this->usuario('enfermero');

        $this->actingAs($enfermero)
            ->post(route('pacientes.controles-peso.store', $paciente), $this->datos($cita))
            ->assertRedirect(route('pacientes.show', $paciente));

        $control = ControlPeso::query()->sole();
        $this->assertSame($paciente->id, $control->paciente_id);
        $this->assertSame($cita->id, $control->cita_id);
        $this->assertSame('72.50', $control->peso);
        $this->assertSame(['Reducir grasa', 'Mejorar fuerza'], $control->objetivos);
        $this->assertSame([['nombre' => 'Péptido A', 'dosis' => '5 mg', 'tiempo' => '4 semanas']], $control->peptidos);

        $this->actingAs($enfermero)
            ->put(route('pacientes.controles-peso.update', [$paciente, $control]), [
                ...$this->datos($cita), 'peso' => '71.00', 'objetivos' => ['Nuevo objetivo'],
            ])
            ->assertRedirect(route('pacientes.show', $paciente));

        $this->assertSame('71.00', $control->fresh()->peso);
        $this->assertSame(['Nuevo objetivo'], $control->fresh()->objetivos);

        $respuesta = $this->actingAs($enfermero)
            ->get(route('pacientes.controles-peso.pdf', [$paciente, $control]));
        $respuesta->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $respuesta->getContent());
        $this->assertStringContainsString('tratamiento-precision-', $respuesta->headers->get('content-disposition'));

        $html = view('pacientes.pdf.control-peso', [
            'control' => $control->fresh()->load('cita'), 'paciente' => $paciente,
        ])->render();
        $this->assertStringContainsString('Tratamiento de precisión', $html);
        $this->assertStringNotContainsString('Médico Prueba', $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    public function test_administracion_y_recepcion_consultan_card_y_pdf_sin_poder_editar(): void
    {
        [$paciente, $cita] = $this->pacienteYCita();
        $control = $paciente->controlesPeso()->create([
            ...$this->datos($cita), 'creado_por' => $this->usuario('enfermero')->id,
        ]);

        foreach (['admin', 'recepcionista'] as $rol) {
            $usuario = $this->usuario($rol);
            $this->actingAs($usuario)->get(route('pacientes.show', $paciente))
                ->assertOk()->assertSee('Tratamiento de precisión')->assertSee('72.50 kg')
                ->assertDontSee('Nuevo tratamiento de precisión')
                ->assertDontSee('data-update-url=');

            $this->actingAs($usuario)->get(route('pacientes.controles-peso.pdf', [$paciente, $control]))
                ->assertOk()->assertHeader('content-type', 'application/pdf');

            $this->actingAs($usuario)
                ->post(route('pacientes.controles-peso.store', $paciente), $this->datos($cita))
                ->assertForbidden();
            $this->actingAs($usuario)
                ->put(route('pacientes.controles-peso.update', [$paciente, $control]), $this->datos($cita))
                ->assertForbidden();
        }
    }

    public function test_medico_solo_edita_controles_de_sus_citas_y_la_cita_debe_ser_del_paciente(): void
    {
        [$paciente, $cita, $medico] = $this->pacienteYCita();
        [$otroPaciente, $otraCita, $otroMedico] = $this->pacienteYCita();
        $control = $paciente->controlesPeso()->create([
            ...$this->datos($cita), 'creado_por' => $this->usuario('enfermero')->id,
        ]);

        $this->actingAs($medico)
            ->post(route('pacientes.controles-peso.store', $paciente), $this->datos($cita))
            ->assertRedirect(route('pacientes.show', $paciente));

        $this->actingAs($medico)
            ->post(route('pacientes.controles-peso.store', $paciente), $this->datos($otraCita))
            ->assertSessionHasErrors('cita_id');

        $citaOtroMedico = $this->cita($paciente, $otroMedico);
        $this->actingAs($medico)
            ->post(route('pacientes.controles-peso.store', $paciente), $this->datos($citaOtroMedico))
            ->assertForbidden();

        $this->actingAs($otroMedico)
            ->put(route('pacientes.controles-peso.update', [$paciente, $control]), $this->datos($citaOtroMedico))
            ->assertForbidden();

        $this->actingAs($otroMedico)
            ->get(route('pacientes.controles-peso.pdf', [$otroPaciente, $control]))
            ->assertNotFound();
    }

    public function test_mediciones_fuera_de_rango_no_se_guardan_y_medico_ajeno_no_descarga_pdf(): void
    {
        [$paciente, $cita] = $this->pacienteYCita();
        [, , $medicoAjeno] = $this->pacienteYCita();

        $this->actingAs($this->usuario('enfermero'))
            ->post(route('pacientes.controles-peso.store', $paciente), [
                ...$this->datos($cita), 'imc' => '0', 'porcentaje_grasa' => '101',
            ])
            ->assertSessionHasErrors(['imc', 'porcentaje_grasa']);

        $this->assertDatabaseCount('controles_peso', 0);

        $control = $paciente->controlesPeso()->create([
            ...$this->datos($cita), 'creado_por' => $this->usuario('enfermero')->id,
        ]);

        $this->actingAs($medicoAjeno)
            ->get(route('pacientes.controles-peso.pdf', [$paciente, $control]))
            ->assertForbidden();
    }

    private function datos(Citas $cita): array
    {
        return [
            'cita_id' => $cita->id,
            'diagnostico' => 'Evaluación inicial',
            'peso' => '72.50', 'talla' => '168.00', 'imc' => '25.69',
            'porcentaje_grasa' => '28.40', 'indice_antioxidante' => '42',
            'objetivos' => ['Reducir grasa', 'Mejorar fuerza', ''],
            'tratamiento_base' => 'Seguimiento semanal',
            'peptidos' => [
                ['nombre' => 'Péptido A', 'dosis' => '5 mg', 'tiempo' => '4 semanas'],
                ['nombre' => '', 'dosis' => '', 'tiempo' => ''],
            ],
            'suplementos' => ['Cofactor A', '', ''],
            'indicacion_dietetica' => 'Plan individual',
            'actividad_fisica' => 'Actividad según tolerancia',
        ];
    }

    private function pacienteYCita(): array
    {
        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente', 'apellido' => 'Prueba',
            'fecha_nacimiento' => '1990-01-01', 'sexo' => 'femenino',
            'categoria' => 'sin_categoria', 'status' => true,
        ]);
        $medico = $this->usuario('medico');
        Medicos::query()->create([
            'user_id' => $medico->id, 'nombre' => 'Médico',
            'apellido_paterno' => 'Prueba', 'especialidad' => 'General',
            'cedula' => 'CED-'.$medico->id, 'telefono' => '5550000800',
            'consultorio' => 'Consultorio 1', 'status' => true,
        ]);
        $medico = $medico->fresh();

        return [$paciente, $this->cita($paciente, $medico), $medico];
    }

    private function cita(Pacientes $paciente, User $medico): Citas
    {
        return Citas::query()->create([
            'paciente_id' => $paciente->id, 'medico_id' => $medico->medico->id,
            'fecha' => now()->addDay()->toDateString(), 'hora' => '10:00',
            'duracion_minutos' => 30, 'modalidad' => 'presencial',
            'motivo' => 'consulta_inicial', 'estado' => 'confirmada',
            'created_by' => $medico->id,
        ]);
    }

    private function usuario(string $rol): User
    {
        return User::factory()->create(['role' => $rol, 'status' => true]);
    }
}
