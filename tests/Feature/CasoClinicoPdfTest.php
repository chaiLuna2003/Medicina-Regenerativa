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

class CasoClinicoPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_administracion_puede_descargar_pdf_del_caso(): void
    {
        $datos = $this->crearCasoClinico();

        $administracion = User::factory()->create([
            'name' => 'Administracion',
            'role' => 'admin',
            'status' => true,
        ]);

        $respuesta = $this
            ->actingAs($administracion)
            ->get(route('casos-clinicos.pdf', $datos['caso']));

        $respuesta->assertOk();
        $respuesta->assertHeader('content-type', 'application/pdf');

        $disposicion = $respuesta->headers->get(
            'content-disposition'
        );

        $this->assertNotNull($disposicion);
        $this->assertStringContainsString(
            'attachment',
            $disposicion
        );
        $this->assertStringContainsString(
            'caso-clinico-'.$datos['caso']->id,
            $disposicion
        );
        $this->assertStringStartsWith(
            '%PDF',
            $respuesta->getContent()
        );
    }

    public function test_medico_relacionado_puede_descargar_pdf(): void
    {
        $datos = $this->crearCasoClinico();

        $respuesta = $this
            ->actingAs($datos['usuarioMedico'])
            ->get(route('casos-clinicos.pdf', $datos['caso']));

        $respuesta
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith(
            '%PDF',
            $respuesta->getContent()
        );
    }

    public function test_recepcion_no_puede_descargar_pdf_del_caso(): void
    {
        $datos = $this->crearCasoClinico();

        $recepcion = User::factory()->create([
            'name' => 'Recepcion',
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $this
            ->actingAs($recepcion)
            ->get(route('casos-clinicos.pdf', $datos['caso']))
            ->assertForbidden();
    }

    private function crearCasoClinico(): array
    {
        $usuarioMedico = User::factory()->create([
            'name' => 'Medico responsable',
            'role' => 'medico',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Medico',
            'apellido_paterno' => 'Responsable',
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-PDF-001',
            'telefono' => '5550000601',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'PDF',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $fecha = now()
            ->addDay()
            ->toDateString();

        $cita = Citas::query()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => $fecha,
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_subsecuente',
            'notas' => 'Cita para probar el expediente PDF.',
            'estado' => 'programada',
            'created_by' => $usuarioMedico->id,
        ]);

        $caso = CasoClinico::query()->create([
            'paciente_id' => $paciente->id,
            'nombre' => 'Caso clinico para PDF',
            'descripcion_inicial' => 'Descripcion inicial del caso para la prueba.',

            'fecha_inicio' => $fecha,
            'estado' => CasoClinico::ESTADO_ACTIVO,
            'created_by' => $usuarioMedico->id,
        ]);

        EvolucionClinica::query()->create([
            'caso_clinico_id' => $caso->id,
            'cita_id' => $cita->id,
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => $fecha,
            'evolucion_clinica' => 'El paciente presenta evolucion favorable.',

            'diagnostico' => 'Diagnostico de prueba.',
            'tratamiento' => 'Tratamiento de prueba.',
            'plan' => 'Continuar seguimiento.',
            'created_by' => $usuarioMedico->id,
        ]);

        return [
            'usuarioMedico' => $usuarioMedico,
            'medico' => $medico,
            'paciente' => $paciente,
            'cita' => $cita,
            'caso' => $caso,
        ];
    }
}
