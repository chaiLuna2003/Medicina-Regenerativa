<?php

namespace Tests\Feature;

use App\Models\Pacientes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class ImportarPacientesLegacyTest extends TestCase
{
    use RefreshDatabase;

    private const HEADERS = [
        'legacy_id', 'nombre', 'apellido', 'fecha_nacimiento', 'sexo', 'categoria',
        'telefono', 'telefono_fijo', 'telefono_secundario', 'email', 'domicilio',
        'ciudad', 'estado', 'codigo_postal', 'lugar_nacimiento', 'ocupacion',
        'religion', 'estado_civil', 'escolaridad', 'tipo_sangre', 'alergias',
        'notas', 'finado', 'status', 'importar', 'incidencias',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        File::delete($this->receiptPath());
        File::deleteDirectory($this->temporaryDirectory());
    }

    protected function tearDown(): void
    {
        File::delete($this->receiptPath());
        File::deleteDirectory($this->temporaryDirectory());

        parent::tearDown();
    }

    public function test_importa_un_paciente_valido(): void
    {
        $path = $this->fixturePath();

        $this->simulate($path);
        $this->commit($path);

        $this->assertDatabaseHas('pacientes', [
            'nombre' => 'Ana',
            'apellido' => 'Ejemplo',
            'fecha_nacimiento' => '1990-04-15 00:00:00',
        ]);
    }

    public function test_convierte_y_almacena_fecha_en_formato_iso(): void
    {
        $path = $this->fixturePath();

        $this->simulate($path);
        $this->commit($path);

        $paciente = Pacientes::query()->where('nombre', 'Ana')->firstOrFail();

        $this->assertSame('1990-04-15', $paciente->fecha_nacimiento->format('Y-m-d'));
    }

    public function test_rechaza_fecha_con_ano_incompleto(): void
    {
        $path = $this->createCsv([
            $this->row([
                'legacy_id' => 'TEST-FECHA',
                'fecha_nacimiento' => '3-02-02',
            ]),
        ]);

        $this->artisan('pacientes:importar-legacy', [
            'archivo' => $path,
        ])
            ->expectsOutputToContain('TEST-FECHA')
            ->assertSuccessful();

        $this->commit($path);

        $this->assertDatabaseCount('pacientes', 0);
    }

    public function test_mapea_sexo_al_catalogo_del_modelo(): void
    {
        $path = $this->fixturePath();

        $this->simulate($path);
        $this->commit($path);

        $this->assertDatabaseHas('pacientes', ['sexo' => 'femenino']);
    }

    public function test_mapea_estado_civil_al_catalogo_del_modelo(): void
    {
        $path = $this->fixturePath();

        $this->simulate($path);
        $this->commit($path);

        $this->assertDatabaseHas('pacientes', ['estado_civil' => 'union_libre']);
    }

    public function test_mapea_escolaridad_al_catalogo_del_modelo(): void
    {
        $path = $this->fixturePath();

        $this->simulate($path);
        $this->commit($path);

        $this->assertDatabaseHas('pacientes', ['escolaridad' => 'tecnico']);
    }

    public function test_rechaza_tipo_de_sangre_fuera_del_catalogo(): void
    {
        $path = $this->createCsv([
            $this->row([
                'legacy_id' => 'TEST-BLOOD',
                'tipo_sangre' => 'X+',
            ]),
        ]);

        $this->artisan('pacientes:importar-legacy', [
            'archivo' => $path,
        ])
            ->expectsOutputToContain('TEST-BLOOD')
            ->assertSuccessful();
        $this->commit($path);

        $this->assertDatabaseCount('pacientes', 0);
    }

    public function test_omite_filas_revisar_por_defecto(): void
    {
        $path = $this->fixturePath();

        $this->simulate($path);
        $this->commit($path);

        $this->assertDatabaseHas('pacientes', ['nombre' => 'Ana']);
        $this->assertDatabaseMissing('pacientes', ['nombre' => 'Bruno']);
    }

    public function test_incluye_fila_revisar_cuando_se_autoriza_y_es_valida(): void
    {
        $path = $this->fixturePath();

        $this->simulate($path, true);
        $this->commit($path, true);

        $this->assertDatabaseHas('pacientes', ['nombre' => 'Ana']);
        $this->assertDatabaseHas('pacientes', ['nombre' => 'Bruno']);
    }

    public function test_previene_duplicados_ignorando_mayusculas_y_espacios(): void
    {
        Pacientes::create([
            'nombre' => '  ANA ',
            'apellido' => 'EJEMPLO',
            'fecha_nacimiento' => '1990-04-15',
            'sexo' => 'femenino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        $path = $this->fixturePath();

        $this->simulate($path);
        $this->commit($path);

        $this->assertDatabaseCount('pacientes', 1);
    }

    public function test_simulacion_no_modifica_la_base_de_datos(): void
    {
        $this->simulate($this->fixturePath());

        $this->assertDatabaseCount('pacientes', 0);
    }

    public function test_commit_inserta_despues_de_una_simulacion_vigente(): void
    {
        $path = $this->fixturePath();

        $this->simulate($path);
        $this->commit($path);

        $this->assertDatabaseCount('pacientes', 1);
    }

    public function test_revierte_toda_la_transaccion_ante_error_inesperado(): void
    {
        $path = $this->createCsv([
            $this->row(['legacy_id' => 'TEST-ROLLBACK-1', 'nombre' => 'Primero']),
            $this->row(['legacy_id' => 'TEST-ROLLBACK-2', 'nombre' => 'Segundo']),
        ]);

        $this->simulate($path);

        $creaciones = 0;
        Pacientes::creating(function () use (&$creaciones): void {
            $creaciones++;

            if ($creaciones === 2) {
                throw new RuntimeException('Fallo controlado de prueba');
            }
        });

        try {
            $this->artisan('pacientes:importar-legacy', [
                'archivo' => $path,
                '--commit' => true,
            ])->assertFailed();
        } finally {
            Pacientes::flushEventListeners();
        }

        $this->assertDatabaseCount('pacientes', 0);
    }

    public function test_lee_csv_con_bom_utf8(): void
    {
        $contents = File::get($this->fixturePath());

        $this->assertTrue(str_starts_with($contents, "\xEF\xBB\xBF"));
        $this->simulate($this->fixturePath());
        $this->assertDatabaseCount('pacientes', 0);
    }

    public function test_no_intenta_insertar_columnas_auxiliares(): void
    {
        $path = $this->fixturePath();

        $this->simulate($path);
        $this->commit($path);

        $this->assertFalse(Schema::hasColumn('pacientes', 'legacy_id'));
        $this->assertFalse(Schema::hasColumn('pacientes', 'importar'));
        $this->assertFalse(Schema::hasColumn('pacientes', 'incidencias'));
        $this->assertDatabaseCount('pacientes', 1);
    }

    private function simulate(string $path, bool $includeReview = false)
    {
        return $this->artisan('pacientes:importar-legacy', [
            'archivo' => $path,
            '--include-review' => $includeReview,
        ])->assertSuccessful();
    }

    private function commit(string $path, bool $includeReview = false)
    {
        return $this->artisan('pacientes:importar-legacy', [
            'archivo' => $path,
            '--commit' => true,
            '--include-review' => $includeReview,
        ])->assertSuccessful();
    }

    private function fixturePath(): string
    {
        return base_path('tests/Fixtures/pacientes_legacy.csv');
    }

    private function createCsv(array $rows): string
    {
        File::ensureDirectoryExists($this->temporaryDirectory());
        $path = $this->temporaryDirectory().'/'.uniqid('pacientes-', true).'.csv';
        $handle = fopen($path, 'wb');

        if ($handle === false) {
            throw new RuntimeException('No se pudo crear el CSV de prueba.');
        }

        fputcsv($handle, self::HEADERS);

        foreach ($rows as $row) {
            fputcsv($handle, array_map(
                static fn (string $header): mixed => $row[$header] ?? '',
                self::HEADERS
            ));
        }

        fclose($handle);

        return $path;
    }

    private function row(array $overrides = []): array
    {
        return array_merge(array_fill_keys(self::HEADERS, ''), [
            'legacy_id' => 'TEST-BASE',
            'nombre' => 'Paciente',
            'apellido' => 'Ficticio',
            'fecha_nacimiento' => '1990-01-15',
            'sexo' => 'FEMENINO',
            'categoria' => 'sin_categoria',
            'estado_civil' => 'SOLTERO',
            'escolaridad' => 'LICENCIATURA',
            'tipo_sangre' => 'O+',
            'finado' => '0',
            'status' => '1',
            'importar' => 'SI',
        ], $overrides);
    }

    private function receiptPath(): string
    {
        return storage_path('app/private/importaciones/pacientes-legacy-preflight.json');
    }

    private function temporaryDirectory(): string
    {
        return storage_path('framework/testing/pacientes-legacy');
    }
}
