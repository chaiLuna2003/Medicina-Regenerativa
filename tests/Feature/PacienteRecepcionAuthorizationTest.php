<?php

namespace Tests\Feature;

use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PacienteRecepcionAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_recepcion_actualiza_los_datos_administrativos_autorizados(): void
    {
        Storage::fake('public');

        $recepcion = $this->usuario('recepcionista');
        $paciente = $this->paciente();

        $respuesta = $this
            ->actingAs($recepcion)
            ->put(route('pacientes.update', $paciente), [
                'categoria' => 'unidem',
                'status' => '0',
                'telefono' => '5511111111',
                'telefono_fijo' => '5522222222',
                'telefono_secundario' => '5533333333',
                'email' => 'paciente@example.com',
                'domicilio' => 'Avenida Reforma 100',
                'ciudad' => 'Ciudad de México',
                'estado' => 'Ciudad de México',
                'codigo_postal' => '06600',
                'lugar_nacimiento' => 'Toluca',
                'ocupacion' => 'Docente',
                'religion' => 'No especificada',
                'estado_civil' => 'casado',
                'escolaridad' => 'licenciatura',
                'tipo_sangre' => 'O+',
                'alergias' => 'Penicilina',
                'costo_consulta_personalizado' => '850.50',
                'finado' => '1',
                'notas' => 'Nota administrativa de recepción.',
                'foto' => UploadedFile::fake()->image('paciente.jpg'),
            ]);

        $respuesta
            ->assertRedirect(route('pacientes.show', $paciente))
            ->assertSessionHas('success');

        $paciente->refresh();

        $this->assertSame('unidem', $paciente->categoria);
        $this->assertFalse($paciente->status);
        $this->assertSame('5511111111', $paciente->telefono);
        $this->assertSame('5522222222', $paciente->telefono_fijo);
        $this->assertSame('5533333333', $paciente->telefono_secundario);
        $this->assertSame('paciente@example.com', $paciente->email);
        $this->assertSame('Avenida Reforma 100', $paciente->domicilio);
        $this->assertSame('Ciudad de México', $paciente->ciudad);
        $this->assertSame('Ciudad de México', $paciente->estado);
        $this->assertSame('06600', $paciente->codigo_postal);
        $this->assertSame('Toluca', $paciente->lugar_nacimiento);
        $this->assertSame('Docente', $paciente->ocupacion);
        $this->assertSame('No especificada', $paciente->religion);
        $this->assertSame('casado', $paciente->estado_civil);
        $this->assertSame('licenciatura', $paciente->escolaridad);
        $this->assertSame('O+', $paciente->tipo_sangre);
        $this->assertSame('Penicilina', $paciente->alergias);
        $this->assertSame('850.50', $paciente->costo_consulta_personalizado);
        $this->assertTrue($paciente->finado);
        $this->assertSame('Nota administrativa de recepción.', $paciente->notas);
        $this->assertNotNull($paciente->foto);
        Storage::disk('public')->assertExists($paciente->foto);
    }

    public function test_recepcion_no_modifica_identidad_del_paciente(): void
    {
        $recepcion = $this->usuario('recepcionista');
        $paciente = $this->paciente();

        $this
            ->actingAs($recepcion)
            ->put(route('pacientes.update', $paciente), [
                'categoria' => 'sin_categoria',
                'status' => '1',
                'nombre' => 'Nombre alterado',
                'apellido' => 'Apellido alterado',
                'fecha_nacimiento' => '2001-02-03',
                'sexo' => 'femenino',
            ])
            ->assertRedirect(route('pacientes.show', $paciente));

        $paciente->refresh();

        $this->assertSame('Paciente', $paciente->nombre);
        $this->assertSame('Prueba', $paciente->apellido);
        $this->assertSame('1990-01-01', $paciente->fecha_nacimiento->toDateString());
        $this->assertSame('masculino', $paciente->sexo);
    }

    public function test_recepcion_no_puede_actualizar_rutas_clinicas(): void
    {
        $recepcion = $this->usuario('recepcionista');
        $paciente = $this->paciente();

        $rutas = [
            'pacientes.historia-clinica.update',
            'pacientes.historia-clinica.heredofamiliares.update',
            'pacientes.historia-clinica.personales-patologicos.update',
            'pacientes.historia-clinica.personales-no-patologicos.update',
            'pacientes.historia-clinica.habitos-alimenticios.update',
            'pacientes.historia-clinica.ginecoobstetricos.update',
        ];

        foreach ($rutas as $ruta) {
            $this
                ->actingAs($recepcion)
                ->put(route($ruta, $paciente))
                ->assertForbidden();
        }
    }

    public function test_medico_no_puede_actualizar_datos_administrativos(): void
    {
        $medico = $this->usuario('medico');
        $paciente = $this->paciente();

        $this
            ->actingAs($medico)
            ->put(route('pacientes.update', $paciente), [
                'categoria' => 'unidem',
                'status' => '0',
            ])
            ->assertForbidden();
    }

    private function usuario(string $rol): User
    {
        return User::factory()->create([
            'role' => $rol,
            'status' => true,
        ]);
    }

    private function paciente(): Pacientes
    {
        return Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Prueba',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);
    }
}
