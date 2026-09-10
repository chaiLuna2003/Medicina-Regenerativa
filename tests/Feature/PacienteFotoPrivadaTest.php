<?php

namespace Tests\Feature;

use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PacienteFotoPrivadaTest extends TestCase
{
    use RefreshDatabase;

    public function test_foto_de_paciente_se_guarda_en_disco_privado(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $recepcion = $this->usuario('recepcionista');
        $paciente = $this->paciente();

        $this
            ->actingAs($recepcion)
            ->put(route('pacientes.update', $paciente), [
                'foto' => UploadedFile::fake()
                    ->image('paciente.jpg'),
            ])
            ->assertRedirect(
                route('pacientes.show', $paciente)
            )
            ->assertSessionHasNoErrors();

        $paciente->refresh();

        $this->assertNotNull($paciente->foto);

        Storage::disk('local')
            ->assertExists($paciente->foto);

        Storage::disk('public')
            ->assertMissing($paciente->foto);
    }

    public function test_usuario_autorizado_puede_ver_foto_privada(): void
    {
        Storage::fake('local');

        $administrador = $this->usuario('admin');
        $paciente = $this->paciente();
        $ruta = 'pacientes/paciente.jpg';

        Storage::disk('local')->put(
            $ruta,
            'contenido-de-imagen'
        );

        $paciente->update([
            'foto' => $ruta,
        ]);

        $this
            ->actingAs($administrador)
            ->get(route('pacientes.foto', $paciente))
            ->assertOk();
    }

    public function test_invitado_no_puede_ver_foto_privada(): void
    {
        Storage::fake('local');

        $paciente = $this->paciente();
        $ruta = 'pacientes/paciente.jpg';

        Storage::disk('local')->put(
            $ruta,
            'contenido-de-imagen'
        );

        $paciente->update([
            'foto' => $ruta,
        ]);

        $this
            ->get(route('pacientes.foto', $paciente))
            ->assertRedirect(route('login'));
    }

    public function test_medico_sin_relacion_no_puede_ver_foto_privada(): void
    {
        Storage::fake('local');

        $medico = $this->usuario('medico');
        $paciente = $this->paciente();
        $ruta = 'pacientes/paciente.jpg';

        Storage::disk('local')->put(
            $ruta,
            'contenido-de-imagen'
        );

        $paciente->update([
            'foto' => $ruta,
        ]);

        $this
            ->actingAs($medico)
            ->get(route('pacientes.foto', $paciente))
            ->assertForbidden();
    }

    private function usuario(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'status' => true,
        ]);
    }

    private function paciente(): Pacientes
    {
        return Pacientes::query()->create([
            'nombre' => 'Paciente',
            'apellido' => 'Fotografia',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);
    }
}
