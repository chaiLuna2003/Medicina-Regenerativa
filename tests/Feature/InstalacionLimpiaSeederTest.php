<?php

namespace Tests\Feature;

use App\Models\Medicos;
use App\Models\User;
use App\Models\Universidad;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InstalacionLimpiaSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_carga_catalogos_sin_usuarios_inseguros(): void
    {
        config()->set('initial-admin.enabled', false);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('universidades', 13);

        $this->assertDatabaseHas('universidades', [
            'nombre' => 'Universidad Estatal del Valle de Ecatepec',
            'abreviatura' => 'UNEVE',
            'logo_path' => 'images/universidades/uneve.png',
        ]);
        $this->assertFileExists(public_path('images/universidades/uneve.png'));

        $this->assertDatabaseHas('universidades', [
            'nombre' => 'Justo Sierra',
            'abreviatura' => 'UJS',
            'logo_path' => 'images/universidades/logo_justosierra.png',
            'status' => true,
        ]);

        $this->assertFileExists(
            public_path(
                'images/universidades/logo_justosierra.png'
            )
        );

        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'adminre@medicinaregenerativa.com',
        ]);
    }

    public function test_seeder_asocia_uneve_solo_a_iris_y_puede_repetirse(): void
    {
        config()->set('initial-admin.enabled', false);

        $iris = Medicos::create([
            'nombre' => 'Iris',
            'apellido_paterno' => 'Rivera',
            'apellido_materno' => 'Padilla',
            'especialidad' => 'Medicina general',
            'cedula' => 'IRIS-TEST-001',
            'telefono' => '5566450302',
            'consultorio' => '1',
        ]);
        $otro = Medicos::create([
            'nombre' => 'Otro',
            'apellido_paterno' => 'Rivera',
            'apellido_materno' => 'Padilla',
            'especialidad' => 'Medicina general',
            'cedula' => 'OTRO-TEST-002',
            'telefono' => '5566450302',
            'consultorio' => '2',
        ]);

        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $uneve = Universidad::where('abreviatura', 'UNEVE')->firstOrFail();
        $this->assertSame($uneve->id, $iris->fresh()->universidad_id);
        $this->assertNull($otro->fresh()->universidad_id);
        $this->assertSame('images/universidades/uneve.png', $iris->fresh()->universidad->logo_path);
        $this->assertDatabaseCount('medicos', 2);
    }

    public function test_admin_inicial_se_crea_desde_configuracion_segura(): void
    {
        config()->set([
            'initial-admin.enabled' => true,
            'initial-admin.name' => 'Administrador inicial',
            'initial-admin.email' => 'admin@example.com',
            'initial-admin.password' => 'Clave-segura-2026',
        ]);

        $this->seed(AdminUserSeeder::class);

        $admin = User::query()
            ->where('email', 'admin@example.com')
            ->first();

        $this->assertNotNull($admin);
        $this->assertSame('Administrador inicial', $admin->name);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->status);
        $this->assertTrue(
            Hash::check('Clave-segura-2026', $admin->password)
        );
        $this->assertNotSame(
            'Clave-segura-2026',
            $admin->password
        );
    }

    public function test_repetir_seeder_no_reemplaza_password_del_admin(): void
    {
        config()->set([
            'initial-admin.enabled' => true,
            'initial-admin.name' => 'Administrador inicial',
            'initial-admin.email' => 'admin@example.com',
            'initial-admin.password' => 'Clave-original-2026',
        ]);

        $this->seed(AdminUserSeeder::class);

        config()->set(
            'initial-admin.password',
            'Clave-distinta-2026'
        );

        $this->seed(AdminUserSeeder::class);

        $admin = User::query()
            ->where('email', 'admin@example.com')
            ->first();

        $this->assertNotNull($admin);
        $this->assertTrue(
            Hash::check('Clave-original-2026', $admin->password)
        );
        $this->assertFalse(
            Hash::check('Clave-distinta-2026', $admin->password)
        );
        $this->assertDatabaseCount('users', 1);
    }
}
