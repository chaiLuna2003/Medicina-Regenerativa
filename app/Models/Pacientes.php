<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pacientes extends Model
{

    /**
     * Catálogo oficial de sexos.
     *
     * Los identificadores se almacenan en la base de datos
     * y las etiquetas se muestran en la interfaz.
     */
    public const SEXOS = [
        'masculino' => 'Masculino',
        'femenino' => 'Femenino',
    ];


    public const ESTADOS_CIVILES = [
        'soltero' => 'Soltero(a)',
        'casado' => 'Casado(a)',
        'union_libre' => 'Unión libre',
        'divorciado' => 'Divorciado(a)',
        'separado' => 'Separado(a)',
        'viudo' => 'Viudo(a)',
        'no_especificado' => 'No especificado',
    ];

    public const ESCOLARIDADES = [
        'sin_escolaridad' => 'Sin escolaridad',
        'primaria' => 'Primaria',
        'secundaria' => 'Secundaria',
        'bachillerato' => 'Bachillerato',
        'tecnico' => 'Carrera técnica',
        'licenciatura' => 'Licenciatura',
        'posgrado' => 'Posgrado',
        'no_especificado' => 'No especificado',
    ];

    public const TIPOS_SANGRE = [
        'A+' => 'A+',
        'A-' => 'A-',
        'B+' => 'B+',
        'B-' => 'B-',
        'AB+' => 'AB+',
        'AB-' => 'AB-',
        'O+' => 'O+',
        'O-' => 'O-',
        'desconocido' => 'Desconocido',
    ];
    /**
     * Catálogo oficial de categorías de pacientes.
     */
    public const CATEGORIAS = [
        'sin_categoria' => [
            'etiqueta' => 'Sin categoría',
            'fondo' => '#F1F5F9',
            'texto' => '#475569',
            'borde' => '#CBD5E1',
        ],

        'rotarios' => [
            'etiqueta' => 'Rotarios',
            'fondo' => '#F3E8FF',
            'texto' => '#7E22CE',
            'borde' => '#D8B4FE',
        ],

        'unidem' => [
            'etiqueta' => 'UNIDEM',
            'fondo' => '#E0F2FE',
            'texto' => '#0369A1',
            'borde' => '#7DD3FC',
        ],

        'alumnos_cucs' => [
            'etiqueta' => 'Alumnos CUCS',
            'fondo' => '#DBEAFE',
            'texto' => '#1D4ED8',
            'borde' => '#93C5FD',
        ],

        'trabajadores' => [
            'etiqueta' => 'Trabajadores',
            'fondo' => '#FCE7F3',
            'texto' => '#BE185D',
            'borde' => '#F9A8D4',
        ],

        'rotarios_20' => [
            'etiqueta' => 'Rotarios 20%',
            'fondo' => '#FFE4E6',
            'texto' => '#BE123C',
            'borde' => '#FDA4AF',
        ],

        'donativo' => [
            'etiqueta' => 'Donativo',
            'fondo' => '#CCFBF1',
            'texto' => '#0F766E',
            'borde' => '#5EEAD4',
        ],

        'medicos_50' => [
            'etiqueta' => 'Médicos 50% desc.',
            'fondo' => '#FEF3C7',
            'texto' => '#A16207',
            'borde' => '#FCD34D',
        ],

        'unidem_20' => [
            'etiqueta' => 'UNIDEM 20%',
            'fondo' => '#99F6E4',
            'texto' => '#115E59',
            'borde' => '#2DD4BF',
        ],
    ];

    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nacimiento',

        /*
    |--------------------------------------------------------------------------
    | Clasificación
    |--------------------------------------------------------------------------
    */

        'sexo',
        'categoria',

        /*
    |--------------------------------------------------------------------------
    | Contacto
    |--------------------------------------------------------------------------
    */

        'telefono',
        'telefono_fijo',
        'telefono_secundario',
        'email',

        /*
    |--------------------------------------------------------------------------
    | Ubicación
    |--------------------------------------------------------------------------
    */

        'domicilio',
        'ciudad',
        'estado',
        'codigo_postal',
        'lugar_nacimiento',

        /*
    |--------------------------------------------------------------------------
    | Información complementaria
    |--------------------------------------------------------------------------
    */

        'ocupacion',
        'religion',
        'estado_civil',
        'escolaridad',
        'tipo_sangre',
        'alergias',
        'costo_consulta_personalizado',
        'finado',

        /*
    |--------------------------------------------------------------------------
    | Campos existentes
    |--------------------------------------------------------------------------
    */

        'notas',
        'foto',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',

            'costo_consulta_personalizado' =>
            'decimal:2',

            'finado' => 'boolean',
            'status' => 'boolean',
        ];
    }

    protected function sexoTexto(): Attribute
    {
        return Attribute::make(
            get: fn(): string =>
            self::SEXOS[$this->sexo]
                ?? 'No especificado'
        );
    }

    protected function categoriaTexto(): Attribute
    {
        return Attribute::make(
            get: fn(): string =>
            self::CATEGORIAS[$this->categoria
                ?? 'sin_categoria']['etiqueta']
                ?? 'Sin categoría'
        );
    }

    protected function categoriaEstilo(): Attribute
    {
        return Attribute::make(
            get: fn(): array =>
            self::CATEGORIAS[$this->categoria
                ?? 'sin_categoria']
                ?? self::CATEGORIAS['sin_categoria']
        );
    }

    public function fotoUrl(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/default.webp');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Citas::class, 'paciente_id');
    }

    /**
     * Casos clínicos abiertos para el paciente.
     */
    public function casosClinicos(): HasMany
    {
        return $this->hasMany(
            CasoClinico::class,
            'paciente_id'
        );
    }

    /**
     * Registros de evolución clínica del paciente.
     */
    public function evolucionesClinicas(): HasMany
    {
        return $this->hasMany(
            EvolucionClinica::class,
            'paciente_id'
        );
    }

    public function signosVitales(): HasMany
    {
        return $this->hasMany(SignoVital::class, 'paciente_id');
    }

    public function historiaClinica(): HasOne
    {
        return $this->hasOne(
            HistoriaClinica::class,
            'paciente_id'
        );
    }

    protected function edad(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (!$this->fecha_nacimiento) {
                    return null;
                }

                $nacimiento = Carbon::parse(
                    $this->fecha_nacimiento
                )->startOfDay();

                $hoy = now();

                if ($nacimiento->isFuture()) {
                    return null;
                }

                $anios = (int) floor(
                    $nacimiento->diffInYears($hoy)
                );

                if ($anios >= 1) {
                    return $anios === 1
                        ? '1 año'
                        : "{$anios} años";
                }

                $meses = (int) floor(
                    $nacimiento->diffInMonths($hoy)
                );

                if ($meses >= 1) {
                    return $meses === 1
                        ? '1 mes'
                        : "{$meses} meses";
                }

                $dias = (int) floor(
                    $nacimiento->diffInDays($hoy)
                );

                if ($dias >= 14) {
                    $semanas = intdiv($dias, 7);

                    return $semanas === 1
                        ? '1 semana'
                        : "{$semanas} semanas";
                }

                if ($dias >= 1) {
                    return $dias === 1
                        ? '1 día de nacido'
                        : "{$dias} días de nacido";
                }

                return 'Recién nacido';
            }
        );
    }

    /**
     * Historial de recetas médicas del paciente a través de sus citas.
     */
    public function recetas(): HasManyThrough
    {
        return $this->hasManyThrough(
            Receta::class,
            Citas::class,
            'paciente_id', // Llave foránea en la tabla citas
            'cita_id',     // Llave foránea en la tabla recetas
            'id',          // Llave primaria en pacientes
            'id'           // Llave primaria en citas
        );
    }

    public function estudios(): HasManyThrough
    {
        return $this->hasManyThrough(
            Estudio::class,
            Citas::class,
            'paciente_id',
            'cita_id',
            'id',
            'id'
        );
    }
}
