@php
    $edicionLimitadaRecepcion =
        isset($pacientes)
        && auth()->user()->isRecepcionista();

    $esEdicion = isset($pacientes);
@endphp


{{-- ===================================================== --}}
{{-- FOTO --}}
{{-- ===================================================== --}}

@unless ($edicionLimitadaRecepcion)
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
                   sm:flex-row sm:items-center"
        >
            <img
                id="preview-foto"
                src="{{ $esEdicion
                    ? $pacientes->fotoUrl()
                    : asset('images/default.webp') }}"
                alt="Vista previa de la fotografía"
                class="h-24 w-24 shrink-0
                       rounded-2xl border border-slate-200
                       bg-white object-cover shadow-sm"
            >

            <div class="min-w-0 flex-1">
                <label
                    for="foto"
                    class="block text-sm font-medium
                           text-slate-700"
                >
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
                           hover:file:bg-blue-100"
                >

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
@endunless


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
                   bg-slate-50 p-5"
        >
            <div class="flex items-center gap-4">

                <img
                    src="{{ $pacientes->fotoUrl() }}"
                    alt="Foto de {{ $pacientes->nombre }}"
                    class="h-16 w-16 shrink-0
                           rounded-xl border
                           border-slate-200 object-cover"
                >

                <div>
                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400"
                    >
                        Paciente
                    </p>

                    <p
                        class="mt-1 text-lg font-semibold
                               text-slate-900"
                    >
                        {{ $pacientes->nombre }}
                        {{ $pacientes->apellido }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $pacientes->edad ?? 'Edad no disponible' }}
                    </p>
                </div>
            </div>

            <p class="mt-4 text-sm text-slate-500">
                Recepción puede modificar únicamente
                la información de contacto.
            </p>
        </div>

    @else

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

            {{-- Nombre --}}
            <div>
                <label
                    for="nombre"
                    class="mb-1.5 block
                           text-sm font-medium
                           text-slate-700"
                >
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
                           focus:ring-blue-500"
                >

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
                           text-slate-700"
                >
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
                           focus:ring-blue-500"
                >

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
                           text-slate-700"
                >
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
                           focus:ring-blue-500"
                >

                <p
                    id="edad_calculada"
                    class="mt-2 text-sm text-slate-500"
                >
                    Selecciona la fecha para calcular la edad.
                </p>

                @error('fecha_nacimiento')
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
                           text-slate-700"
                >
                    Estado del paciente
                </label>

                <select
                    id="status"
                    name="status"
                    class="block w-full rounded-xl
                           border-slate-300 text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500"
                >
                    <option
                        value="1"
                        @selected(
                            old(
                                'status',
                                $pacientes->status ?? 1
                            ) == 1
                        )
                    >
                        Activo
                    </option>

                    <option
                        value="0"
                        @selected(
                            old(
                                'status',
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

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

        {{-- Teléfono --}}
        <div>
            <label
                for="telefono"
                class="mb-1.5 block
                       text-sm font-medium
                       text-slate-700"
            >
                Teléfono
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
                       focus:ring-blue-500"
            >

            @error('telefono')
                <p class="mt-1.5 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Correo --}}
        <div>
            <label
                for="email"
                class="mb-1.5 block
                       text-sm font-medium
                       text-slate-700"
            >
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
                       focus:ring-blue-500"
            >

            @error('email')
                <p class="mt-1.5 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

    </div>
</section>


{{-- ===================================================== --}}
{{-- NOTAS --}}
{{-- ===================================================== --}}

@unless ($edicionLimitadaRecepcion)
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
                   focus:ring-blue-500"
        >{{ old('notas', $pacientes->notas ?? '') }}</textarea>

        <div class="mt-2 flex items-center justify-between">
            <p class="text-xs text-slate-400">
                Evita registrar información innecesaria.
            </p>

            <p
                id="contador-notas"
                class="text-xs text-slate-400"
            >
                0 / 5000
            </p>
        </div>

        @error('notas')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </section>
@endunless


{{-- ===================================================== --}}
{{-- JAVASCRIPT --}}
{{-- ===================================================== --}}

@unless ($edicionLimitadaRecepcion)

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
                    function () {
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
                        hoy.getFullYear()
                        - nacimiento.getFullYear();

                    const aunNoCumple =
                        hoy.getMonth()
                            < nacimiento.getMonth()
                        || (
                            hoy.getMonth()
                                === nacimiento.getMonth()
                            && hoy.getDate()
                                < nacimiento.getDate()
                        );

                    if (aunNoCumple) {
                        anios--;
                    }

                    let descripcion;

                    if (anios >= 1) {
                        descripcion =
                            anios === 1
                                ? '1 año'
                                : `${anios} años`;
                    } else {
                        let meses =
                            (
                                hoy.getFullYear()
                                - nacimiento.getFullYear()
                            ) * 12
                            + hoy.getMonth()
                            - nacimiento.getMonth();

                        if (
                            hoy.getDate()
                            < nacimiento.getDate()
                        ) {
                            meses--;
                        }

                        if (meses >= 1) {
                            descripcion =
                                meses === 1
                                    ? '1 mes'
                                    : `${meses} meses`;
                        } else {
                            const milisegundosPorDia =
                                1000
                                * 60
                                * 60
                                * 24;

                            const dias =
                                Math.floor(
                                    (
                                        hoy
                                        - nacimiento
                                    )
                                    / milisegundosPorDia
                                );

                            if (dias >= 14) {
                                const semanas =
                                    Math.floor(
                                        dias / 7
                                    );

                                descripcion =
                                    semanas === 1
                                        ? '1 semana'
                                        : `${semanas} semanas`;
                            } else if (dias >= 1) {
                                descripcion =
                                    dias === 1
                                        ? '1 día de nacido'
                                        : `${dias} días de nacido`;
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

@endunless