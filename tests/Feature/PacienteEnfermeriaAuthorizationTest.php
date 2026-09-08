<?php

namespace Tests\Feature;

use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PacienteEnfermeriaAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_enfermeria_consulta_listado_ficha_y_edicion_de_pacientes(): void
    {
        $enfermero = $this->usuarioEnfermeria();
        $paciente = $this->paciente();

        $this
            ->actingAs($enfermero)
            ->get(route('pacientes.index'))
            ->assertOk()
            ->assertSee('Paciente')
            ->assertSee('Prueba');

        $this
            ->actingAs($enfermero)
            ->get(route('pacientes.show', $paciente))
            ->assertOk()
            ->assertSee('Paciente')
            ->assertSee('Prueba');

        $this
            ->actingAs($enfermero)
            ->get(route('pacientes.edit', $paciente))
            ->assertOk()
            ->assertSee('Paciente')
            ->assertSee('Prueba');
    }

    public function test_enfermeria_actualiza_los_datos_permitidos_del_paciente(): void
    {
        $enfermero = $this->usuarioEnfermeria();
        $paciente = $this->paciente();

        $this
            ->actingAs($enfermero)
            ->put(route('pacientes.update', $paciente), [
                'seccion' => 'generales',
                'nombre' => 'Paciente actualizado',
                'apellido' => 'Por enfermería',
                'fecha_nacimiento' => '1992-05-10',
                'sexo' => 'femenino',
                'tipo_sangre' => 'O+',
                'alergias' => 'Penicilina',
            ])
            ->assertRedirect(
                route('pacientes.show', $paciente)
            );

        $paciente->refresh();

        $this->assertSame(
            'Paciente actualizado',
            $paciente->nombre
        );

        $this->assertSame(
            'Por enfermería',
            $paciente->apellido
        );

        $this->assertSame(
            '1992-05-10',
            $paciente->fecha_nacimiento->toDateString()
        );

        $this->assertSame(
            'femenino',
            $paciente->sexo
        );

        $this->assertSame(
            'O+',
            $paciente->tipo_sangre
        );

        $this->assertSame(
            'Penicilina',
            $paciente->alergias
        );
    }

    public function test_enfermeria_no_puede_crear_pacientes(): void
    {
        $enfermero = $this->usuarioEnfermeria();

        $this
            ->actingAs($enfermero)
            ->get(route('pacientes.create'))
            ->assertForbidden();

        $this
            ->actingAs($enfermero)
            ->post(route('pacientes.store'), [
                'nombre' => 'Paciente indebido',
                'apellido' => 'No autorizado',
                'fecha_nacimiento' => '1990-01-01',
                'sexo' => 'masculino',
                'categoria' => 'sin_categoria',
                'status' => true,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('pacientes', [
            'nombre' => 'Paciente indebido',
        ]);
    }

    public function test_ficha_de_enfermeria_muestra_datos_seguros_y_oculta_informacion_clinica(): void
    {
        $enfermero = $this->usuarioEnfermeria();

        $paciente = $this->paciente();

        $paciente->update([
            'tipo_sangre' => 'O+',
            'alergias' => 'Penicilina',
        ]);

        $this
            ->actingAs($enfermero)
            ->get(route('pacientes.show', $paciente))
            ->assertOk()
            ->assertSee('O+')
            ->assertSee('Penicilina')
            ->assertDontSee('Historia clínica')
            ->assertDontSee('Recetas médicas')
            ->assertDontSee('Estudios clínicos')
            ->assertDontSee('Exploración física');
    }

    public function test_enfermeria_ve_el_enlace_de_pacientes_en_la_navegacion(): void
    {
        $enfermero = $this->usuarioEnfermeria();

        $this
            ->actingAs($enfermero)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(
                route('pacientes.index'),
                false
            );
    }

    private function usuarioEnfermeria(): User
    {
        return User::factory()->create([
            'role' => 'enfermero',
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
