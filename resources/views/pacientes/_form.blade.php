@php
$edicionLimitadaRecepcion =
isset($pacientes)
&& auth()->user()->isRecepcionista();

$esEdicion = isset($pacientes);
$sexosPaciente =
\App\Models\Pacientes::SEXOS;

$categoriasPaciente =
\App\Models\Pacientes::CATEGORIAS;

$estadosCivilesPaciente =
\App\Models\Pacientes::ESTADOS_CIVILES;

$escolaridadesPaciente =
\App\Models\Pacientes::ESCOLARIDADES;

$tiposSangrePaciente =
\App\Models\Pacientes::TIPOS_SANGRE;
@endphp


{{-- ===================================================== --}}
{{-- FOTO --}}
{{-- ===================================================== --}}

<section class="mb-8">
    <div class="mb-4">
        <h3 class="text-base font-semibold text-slate-900">
            Fotografía
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Imagen de identificación del paciente.
        </p>
    </div>

    <div
        class="flex flex-col gap-5
                   rounded-2xl border border-slate-200
                   bg-slate-50 p-5
                   sm:flex-row sm:items-center">
        <img
            id="preview-foto"
            src="{{ $esEdicion
                    ? $pacientes->fotoUrl()
                    : asset('images/default.webp') }}"
            alt="Vista previa de la fotografía"
            class="h-24 w-24 shrink-0
                       rounded-2xl border border-slate-200
                       bg-white object-cover shadow-sm">

        <div class="min-w-0 flex-1">
            <label
                for="foto"
                class="block text-sm font-medium
                           text-slate-700">
                Foto del paciente
            </label>

            <input
                id="foto"
                type="file"
                name="foto"
                accept="image/*"
                capture="environment"
                class="mt-2 block w-full text-sm
                           text-slate-600
                           file:mr-4 file:rounded-lg
                           file:border-0
                           file:bg-blue-50
                           file:px-4 file:py-2
                           file:text-sm
                           file:font-semibold
                           file:text-blue-700
                           hover:file:bg-blue-100">

            <p class="mt-2 text-xs text-slate-400">
                JPG, PNG o WEBP. Máximo 4 MB.
            </p>

            @error('foto')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>
</section>


{{-- ===================================================== --}}
{{-- DATOS GENERALES --}}
{{-- ===================================================== --}}

<section class="mb-8">
    <div class="mb-4">
        <h3 class="text-base font-semibold text-slate-900">
            Datos generales
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Información personal del paciente.
        </p>
    </div>

    @if ($edicionLimitadaRecepcion)

    <div
        class="rounded-2xl border
                   border-slate-200
                   bg-slate-50 p-5">
        <div class="flex items-center gap-4">

            <img
                src="{{ $pacientes->fotoUrl() }}"
                alt="Foto de {{ $pacientes->nombre }}"
                class="h-16 w-16 shrink-0
                           rounded-xl border
                           border-slate-200 object-cover">

            <div>
                <p
                    class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                    Paciente
                </p>

                <p
                    class="mt-1 text-lg font-semibold
                               text-slate-900">
                    {{ $pacientes->nombre }}
                    {{ $pacientes->apellido }}
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $pacientes->edad ?? 'Edad no disponible' }}
                </p>
            </div>
        </div>

        <p class="mt-4 text-sm text-slate-500">
            Recepción puede actualizar los datos administrativos.
        </p>

        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="categoria" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Categoría
                </label>
                <select id="categoria" name="categoria" required
                    class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($categoriasPaciente as $valor => $configuracion)
                        <option value="{{ $valor }}" @selected(old('categoria', $pacientes->categoria ?? 'sin_categoria') === $valor)>
                            {{ $configuracion['etiqueta'] }}
                        </option>
                    @endforeach
                </select>
                @error('categoria')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Estado del paciente
                </label>
                <select id="status" name="status" required
                    class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="1" @selected(old('status', $pacientes->status ?? 1) == 1)>Activo</option>
                    <option value="0" @selected(old('status', $pacientes->status ?? 1) == 0)>Inactivo</option>
                </select>
                @error('status')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    @else

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

        {{-- Nombre --}}
        <div>
            <label
                for="nombre"
                class="mb-1.5 block
                           text-sm font-medium
                           text-slate-700">
                Nombre
            </label>

            <input
                id="nombre"
                type="text"
                name="nombre"
                value="{{ old(
                        'nombre',
                        $pacientes->nombre ?? ''
                    ) }}"
                required
                maxlength="255"
                autocomplete="given-name"
                class="block w-full rounded-xl
                           border-slate-300 text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500">

            @error('nombre')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

        {{-- Apellidos --}}
        <div>
            <label
                for="apellido"
                class="mb-1.5 block
                           text-sm font-medium
                           text-slate-700">
                Apellidos
            </label>

            <input
                id="apellido"
                type="text"
                name="apellido"
                value="{{ old(
                        'apellido',
                        $pacientes->apellido ?? ''
                    ) }}"
                required
                maxlength="255"
                autocomplete="family-name"
                class="block w-full rounded-xl
                           border-slate-300 text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500">

            @error('apellido')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

        {{-- Fecha de nacimiento --}}
        <div>
            <label
                for="fecha_nacimiento"
                class="mb-1.5 block
                           text-sm font-medium
                           text-slate-700">
                Fecha de nacimiento
            </label>

            <input
                id="fecha_nacimiento"
                type="date"
                name="fecha_nacimiento"
                value="{{ old(
                        'fecha_nacimiento',
                        isset($pacientes)
                            && $pacientes->fecha_nacimiento
                                ? $pacientes
                                    ->fecha_nacimiento
                                    ->format('Y-m-d')
                                : ''
                    ) }}"
                max="{{ now()->format('Y-m-d') }}"
                required
                class="block w-full rounded-xl
                           border-slate-300 text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500">

            <p
                id="edad_calculada"
                class="mt-2 text-sm text-slate-500">
                Selecciona la fecha para calcular la edad.
            </p>

            @error('fecha_nacimiento')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

        {{-- Sexo --}}
        <div>
            <label
                for="sexo"
                class="mb-1.5 block text-sm
               font-medium text-slate-700">
                Sexo
            </label>

            <select
                id="sexo"
                name="sexo"
                required
                class="block w-full rounded-xl
               border-slate-300 text-sm shadow-sm
               focus:border-blue-500
               focus:ring-blue-500">

                <option value="">
                    Selecciona una opción
                </option>

                @foreach (
                $sexosPaciente
                as $valor => $etiqueta
                )
                <option
                    value="{{ $valor }}"
                    @selected(
                    old( 'sexo' ,
                    $pacientes->sexo ?? ''
                    ) === $valor
                    )>
                    {{ $etiqueta }}
                </option>
                @endforeach
            </select>

            @error('sexo')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

        {{-- Categoría --}}
        <div>
            <label
                for="categoria"
                class="mb-1.5 block text-sm
               font-medium text-slate-700">
                Categoría
            </label>

            <select
                id="categoria"
                name="categoria"
                class="block w-full rounded-xl
               border-slate-300 text-sm shadow-sm
               focus:border-blue-500
               focus:ring-blue-500">

                @foreach (
                $categoriasPaciente
                as $valor => $configuracion
                )
                <option
                    value="{{ $valor }}"
                    @selected(
                    old( 'categoria' ,
                    $pacientes->categoria
                    ?? 'sin_categoria'
                    ) === $valor
                    )>
                    {{ $configuracion['etiqueta'] }}
                </option>
                @endforeach
            </select>

            @error('categoria')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

        {{-- Status --}}
        <div>
            <label
                for="status"
                class="mb-1.5 block
                           text-sm font-medium
                           text-slate-700">
                Estado del paciente
            </label>

            <select
                id="status"
                name="status"
                class="block w-full rounded-xl
                           border-slate-300 text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500">
                <option
                    value="1"
                    @selected(
                    old( 'status' ,
                    $pacientes->status ?? 1
                    ) == 1
                    )
                    >
                    Activo
                </option>

                <option
                    value="0"
                    @selected(
                    old( 'status' ,
                    $pacientes->status ?? 1
                    ) == 0
                    )
                    >
                    Inactivo
                </option>
            </select>

            <p class="mt-2 text-xs text-slate-400">
                Los pacientes nuevos se registran activos
                por defecto.
            </p>

            @error('status')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

    </div>

    @endif
</section>


{{-- ===================================================== --}}
{{-- CONTACTO --}}
{{-- ===================================================== --}}

<section class="mb-8">
    <div class="mb-4">
        <h3 class="text-base font-semibold text-slate-900">
            Información de contacto
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Datos utilizados para comunicación y seguimiento.
        </p>
    </div>

    <div
    class="grid grid-cols-1 gap-5
           sm:grid-cols-2
           md:grid-cols-3">

    {{-- Celular / WhatsApp --}}
    @unless(auth()->user()->isMedico())
    <div>
        <label
            for="telefono"
            class="mb-1.5 block
                   text-sm font-medium
                   text-slate-700">
            Celular / WhatsApp
        </label>

        <input
            id="telefono"
            type="text"
            name="telefono"
            value="{{ old(
                'telefono',
                $pacientes->telefono ?? ''
            ) }}"
            maxlength="20"
            autocomplete="tel"
            placeholder="Ej. 55 1234 5678"
            class="block w-full rounded-xl
                   border-slate-300 text-sm
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500">

        @error('telefono')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Teléfono fijo --}}
    <div>
        <label
            for="telefono_fijo"
            class="mb-1.5 block
                   text-sm font-medium
                   text-slate-700">
            Teléfono
        </label>

        <input
            id="telefono_fijo"
            type="text"
            name="telefono_fijo"
            value="{{ old(
                'telefono_fijo',
                $pacientes->telefono_fijo ?? ''
            ) }}"
            maxlength="20"
            autocomplete="tel"
            class="block w-full rounded-xl
                   border-slate-300 text-sm
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500">

        @error('telefono_fijo')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Teléfono secundario --}}
    <div>
        <label
            for="telefono_secundario"
            class="mb-1.5 block
                   text-sm font-medium
                   text-slate-700">
            Teléfono secundario
        </label>

        <input
            id="telefono_secundario"
            type="text"
            name="telefono_secundario"
            value="{{ old(
                'telefono_secundario',
                $pacientes->telefono_secundario ?? ''
            ) }}"
            maxlength="20"
            autocomplete="tel"
            class="block w-full rounded-xl
                   border-slate-300 text-sm
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500">

        @error('telefono_secundario')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    @endunless

    {{-- Correo --}}
    <div class="md:col-span-2">
        <label
            for="email"
            class="mb-1.5 block
                   text-sm font-medium
                   text-slate-700">
            Correo electrónico
        </label>

        <input
            id="email"
            type="email"
            name="email"
            value="{{ old(
                'email',
                $pacientes->email ?? ''
            ) }}"
            maxlength="255"
            autocomplete="email"
            placeholder="paciente@correo.com"
            class="block w-full rounded-xl
                   border-slate-300 text-sm
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500">

        @error('email')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>
</div>
</section>

{{-- ===================================================== --}}
{{-- DOMICILIO --}}
{{-- ===================================================== --}}

<section class="mb-8">
    <div class="mb-4">
        <h3 class="text-base font-semibold text-slate-900">
            Domicilio
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Información de ubicación del paciente.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

        <div class="sm:col-span-2">
            <label
                for="domicilio"
                class="mb-1.5 block text-sm
                       font-medium text-slate-700">
                Domicilio
            </label>

            <input
                id="domicilio"
                type="text"
                name="domicilio"
                value="{{ old(
                    'domicilio',
                    $pacientes->domicilio ?? ''
                ) }}"
                maxlength="500"
                class="block w-full rounded-xl
                       border-slate-300 text-sm shadow-sm
                       focus:border-blue-500
                       focus:ring-blue-500">

            @error('domicilio')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

        @foreach ([
        'ciudad' => 'Ciudad',
        'estado' => 'Estado',
        'codigo_postal' => 'Código postal',
        'lugar_nacimiento' => 'Lugar de nacimiento',
        ] as $campo => $etiqueta)

        <div>
            <label
                for="{{ $campo }}"
                class="mb-1.5 block text-sm
                           font-medium text-slate-700">
                {{ $etiqueta }}
            </label>

            <input
                id="{{ $campo }}"
                type="text"
                name="{{ $campo }}"
                value="{{ old(
                        $campo,
                        $pacientes->{$campo} ?? ''
                    ) }}"
                maxlength="{{ $campo === 'codigo_postal'
                        ? 10
                        : 200 }}"
                class="block w-full rounded-xl
                           border-slate-300 text-sm shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500">

            @error($campo)
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>
        @endforeach
    </div>
</section>

{{-- ===================================================== --}}
{{-- INFORMACIÓN COMPLEMENTARIA --}}
{{-- ===================================================== --}}

<section class="mb-8">
    <div class="mb-4">
        <h3 class="text-base font-semibold text-slate-900">
            Información complementaria
        </h3>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

        @foreach ([
        'ocupacion' => 'Ocupación',
        'religion' => 'Religión',
        ] as $campo => $etiqueta)

        <div>
            <label
                for="{{ $campo }}"
                class="mb-1.5 block text-sm
                           font-medium text-slate-700">
                {{ $etiqueta }}
            </label>

            <input
                id="{{ $campo }}"
                type="text"
                name="{{ $campo }}"
                value="{{ old(
                        $campo,
                        $pacientes->{$campo} ?? ''
                    ) }}"
                maxlength="200"
                class="block w-full rounded-xl
                           border-slate-300 text-sm shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500">

            @error($campo)
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>
        @endforeach

        {{-- Estado civil --}}
        <div>
            <label
                for="estado_civil"
                class="mb-1.5 block text-sm font-medium text-slate-700">
                Estado civil
            </label>

            <select
                id="estado_civil"
                name="estado_civil"
                class="block w-full rounded-xl border-slate-300
                       text-sm shadow-sm focus:border-blue-500
                       focus:ring-blue-500">

                <option value="">Selecciona una opción</option>

                @foreach ($estadosCivilesPaciente as $valor => $etiqueta)
                    <option
                        value="{{ $valor }}"
                        @selected(
                            old(
                                'estado_civil',
                                $pacientes->estado_civil ?? ''
                            ) === $valor
                        )>
                        {{ $etiqueta }}
                    </option>
                @endforeach
            </select>

            @error('estado_civil')
                <p class="mt-1.5 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Escolaridad --}}
        <div>
            <label
                for="escolaridad"
                class="mb-1.5 block text-sm font-medium text-slate-700">
                Escolaridad
            </label>

            <select
                id="escolaridad"
                name="escolaridad"
                class="block w-full rounded-xl border-slate-300
                       text-sm shadow-sm focus:border-blue-500
                       focus:ring-blue-500">

                <option value="">Selecciona una opción</option>

                @foreach ($escolaridadesPaciente as $valor => $etiqueta)
                    <option
                        value="{{ $valor }}"
                        @selected(
                            old(
                                'escolaridad',
                                $pacientes->escolaridad ?? ''
                            ) === $valor
                        )>
                        {{ $etiqueta }}
                    </option>
                @endforeach
            </select>

            @error('escolaridad')
                <p class="mt-1.5 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Tipo de sangre --}}
        <div>
            <label
                for="tipo_sangre"
                class="mb-1.5 block text-sm font-medium text-slate-700">
                Tipo de sangre
            </label>

            <select
                id="tipo_sangre"
                name="tipo_sangre"
                class="block w-full rounded-xl border-slate-300
                       text-sm shadow-sm focus:border-blue-500
                       focus:ring-blue-500">

                <option value="">Selecciona una opción</option>

                @foreach ($tiposSangrePaciente as $valor => $etiqueta)
                    <option
                        value="{{ $valor }}"
                        @selected(
                            old(
                                'tipo_sangre',
                                $pacientes->tipo_sangre ?? ''
                            ) === $valor
                        )>
                        {{ $etiqueta }}
                    </option>
                @endforeach
            </select>

            @error('tipo_sangre')
                <p class="mt-1.5 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Alergias --}}
        <div class="sm:col-span-2">
            <label
                for="alergias"
                class="mb-1.5 block text-sm font-medium text-slate-700">
                Alergias
            </label>

            <textarea
                id="alergias"
                name="alergias"
                rows="3"
                maxlength="2000"
                placeholder="Medicamentos, alimentos u otras alergias conocidas"
                class="block w-full rounded-xl border-slate-300
                       text-sm shadow-sm focus:border-blue-500
                       focus:ring-blue-500">{{ old(
                            'alergias',
                            $pacientes->alergias ?? ''
                        ) }}</textarea>

            @error('alergias')
                <p class="mt-1.5 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="costo_consulta_personalizado"
                class="mb-1.5 block text-sm
                       font-medium text-slate-700">
                Costo de consulta personalizado
            </label>

            <input
                id="costo_consulta_personalizado"
                type="number"
                name="costo_consulta_personalizado"
                value="{{ old(
                    'costo_consulta_personalizado',
                    $pacientes
                        ->costo_consulta_personalizado
                        ?? ''
                ) }}"
                min="0"
                max="99999999.99"
                step="0.01"
                class="block w-full rounded-xl
                       border-slate-300 text-sm shadow-sm
                       focus:border-blue-500
                       focus:ring-blue-500">

            @error('costo_consulta_personalizado')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="flex items-center">
            <input
                id="finado"
                type="checkbox"
                name="finado"
                value="1"
                @checked(
                old( 'finado' ,
                $pacientes->finado ?? false
            )
            )
            class="h-4 w-4 rounded
            border-slate-300 text-blue-600
            focus:ring-blue-500">

            <label
                for="finado"
                class="ml-3 text-sm font-medium
                       text-slate-700">
                Paciente finado
            </label>
        </div>
    </div>
</section>


{{-- ===================================================== --}}
{{-- NOTAS --}}
{{-- ===================================================== --}}

<section>
    <div class="mb-4">
        <h3 class="text-base font-semibold text-slate-900">
            Notas
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Observaciones generales relevantes
            para el expediente administrativo.
        </p>
    </div>

    <textarea
        id="notas"
        name="notas"
        rows="5"
        maxlength="5000"
        placeholder="Observaciones generales del paciente..."
        class="block w-full resize-y
                   rounded-xl border-slate-300
                   text-sm shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500">{{ old('notas', $pacientes->notas ?? '') }}</textarea>

    <div class="mt-2 flex items-center justify-between">
        <p class="text-xs text-slate-400">
            Evita registrar información innecesaria.
        </p>

        <p
            id="contador-notas"
            class="text-xs text-slate-400">
            0 / 5000
        </p>
    </div>

    @error('notas')
    <p class="mt-1.5 text-sm text-red-600">
        {{ $message }}
    </p>
    @enderror
</section>


{{-- ===================================================== --}}
{{-- JAVASCRIPT --}}
{{-- ===================================================== --}}

<script>
    document.addEventListener(
        'DOMContentLoaded',
        () => {
            /*
            |--------------------------------------------------------------------------
            | Vista previa de fotografía
            |--------------------------------------------------------------------------
            */

            const fotoInput =
                document.getElementById('foto');

            const fotoPreview =
                document.getElementById('preview-foto');

            fotoInput?.addEventListener(
                'change',
                function() {
                    const archivo = this.files?.[0];

                    if (!archivo || !fotoPreview) {
                        return;
                    }

                    fotoPreview.src =
                        URL.createObjectURL(archivo);
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Cálculo visual de edad
            |--------------------------------------------------------------------------
            */

            const fechaInput =
                document.getElementById(
                    'fecha_nacimiento'
                );

            const edadTexto =
                document.getElementById(
                    'edad_calculada'
                );

            function calcularEdad() {
                if (!fechaInput || !edadTexto) {
                    return;
                }

                if (!fechaInput.value) {
                    edadTexto.textContent =
                        'Selecciona la fecha para calcular la edad.';

                    edadTexto.className =
                        'mt-2 text-sm text-slate-500';

                    return;
                }

                const [anio, mes, dia] =
                fechaInput.value
                    .split('-')
                    .map(Number);

                const nacimiento =
                    new Date(
                        anio,
                        mes - 1,
                        dia
                    );

                const hoy = new Date();

                nacimiento.setHours(
                    0,
                    0,
                    0,
                    0
                );

                hoy.setHours(
                    0,
                    0,
                    0,
                    0
                );

                if (nacimiento > hoy) {
                    edadTexto.textContent =
                        'La fecha de nacimiento no puede ser futura.';

                    edadTexto.className =
                        'mt-2 text-sm font-medium text-red-600';

                    return;
                }

                let anios =
                    hoy.getFullYear() -
                    nacimiento.getFullYear();

                const aunNoCumple =
                    hoy.getMonth() <
                    nacimiento.getMonth() ||
                    (
                        hoy.getMonth() ===
                        nacimiento.getMonth() &&
                        hoy.getDate() <
                        nacimiento.getDate()
                    );

                if (aunNoCumple) {
                    anios--;
                }

                let descripcion;

                if (anios >= 1) {
                    descripcion =
                        anios === 1 ?
                        '1 año' :
                        `${anios} años`;
                } else {
                    let meses =
                        (
                            hoy.getFullYear() -
                            nacimiento.getFullYear()
                        ) * 12 +
                        hoy.getMonth() -
                        nacimiento.getMonth();

                    if (
                        hoy.getDate() <
                        nacimiento.getDate()
                    ) {
                        meses--;
                    }

                    if (meses >= 1) {
                        descripcion =
                            meses === 1 ?
                            '1 mes' :
                            `${meses} meses`;
                    } else {
                        const milisegundosPorDia =
                            1000 *
                            60 *
                            60 *
                            24;

                        const dias =
                            Math.floor(
                                (
                                    hoy -
                                    nacimiento
                                ) /
                                milisegundosPorDia
                            );

                        if (dias >= 14) {
                            const semanas =
                                Math.floor(
                                    dias / 7
                                );

                            descripcion =
                                semanas === 1 ?
                                '1 semana' :
                                `${semanas} semanas`;
                        } else if (dias >= 1) {
                            descripcion =
                                dias === 1 ?
                                '1 día de nacido' :
                                `${dias} días de nacido`;
                        } else {
                            descripcion =
                                'Recién nacido';
                        }
                    }
                }

                edadTexto.textContent =
                    `Edad calculada: ${descripcion}`;

                edadTexto.className =
                    'mt-2 text-sm font-medium text-blue-600';
            }

            fechaInput?.addEventListener(
                'input',
                calcularEdad
            );

            calcularEdad();


            /*
            |--------------------------------------------------------------------------
            | Contador de notas
            |--------------------------------------------------------------------------
            */

            const notas =
                document.getElementById('notas');

            const contador =
                document.getElementById(
                    'contador-notas'
                );

            function actualizarContadorNotas() {
                if (!notas || !contador) {
                    return;
                }

                contador.textContent =
                    `${notas.value.length} / 5000`;
            }

            notas?.addEventListener(
                'input',
                actualizarContadorNotas
            );

            actualizarContadorNotas();
        }
    );
</script>