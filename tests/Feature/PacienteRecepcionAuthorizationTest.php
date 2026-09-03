<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
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

    public function test_recepcion_actualiza_identidad_del_paciente(): void
    {
        $recepcion = $this->usuario('recepcionista');
        $paciente = $this->paciente();

        $this
            ->actingAs($recepcion)
            ->put(route('pacientes.update', $paciente), [
                'nombre' => 'Nombre actualizado',
                'apellido' => 'Apellido actualizado',
                'fecha_nacimiento' => '2001-02-03',
                'sexo' => 'femenino',
                'categoria' => 'sin_categoria',
                'status' => '1',
            ])
            ->assertRedirect(
                route('pacientes.show', $paciente)
            );

        $paciente->refresh();

        $this->assertSame(
            'Nombre actualizado',
            $paciente->nombre
        );

        $this->assertSame(
            'Apellido actualizado',
            $paciente->apellido
        );

        $this->assertSame(
            '2001-02-03',
            $paciente->fecha_nacimiento->toDateString()
        );

        $this->assertSame(
            'femenino',
            $paciente->sexo
        );
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

    public function test_medico_vinculado_ve_edicion_sin_campos_telefonicos(): void
    {
        $medico = $this->medicoConPerfil();
        $paciente = $this->paciente();

        $this->vincularMedicoConPaciente(
            $medico,
            $paciente
        );

        $this
            ->actingAs($medico)
            ->get(route('pacientes.edit', $paciente))
            ->assertOk()
            ->assertDontSee(
                'name="telefono"',
                false
            )
            ->assertDontSee(
                'name="telefono_fijo"',
                false
            )
            ->assertDontSee(
                'name="telefono_secundario"',
                false
            )
            ->assertSee(
                'name="email"',
                false
            )
            ->assertDontSee(
                'href="'.route('pacientes.index').'"',
                false
            )
            ->assertSee(
                'href="'.route(
                    'pacientes.show',
                    $paciente
                ).'"',
                false
            );
    }

    public function test_medico_vinculado_actualiza_datos_no_telefonicos_del_paciente(): void
    {
        $medico = $this->medicoConPerfil();
        $paciente = $this->paciente();

        $paciente->update([
            'telefono' => '5500000000',
        ]);

        $this->vincularMedicoConPaciente(
            $medico,
            $paciente
        );

        $this
            ->actingAs($medico)
            ->put(route('pacientes.update', $paciente), [
                'nombre' => 'Paciente medico',
                'apellido' => 'Actualizado',
                'fecha_nacimiento' => '1985-06-15',
                'sexo' => 'femenino',
                'categoria' => 'unidem',
                'status' => '1',
                'email' => 'paciente.medico@example.com',
                'alergias' => 'Penicilina',
                'notas' => 'Informacion actualizada por medico.',
            ])
            ->assertRedirect(
                route('pacientes.show', $paciente)
            );

        $paciente->refresh();

        $this->assertSame(
            'Paciente medico',
            $paciente->nombre
        );

        $this->assertSame(
            'Actualizado',
            $paciente->apellido
        );

        $this->assertSame(
            '1985-06-15',
            $paciente->fecha_nacimiento->toDateString()
        );

        $this->assertSame(
            'femenino',
            $paciente->sexo
        );

        $this->assertSame(
            'unidem',
            $paciente->categoria
        );

        $this->assertSame(
            '5500000000',
            $paciente->telefono
        );

        $this->assertSame(
            'paciente.medico@example.com',
            $paciente->email
        );

        $this->assertSame(
            'Penicilina',
            $paciente->alergias
        );

        $this->assertSame(
            'Informacion actualizada por medico.',
            $paciente->notas
        );
    }

    public function test_medico_vinculado_no_actualiza_telefonos_del_paciente(): void
    {
        $medico = $this->medicoConPerfil();
        $paciente = $this->paciente();

        $paciente->update([
            'telefono' => '5500000000',
            'telefono_fijo' => '5555555555',
            'telefono_secundario' => '5511111111',
        ]);

        $this->vincularMedicoConPaciente(
            $medico,
            $paciente
        );

        $this
            ->actingAs($medico)
            ->put(route('pacientes.update', $paciente), [
                'telefono' => '5599999999',
            ])
            ->assertForbidden();

        $paciente->refresh();

        $this->assertSame(
            '5500000000',
            $paciente->telefono
        );

        $this->assertSame(
            '5555555555',
            $paciente->telefono_fijo
        );

        $this->assertSame(
            '5511111111',
            $paciente->telefono_secundario
        );
    }

    public function test_medico_ajeno_no_actualiza_al_paciente(): void
    {
        $medico = $this->medicoConPerfil();
        $paciente = $this->paciente();

        $this
            ->actingAs($medico)
            ->put(route('pacientes.update', $paciente), [
                'nombre' => 'Intento indebido',
                'apellido' => 'Bloqueado',
                'fecha_nacimiento' => '1991-01-01',
                'sexo' => 'femenino',
                'categoria' => 'unidem',
                'status' => '1',
            ])
            ->assertForbidden();

        $paciente->refresh();

        $this->assertSame('Paciente', $paciente->nombre);
        $this->assertSame('Prueba', $paciente->apellido);
    }

    public function test_medico_sin_perfil_no_actualiza_al_paciente(): void
    {
        $medico = $this->usuario('medico');
        $paciente = $this->paciente();

        $this
            ->actingAs($medico)
            ->put(route('pacientes.update', $paciente), [
                'nombre' => 'Intento indebido',
            ])
            ->assertForbidden();
    }

    private function medicoConPerfil(): User
    {
        $usuario = $this->usuario('medico');

        Medicos::query()->create([
            'user_id' => $usuario->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Autorización',
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-PACIENTE-'.$usuario->id,
            'telefono' => '5550000800',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);

        return $usuario->fresh();
    }

    private function vincularMedicoConPaciente(
        User $usuario,
        Pacientes $paciente
    ): void {
        Citas::query()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $usuario->medico->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_inicial',
            'estado' => 'confirmada',
            'created_by' => $usuario->id,
        ]);
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
