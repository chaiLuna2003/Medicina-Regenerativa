<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\Receta;
use App\Models\SignoVital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecetaPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_administracion_descarga_receta_horizontal_con_signos_de_su_cita(): void
    {
        $datos = $this->crearRecetaConCita();

        SignoVital::query()->create([
            'paciente_id' => $datos['paciente']->id,
            'cita_id' => $datos['cita']->id,
            'enfermero_id' => $datos['enfermero']->id,
            'peso' => 72.50,
            'estatura' => 168.00,
            'temperatura' => 36.7,
            'presion_sistolica' => 120,
            'presion_diastolica' => 80,
            'frecuencia_cardiaca' => 74,
            'frecuencia_respiratoria' => 18,
            'saturacion_oxigeno' => 98,
            'glucosa' => 92.00,
        ]);

        $respuesta = $this
            ->actingAs($datos['administrador'])
            ->get(route('recetas.pdf', $datos['receta']));

        $respuesta
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith(
            '%PDF',
            $respuesta->getContent()
        );

        $this->assertStringContainsString(
            'attachment',
            (string) $respuesta->headers->get(
                'content-disposition'
            )
        );
    }

    public function test_plantilla_muestra_signos_de_la_cita_de_la_receta(): void
    {
        $datos = $this->crearRecetaConCita();

        SignoVital::query()->create([
            'paciente_id' => $datos['paciente']->id,
            'cita_id' => $datos['cita']->id,
            'enfermero_id' => $datos['enfermero']->id,
            'peso' => 72.50,
            'estatura' => 168.00,
            'temperatura' => 36.7,
            'presion_sistolica' => 120,
            'presion_diastolica' => 80,
            'frecuencia_cardiaca' => 74,
            'frecuencia_respiratoria' => 18,
            'saturacion_oxigeno' => 98,
            'glucosa' => 92.00,
        ]);

        $otraCita = Citas::query()->create([
            'paciente_id' => $datos['paciente']->id,
            'medico_id' => $datos['medico']->id,
            'fecha' => now()->addDays(2)->toDateString(),
            'hora' => '12:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_subsecuente',
            'estado' => 'programada',
            'created_by' => $datos['administrador']->id,
        ]);

        SignoVital::query()->create([
            'paciente_id' => $datos['paciente']->id,
            'cita_id' => $otraCita->id,
            'enfermero_id' => $datos['enfermero']->id,
            'peso' => 199.99,
            'estatura' => 199.99,
            'temperatura' => 39.9,
            'presion_sistolica' => 199,
            'presion_diastolica' => 119,
            'frecuencia_cardiaca' => 199,
            'frecuencia_respiratoria' => 39,
            'saturacion_oxigeno' => 77,
            'glucosa' => 299.99,
        ]);

        $receta = $datos['receta']->fresh()->load([
            'cita.paciente',
            'cita.medico.user',
            'cita.medico.universidad',
            'cita.signoVital',
        ]);

        $html = view(
            'recetas.pdf',
            compact('receta')
        )->render();

        $this->assertStringContainsString('72.50', $html);
        $this->assertStringContainsString('168.00', $html);
        $this->assertStringContainsString('120/80', $html);
        $this->assertStringContainsString('98', $html);
        $this->assertStringContainsString('92.00', $html);

        $this->assertStringNotContainsString('199.99', $html);
        $this->assertStringNotContainsString('199/119', $html);
        $this->assertStringNotContainsString('299.99', $html);
    }

    public function test_receta_sin_valoracion_muestra_estado_vacio(): void
    {
        $datos = $this->crearRecetaConCita();

        $receta = $datos['receta']->fresh()->load([
            'cita.paciente',
            'cita.medico.user',
            'cita.medico.universidad',
            'cita.signoVital',
        ]);

        $html = view(
            'recetas.pdf',
            compact('receta')
        )->render();

        $this->assertStringContainsString(
            'No se registraron signos vitales',
            $html
        );
    }

    public function test_configuracion_de_receta_conserva_privacidad_y_orientacion(): void
    {
        $controlador = file_get_contents(
            app_path('Http/Controllers/RecetasController.php')
        );

        $plantilla = file_get_contents(
            resource_path('views/recetas/pdf.blade.php')
        );

        $this->assertIsString($controlador);
        $this->assertIsString($plantilla);

        $this->assertStringContainsString(
            "'cita.signoVital'",
            $controlador
        );

        $this->assertStringContainsString(
            "'landscape'",
            $controlador
        );

        foreach (
            [
                'consultorio',
                'telefono',
                'teléfono',
                'user->email',
                'Correo:',
            ] as $campoProhibido
        ) {
            $this->assertStringNotContainsString(
                $campoProhibido,
                $plantilla
            );
        }

        $this->assertStringContainsString(
            'Dirección de atención',
            $plantilla
        );

        $this->assertStringContainsString(
            'Horario de atención',
            $plantilla
        );
    }

    private function crearRecetaConCita(): array
    {
        $administrador = User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);

        $usuarioMedico = User::factory()->create([
            'role' => 'medico',
            'status' => true,
        ]);

        $enfermero = User::factory()->create([
            'role' => 'enfermero',
            'status' => true,
        ]);

        $medico = Medicos::query()->create([
            'user_id' => $usuarioMedico->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Receta',
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => 'REC-PDF-001',
            'telefono' => '5550000700',
            'consultorio' => 'Consultorio de prueba',
            'status' => true,
        ]);

        $paciente = Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Receta PDF',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $cita = Citas::query()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_subsecuente',
            'estado' => 'programada',
            'created_by' => $administrador->id,
        ]);

        $receta = Receta::query()->create([
            'cita_id' => $cita->id,
            'contenido' => 'Tomar medicamento cada ocho horas.',
            'fecha_expedicion' => now(),
        ]);

        return compact(
            'administrador',
            'usuarioMedico',
            'enfermero',
            'medico',
            'paciente',
            'cita',
            'receta'
        );
    }
}
