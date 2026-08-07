<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-600">
                    Panel de enfermería
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Registrar signos vitales
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Captura la valoración previa a la consulta médica.
                </p>
            </div>

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>

                Volver al dashboard
            </a>
        </div>
    </x-slot>

    @php
    $nombrePaciente = trim(
        ($cita->paciente?->nombre ?? '') . ' ' .
        ($cita->paciente?->apellido_paterno ?? $cita->paciente?->apellido ?? '') . ' ' .
        ($cita->paciente?->apellido_materno ?? '')
    );

    $nombreMedico = trim(
        ($cita->medico?->nombre ?? '') . ' ' .
        ($cita->medico?->apellido_paterno ?? '') . ' ' .
        ($cita->medico?->apellido_materno ?? '')
    );

    $fechaCita = $cita->fecha
        ? \Carbon\Carbon::parse($cita->fecha)
            ->locale('es')
            ->translatedFormat('d \d\e F \d\e Y')
        : 'Fecha no disponible';

    $horaCita = $cita->hora
        ? \Carbon\Carbon::parse($cita->hora)->format('H:i')
        : 'Sin hora';
@endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Resumen de errores --}}
            @if ($errors->any())
                <div
                    class="rounded-2xl border border-red-200 bg-red-50 p-5"
                    role="alert"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z"
                                />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-bold text-red-900">
                                Revisa la información capturada
                            </h3>

                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Información de la cita --}}
            <section class="overflow-hidden rounded-2xl bg-gray-900 text-white shadow-sm">
                <div class="relative p-6">
                    <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-emerald-400/10"></div>
                    <div class="absolute -bottom-20 right-24 h-40 w-40 rounded-full bg-blue-400/10"></div>

                    <div class="relative grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div class="md:col-span-2">
                            <p class="text-xs font-bold uppercase tracking-widest text-emerald-300">
                                Paciente
                            </p>

                            <h3 class="mt-2 text-xl font-bold">
                                {{ $nombrePaciente ?: 'Paciente no disponible' }}
                            </h3>

                            @if ($cita->paciente?->fecha_nacimiento)
                                <p class="mt-1 text-sm text-gray-300">
                                    Fecha de nacimiento:
                                    {{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">
                                Fecha y hora
                            </p>

                            <p class="mt-2 font-semibold">
                                {{ $fechaCita }}
                            </p>

                            <p class="mt-1 text-sm text-gray-300">
                                {{ $horaCita }} horas
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">
                                Médico
                            </p>

                            <p class="mt-2 font-semibold">
                                Dr. {{ $nombreMedico ?: 'No asignado' }}
                            </p>

                            @if ($cita->motivo)
                                <p class="mt-1 text-sm text-gray-300">
                                    {{ $cita->motivo }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <form
                id="form-signos-vitales"
                method="POST"
                action="{{ route('signos-vitales.store', $cita) }}"
                class="space-y-6"
            >
                @csrf

                {{-- Medidas corporales --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-gray-100 pb-5">
                        <h3 class="text-lg font-bold text-gray-900">
                            Medidas corporales
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            El peso y la estatura son obligatorios para calcular el IMC.
                        </p>
                    </div>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                        {{-- Peso --}}
                        <div>
                            <label
                                for="peso"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Peso
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative mt-2">
                                <input
                                    id="peso"
                                    name="peso"
                                    type="number"
                                    inputmode="decimal"
                                    min="0.5"
                                    max="500"
                                    step="0.01"
                                    value="{{ old('peso') }}"
                                    required
                                    autofocus
                                    placeholder="Ej. 72.50"
                                    class="block w-full rounded-xl border-gray-300 pr-14 shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500 @error('peso') border-red-400 @enderror"
                                >

                                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-gray-400">
                                    kg
                                </span>
                            </div>

                            @error('peso')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Estatura --}}
                        <div>
                            <label
                                for="estatura"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Estatura
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative mt-2">
                                <input
                                    id="estatura"
                                    name="estatura"
                                    type="number"
                                    inputmode="decimal"
                                    min="20"
                                    max="250"
                                    step="0.01"
                                    value="{{ old('estatura') }}"
                                    required
                                    placeholder="Ej. 175.00"
                                    class="block w-full rounded-xl border-gray-300 pr-14 shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500 @error('estatura') border-red-400 @enderror"
                                >

                                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-gray-400">
                                    cm
                                </span>
                            </div>

                            @error('estatura')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- IMC --}}
                        <div>
                            <span class="block text-sm font-semibold text-gray-700">
                                IMC calculado
                            </span>

                            <div class="mt-2 flex min-h-[42px] items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-2">
                                <span
                                    id="imc-valor"
                                    class="text-lg font-bold text-gray-900"
                                >
                                    —
                                </span>

                                <span
                                    id="imc-clasificacion"
                                    class="rounded-full bg-gray-200 px-2.5 py-1 text-xs font-semibold text-gray-600"
                                >
                                    Sin calcular
                                </span>
                            </div>

                            <p class="mt-2 text-xs text-gray-400">
                                Se calcula automáticamente con peso y estatura.
                            </p>
                        </div>
                    </div>
                </section>

                {{-- Signos vitales --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-gray-100 pb-5">
                        <h3 class="text-lg font-bold text-gray-900">
                            Signos vitales
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Captura las mediciones disponibles durante la valoración.
                        </p>
                    </div>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                        {{-- Temperatura --}}
                        <div>
                            <label
                                for="temperatura"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Temperatura
                            </label>

                            <div class="relative mt-2">
                                <input
                                    id="temperatura"
                                    name="temperatura"
                                    type="number"
                                    inputmode="decimal"
                                    min="30"
                                    max="45"
                                    step="0.1"
                                    value="{{ old('temperatura') }}"
                                    placeholder="Ej. 36.5"
                                    class="block w-full rounded-xl border-gray-300 pr-14 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('temperatura') border-red-400 @enderror"
                                >

                                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-gray-400">
                                    °C
                                </span>
                            </div>

                            @error('temperatura')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Frecuencia cardiaca --}}
                        <div>
                            <label
                                for="frecuencia_cardiaca"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Frecuencia cardiaca
                            </label>

                            <div class="relative mt-2">
                                <input
                                    id="frecuencia_cardiaca"
                                    name="frecuencia_cardiaca"
                                    type="number"
                                    inputmode="numeric"
                                    min="20"
                                    max="300"
                                    step="1"
                                    value="{{ old('frecuencia_cardiaca') }}"
                                    placeholder="Ej. 75"
                                    class="block w-full rounded-xl border-gray-300 pr-16 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('frecuencia_cardiaca') border-red-400 @enderror"
                                >

                                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-gray-400">
                                    lpm
                                </span>
                            </div>

                            @error('frecuencia_cardiaca')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Frecuencia respiratoria --}}
                        <div>
                            <label
                                for="frecuencia_respiratoria"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Frecuencia respiratoria
                            </label>

                            <div class="relative mt-2">
                                <input
                                    id="frecuencia_respiratoria"
                                    name="frecuencia_respiratoria"
                                    type="number"
                                    inputmode="numeric"
                                    min="5"
                                    max="100"
                                    step="1"
                                    value="{{ old('frecuencia_respiratoria') }}"
                                    placeholder="Ej. 18"
                                    class="block w-full rounded-xl border-gray-300 pr-20 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('frecuencia_respiratoria') border-red-400 @enderror"
                                >

                                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-gray-400">
                                    rpm
                                </span>
                            </div>

                            @error('frecuencia_respiratoria')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Saturación --}}
                        <div>
                            <label
                                for="saturacion_oxigeno"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Saturación de oxígeno
                            </label>

                            <div class="relative mt-2">
                                <input
                                    id="saturacion_oxigeno"
                                    name="saturacion_oxigeno"
                                    type="number"
                                    inputmode="numeric"
                                    min="50"
                                    max="100"
                                    step="1"
                                    value="{{ old('saturacion_oxigeno') }}"
                                    placeholder="Ej. 98"
                                    class="block w-full rounded-xl border-gray-300 pr-14 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('saturacion_oxigeno') border-red-400 @enderror"
                                >

                                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-gray-400">
                                    %
                                </span>
                            </div>

                            @error('saturacion_oxigeno')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Glucosa --}}
                        <div>
                            <label
                                for="glucosa"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Glucosa
                            </label>

                            <div class="relative mt-2">
                                <input
                                    id="glucosa"
                                    name="glucosa"
                                    type="number"
                                    inputmode="decimal"
                                    min="20"
                                    max="1000"
                                    step="0.01"
                                    value="{{ old('glucosa') }}"
                                    placeholder="Ej. 95"
                                    class="block w-full rounded-xl border-gray-300 pr-20 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('glucosa') border-red-400 @enderror"
                                >

                                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-gray-400">
                                    mg/dL
                                </span>
                            </div>

                            @error('glucosa')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- Presión arterial --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-gray-100 pb-5">
                        <h3 class="text-lg font-bold text-gray-900">
                            Presión arterial
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Si registras una medida, debes capturar ambas.
                        </p>
                    </div>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">

                        {{-- Sistólica --}}
                        <div>
                            <label
                                for="presion_sistolica"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Presión sistólica
                            </label>

                            <div class="relative mt-2">
                                <input
                                    id="presion_sistolica"
                                    name="presion_sistolica"
                                    type="number"
                                    inputmode="numeric"
                                    min="40"
                                    max="300"
                                    step="1"
                                    value="{{ old('presion_sistolica') }}"
                                    placeholder="Ej. 120"
                                    class="block w-full rounded-xl border-gray-300 pr-20 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('presion_sistolica') border-red-400 @enderror"
                                >

                                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-gray-400">
                                    mmHg
                                </span>
                            </div>

                            @error('presion_sistolica')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Diastólica --}}
                        <div>
                            <label
                                for="presion_diastolica"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Presión diastólica
                            </label>

                            <div class="relative mt-2">
                                <input
                                    id="presion_diastolica"
                                    name="presion_diastolica"
                                    type="number"
                                    inputmode="numeric"
                                    min="20"
                                    max="200"
                                    step="1"
                                    value="{{ old('presion_diastolica') }}"
                                    placeholder="Ej. 80"
                                    class="block w-full rounded-xl border-gray-300 pr-20 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('presion_diastolica') border-red-400 @enderror"
                                >

                                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-gray-400">
                                    mmHg
                                </span>
                            </div>

                            @error('presion_diastolica')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- Observaciones --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <label
                        for="observaciones"
                        class="block text-lg font-bold text-gray-900"
                    >
                        Observaciones
                    </label>

                    <p class="mt-1 text-sm text-gray-500">
                        Registra síntomas, condiciones o información relevante para el médico.
                    </p>

                    <textarea
                        id="observaciones"
                        name="observaciones"
                        rows="5"
                        maxlength="2000"
                        placeholder="Ej. El paciente refiere mareo desde esta mañana..."
                        class="mt-5 block w-full resize-y rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('observaciones') border-red-400 @enderror"
                    >{{ old('observaciones') }}</textarea>

                    <div class="mt-2 flex items-center justify-between gap-4">
                        @error('observaciones')
                            <p class="text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @else
                            <span></span>
                        @enderror

                        <p class="text-xs text-gray-400">
                            <span id="contador-observaciones">0</span>/2000
                        </p>
                    </div>
                </section>

                {{-- Acciones --}}
                <section class="flex flex-col-reverse gap-3 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-end">
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        Cancelar
                    </a>

                    <button
                        id="boton-guardar"
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>

                        <span id="texto-boton-guardar">
                            Guardar signos vitales
                        </span>
                    </button>
                </section>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const formulario = document.getElementById('form-signos-vitales');
            const pesoInput = document.getElementById('peso');
            const estaturaInput = document.getElementById('estatura');
            const imcValor = document.getElementById('imc-valor');
            const imcClasificacion = document.getElementById('imc-clasificacion');
            const observaciones = document.getElementById('observaciones');
            const contadorObservaciones = document.getElementById('contador-observaciones');
            const botonGuardar = document.getElementById('boton-guardar');
            const textoBoton = document.getElementById('texto-boton-guardar');

            const estilosClasificacion = {
                bajo: 'bg-blue-50 text-blue-700',
                normal: 'bg-emerald-50 text-emerald-700',
                sobrepeso: 'bg-amber-50 text-amber-700',
                obesidad: 'bg-red-50 text-red-700',
                vacio: 'bg-gray-200 text-gray-600',
            };

            function actualizarImc() {
                const peso = Number.parseFloat(pesoInput.value);
                const estaturaCentimetros = Number.parseFloat(estaturaInput.value);

                imcClasificacion.className =
                    'rounded-full px-2.5 py-1 text-xs font-semibold';

                if (
                    !Number.isFinite(peso) ||
                    !Number.isFinite(estaturaCentimetros) ||
                    peso <= 0 ||
                    estaturaCentimetros <= 0
                ) {
                    imcValor.textContent = '—';
                    imcClasificacion.textContent = 'Sin calcular';
                    imcClasificacion.classList.add(
                        ...estilosClasificacion.vacio.split(' ')
                    );
                    return;
                }

                const estaturaMetros = estaturaCentimetros / 100;
                const imc = peso / (estaturaMetros * estaturaMetros);

                imcValor.textContent = imc.toFixed(2);

                if (imc < 18.5) {
                    imcClasificacion.textContent = 'Peso bajo';
                    imcClasificacion.classList.add(
                        ...estilosClasificacion.bajo.split(' ')
                    );
                } else if (imc < 25) {
                    imcClasificacion.textContent = 'Rango normal';
                    imcClasificacion.classList.add(
                        ...estilosClasificacion.normal.split(' ')
                    );
                } else if (imc < 30) {
                    imcClasificacion.textContent = 'Sobrepeso';
                    imcClasificacion.classList.add(
                        ...estilosClasificacion.sobrepeso.split(' ')
                    );
                } else {
                    imcClasificacion.textContent = 'Obesidad';
                    imcClasificacion.classList.add(
                        ...estilosClasificacion.obesidad.split(' ')
                    );
                }
            }

            function actualizarContador() {
                contadorObservaciones.textContent = observaciones.value.length;
            }

            pesoInput.addEventListener('input', actualizarImc);
            estaturaInput.addEventListener('input', actualizarImc);
            observaciones.addEventListener('input', actualizarContador);

            formulario.addEventListener('submit', (event) => {
                if (!formulario.checkValidity()) {
                    event.preventDefault();
                    formulario.reportValidity();
                    return;
                }

                const confirmado = window.confirm(
                    '¿Confirmas que los datos capturados son correctos? Esta valoración quedará asociada a la cita.'
                );

                if (!confirmado) {
                    event.preventDefault();
                    return;
                }

                botonGuardar.disabled = true;
                textoBoton.textContent = 'Guardando valoración...';
            });

            actualizarImc();
            actualizarContador();
        });
    </script>
</x-app-layout>