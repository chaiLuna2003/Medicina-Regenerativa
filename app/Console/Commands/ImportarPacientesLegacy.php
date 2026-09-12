<?php

namespace App\Console\Commands;

use App\Models\Pacientes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class ImportarPacientesLegacy extends Command
{
    protected $signature = 'pacientes:importar-legacy
        {archivo : Ruta absoluta o relativa al CSV}
        {--commit : Inserta los registros; sin esta opción solo simula}
        {--include-review : Incluye filas marcadas como REVISAR si son válidas}';

    protected $description = 'Valida e importa pacientes legacy desde un CSV normalizado';

    private const HEADERS = [
        'legacy_id',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'sexo',
        'categoria',
        'telefono',
        'telefono_fijo',
        'telefono_secundario',
        'email',
        'domicilio',
        'ciudad',
        'estado',
        'codigo_postal',
        'lugar_nacimiento',
        'ocupacion',
        'religion',
        'estado_civil',
        'escolaridad',
        'tipo_sangre',
        'alergias',
        'notas',
        'finado',
        'status',
        'importar',
        'incidencias',
    ];

    private const PAYLOAD_FIELDS = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'sexo',
        'categoria',
        'telefono',
        'telefono_fijo',
        'telefono_secundario',
        'email',
        'domicilio',
        'ciudad',
        'estado',
        'codigo_postal',
        'lugar_nacimiento',
        'ocupacion',
        'religion',
        'estado_civil',
        'escolaridad',
        'tipo_sangre',
        'alergias',
        'notas',
        'finado',
        'status',
    ];

    public function handle(): int
    {
        $commit = (bool) $this->option('commit');
        $includeReview = (bool) $this->option('include-review');

        if (! $this->entornoPermiteEjecucion()) {
            return self::FAILURE;
        }

        $path = $this->resolverRuta((string) $this->argument('archivo'));

        if (! is_file($path) || ! is_readable($path)) {
            $this->error('No se puede leer el archivo indicado.');

            return self::FAILURE;
        }

        $hash = hash_file('sha256', $path);

        if ($hash === false) {
            $this->error('No fue posible calcular la huella del archivo.');

            return self::FAILURE;
        }

        $resultado = $this->prepararImportacion($path, $includeReview);

        if ($resultado['fatal'] !== null) {
            $this->error($resultado['fatal']);

            return self::FAILURE;
        }

        $databaseSignature = $this->firmaBaseDeDatos();

        if (! $commit) {
            $this->guardarComprobante($hash, $includeReview, $databaseSignature);
            $this->mostrarResultado($resultado, false, 0);
            $this->info('Simulación terminada. No se modificó la base de datos.');

            return self::SUCCESS;
        }

        if (! $this->comprobanteValido($hash, $includeReview, $databaseSignature)) {
            $this->error('No existe una simulación vigente para este archivo, opciones y estado de la base de datos.');
            $this->line('Ejecuta primero el mismo comando sin --commit.');

            return self::FAILURE;
        }

        $insertados = 0;

        try {
            DB::transaction(function () use ($resultado, &$insertados): void {
                foreach ($resultado['candidatos'] as $payload) {
                    Pacientes::create($payload);
                    $insertados++;
                }
            });
        } catch (Throwable $exception) {
            report($exception);
            $this->error('La importación falló y la transacción fue revertida completamente.');
            $this->mostrarResultado($resultado, true, 0, 1);

            return self::FAILURE;
        }

        File::delete($this->rutaComprobante());
        $this->mostrarResultado($resultado, true, $insertados);
        $this->info('Importación local terminada correctamente.');

        return self::SUCCESS;
    }

    private function prepararImportacion(string $path, bool $includeReview): array
    {
        $resultado = [
            'leidos' => 0,
            'candidatos' => [],
            'omitidos_revision' => 0,
            'duplicados_existentes' => 0,
            'duplicados_archivo' => 0,
            'invalidos' => 0,
            'errores' => [],
            'fatal' => null,
        ];

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            $resultado['fatal'] = 'No fue posible abrir el CSV.';

            return $resultado;
        }

        try {
            $headers = fgetcsv($handle);

            if ($headers === false) {
                $resultado['fatal'] = 'El CSV no contiene encabezados.';

                return $resultado;
            }

            $headers = array_map(
                static fn ($header): string => trim((string) $header),
                $headers
            );
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]) ?? $headers[0];

            $headerError = $this->validarHeaders($headers);

            if ($headerError !== null) {
                $resultado['fatal'] = $headerError;

                return $resultado;
            }

            $existingKeys = $this->clavesExistentes();
            $fileKeys = [];

            while (($values = fgetcsv($handle)) !== false) {
                if ($this->filaVacia($values)) {
                    continue;
                }

                $resultado['leidos']++;

                if (count($values) !== count($headers)) {
                    $resultado['invalidos']++;
                    $resultado['errores'][] = [
                        'legacy_id' => '?',
                        'mensaje' => 'cantidad de columnas incorrecta',
                    ];

                    continue;
                }

                $row = array_combine($headers, $values);

                if ($row === false) {
                    $resultado['invalidos']++;
                    $resultado['errores'][] = [
                        'legacy_id' => '?',
                        'mensaje' => 'no se pudo interpretar la fila',
                    ];

                    continue;
                }

                $legacyId = $this->identificadorSeguro($row['legacy_id'] ?? null);
                $importar = mb_strtoupper(trim((string) ($row['importar'] ?? '')));

                if ($importar === 'REVISAR' && ! $includeReview) {
                    $resultado['omitidos_revision']++;

                    continue;
                }

                $payload = $this->crearPayload($row);
                $validator = Validator::make($payload, $this->reglas());

                if ($validator->fails()) {
                    $resultado['invalidos']++;
                    $resultado['errores'][] = [
                        'legacy_id' => $legacyId,
                        'mensaje' => $validator->errors()->first(),
                    ];

                    continue;
                }

                $payload = $validator->validated();
                $key = $this->clavePaciente($payload);

                if (isset($existingKeys[$key])) {
                    $resultado['duplicados_existentes']++;

                    continue;
                }

                if (isset($fileKeys[$key])) {
                    $resultado['duplicados_archivo']++;

                    continue;
                }

                $fileKeys[$key] = true;
                $resultado['candidatos'][] = $payload;
            }
        } finally {
            fclose($handle);
        }

        return $resultado;
    }

    private function crearPayload(array $row): array
    {
        $payload = [];

        foreach (self::PAYLOAD_FIELDS as $field) {
            $payload[$field] = $this->valorNullable($row[$field] ?? null);
        }

        $payload['nombre'] = $this->normalizarEspacios($payload['nombre']);
        $payload['apellido'] = $this->normalizarEspacios($payload['apellido']);
        $payload['fecha_nacimiento'] = $this->normalizarFecha($payload['fecha_nacimiento']);
        $payload['sexo'] = $this->normalizarSexo($payload['sexo']);
        $payload['categoria'] = $this->normalizarClaveCatalogo($payload['categoria']);
        $payload['estado_civil'] = $this->normalizarEstadoCivil($payload['estado_civil']);
        $payload['escolaridad'] = $this->normalizarEscolaridad($payload['escolaridad']);
        $payload['tipo_sangre'] = $this->normalizarTipoSangre($payload['tipo_sangre']);
        $payload['email'] = $payload['email'] === null
            ? null
            : mb_strtolower($payload['email']);
        $payload['finado'] = $this->normalizarBooleano($payload['finado']);
        $payload['status'] = $this->normalizarBooleano($payload['status']);

        return $payload;
    }

    private function reglas(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'fecha_nacimiento' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'sexo' => ['required', Rule::in(array_keys(Pacientes::SEXOS))],
            'categoria' => ['required', Rule::in(array_keys(Pacientes::CATEGORIAS))],
            'telefono' => ['nullable', 'string', 'max:20'],
            'telefono_fijo' => ['nullable', 'string', 'max:20'],
            'telefono_secundario' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'domicilio' => ['nullable', 'string', 'max:500'],
            'ciudad' => ['nullable', 'string', 'max:150'],
            'estado' => ['nullable', 'string', 'max:150'],
            'codigo_postal' => ['nullable', 'string', 'max:10'],
            'lugar_nacimiento' => ['nullable', 'string', 'max:200'],
            'ocupacion' => ['nullable', 'string', 'max:200'],
            'religion' => ['nullable', 'string', 'max:150'],
            'estado_civil' => ['nullable', Rule::in(array_keys(Pacientes::ESTADOS_CIVILES))],
            'escolaridad' => ['nullable', Rule::in(array_keys(Pacientes::ESCOLARIDADES))],
            'tipo_sangre' => ['nullable', Rule::in(array_keys(Pacientes::TIPOS_SANGRE))],
            'alergias' => ['nullable', 'string', 'max:2000'],
            'notas' => ['nullable', 'string', 'max:5000'],
            'finado' => ['nullable', 'boolean'],
            'status' => ['required', 'boolean'],
        ];
    }

    private function clavesExistentes(): array
    {
        $keys = [];

        Pacientes::query()
            ->select(['nombre', 'apellido', 'fecha_nacimiento'])
            ->each(function (Pacientes $paciente) use (&$keys): void {
                $keys[$this->clavePaciente([
                    'nombre' => $paciente->nombre,
                    'apellido' => $paciente->apellido,
                    'fecha_nacimiento' => $paciente->fecha_nacimiento?->format('Y-m-d'),
                ])] = true;
            });

        return $keys;
    }

    private function clavePaciente(array $payload): string
    {
        return implode('|', [
            $this->normalizarIdentidad((string) ($payload['nombre'] ?? '')),
            $this->normalizarIdentidad((string) ($payload['apellido'] ?? '')),
            (string) ($payload['fecha_nacimiento'] ?? ''),
        ]);
    }

    private function normalizarIdentidad(string $value): string
    {
        return mb_strtolower($this->normalizarEspacios($value) ?? '');
    }

    private function normalizarEspacios(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return preg_replace('/\s+/u', ' ', trim((string) $value)) ?: null;
    }

    private function valorNullable(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizarFecha(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;

        $formats = [
            '/^\d{4}-\d{2}-\d{2}$/' => 'Y-m-d',
            '/^\d{2}\/\d{2}\/\d{4}$/' => 'd/m/Y',
        ];

        foreach ($formats as $pattern => $format) {
            if (! preg_match($pattern, $value)) {
                continue;
            }

            $date = \DateTimeImmutable::createFromFormat('!'.$format, $value);
            $errors = \DateTimeImmutable::getLastErrors();

            if ($date !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date->format('Y-m-d');
            }
        }

        return $value;
    }

    private function normalizarSexo(mixed $value): mixed
    {
        $key = $this->normalizarClaveCatalogo($value);

        return match ($key) {
            'f', 'mujer', 'femenino' => 'femenino',
            'm', 'hombre', 'masculino' => 'masculino',
            default => $key,
        };
    }

    private function normalizarEstadoCivil(mixed $value): mixed
    {
        $key = $this->normalizarClaveCatalogo($value);

        return match ($key) {
            'soltero_a' => 'soltero',
            'casado_a' => 'casado',
            'divorciado_a' => 'divorciado',
            'separado_a' => 'separado',
            'viudo_a' => 'viudo',
            default => $key,
        };
    }

    private function normalizarEscolaridad(mixed $value): mixed
    {
        $key = $this->normalizarClaveCatalogo($value);

        return match ($key) {
            'carrera_tecnica' => 'tecnico',
            'preparatoria' => 'bachillerato',
            'universidad' => 'licenciatura',
            'maestria', 'doctorado' => 'posgrado',
            'ninguna' => 'sin_escolaridad',
            default => $key,
        };
    }

    private function normalizarTipoSangre(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $bloodType = mb_strtoupper(preg_replace('/\s+/u', '', (string) $value) ?? '');

        return $bloodType === 'DESCONOCIDO' ? 'desconocido' : $bloodType;
    }

    private function normalizarClaveCatalogo(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return Str::of((string) $value)
            ->trim()
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->value();
    }

    private function normalizarBooleano(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return match (mb_strtolower(trim((string) $value))) {
            '1', 'true', 'si', 'sí' => true,
            '0', 'false', 'no' => false,
            default => $value,
        };
    }

    private function filaVacia(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function validarHeaders(array $headers): ?string
    {
        if (count($headers) !== count(array_unique($headers))) {
            return 'El CSV contiene encabezados duplicados.';
        }

        $missing = array_values(array_diff(self::HEADERS, $headers));
        $unexpected = array_values(array_diff($headers, self::HEADERS));

        if ($missing !== []) {
            return 'Faltan columnas requeridas: '.implode(', ', $missing).'.';
        }

        if ($unexpected !== []) {
            return 'El CSV contiene columnas inesperadas: '.implode(', ', $unexpected).'.';
        }

        return null;
    }

    private function identificadorSeguro(mixed $legacyId): string
    {
        $legacyId = trim((string) $legacyId);

        return preg_match('/^[A-Za-z0-9_-]{1,50}$/', $legacyId) === 1
            ? $legacyId
            : '?';
    }

    private function resolverRuta(string $argument): string
    {
        $isAbsolute = str_starts_with($argument, DIRECTORY_SEPARATOR)
            || preg_match('/^[A-Za-z]:[\\\\\/]/', $argument) === 1;

        return $isAbsolute ? $argument : base_path($argument);
    }

    private function entornoPermiteEjecucion(): bool
    {
        if (app()->environment('production')) {
            $this->error('La lectura e importación están bloqueadas en producción.');

            return false;
        }

        $connection = (string) config('database.default');

        if ($connection === 'sqlite') {
            return true;
        }

        $host = mb_strtolower((string) config("database.connections.{$connection}.host"));

        if (! in_array($host, ['127.0.0.1', 'localhost', '::1'], true)) {
            $this->error('El comando está bloqueado porque la base configurada no es local.');

            return false;
        }

        return true;
    }

    private function firmaBaseDeDatos(): string
    {
        $connection = (string) config('database.default');
        $latest = Pacientes::query()->max('updated_at');

        return hash('sha256', json_encode([
            'connection' => $connection,
            'database' => config("database.connections.{$connection}.database"),
            'host' => config("database.connections.{$connection}.host"),
            'count' => Pacientes::query()->count(),
            'max_id' => Pacientes::query()->max('id'),
            'latest_update' => $latest,
        ], JSON_THROW_ON_ERROR));
    }

    private function guardarComprobante(string $hash, bool $includeReview, string $databaseSignature): void
    {
        File::ensureDirectoryExists(dirname($this->rutaComprobante()));
        File::put($this->rutaComprobante(), json_encode([
            'file_hash' => $hash,
            'include_review' => $includeReview,
            'database_signature' => $databaseSignature,
            'created_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }

    private function comprobanteValido(string $hash, bool $includeReview, string $databaseSignature): bool
    {
        if (! File::isFile($this->rutaComprobante())) {
            return false;
        }

        try {
            $receipt = json_decode(File::get($this->rutaComprobante()), true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return false;
        }

        return hash_equals((string) ($receipt['file_hash'] ?? ''), $hash)
            && ($receipt['include_review'] ?? null) === $includeReview
            && hash_equals((string) ($receipt['database_signature'] ?? ''), $databaseSignature);
    }

    private function rutaComprobante(): string
    {
        return storage_path('app/private/importaciones/pacientes-legacy-preflight.json');
    }

    private function mostrarResultado(array $resultado, bool $commit, int $insertados, int $erroresExtra = 0): void
    {
        foreach (array_slice($resultado['errores'], 0, 20) as $error) {
            $this->warn("Legacy {$error['legacy_id']}: {$error['mensaje']}");
        }

        $remaining = count($resultado['errores']) - 20;

        if ($remaining > 0) {
            $this->warn("Se omitieron {$remaining} mensajes adicionales para proteger los datos personales.");
        }

        $this->table(
            ['Métrica', 'Total'],
            [
                ['Modo', $commit ? 'IMPORTACIÓN LOCAL' : 'SIMULACIÓN'],
                ['Registros leídos', $resultado['leidos']],
                ['Registros que podrían insertarse', count($resultado['candidatos'])],
                ['Registros insertados', $insertados],
                ['Omitidos por revisión', $resultado['omitidos_revision']],
                ['Duplicados existentes', $resultado['duplicados_existentes']],
                ['Duplicados dentro del CSV', $resultado['duplicados_archivo']],
                ['Registros inválidos', $resultado['invalidos']],
                ['Errores encontrados', count($resultado['errores']) + $erroresExtra],
            ]
        );
    }
}
