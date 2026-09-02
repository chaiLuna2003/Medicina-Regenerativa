<div
    id="modal-signos-vitales"
    class="fixed inset-0 z-50 hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="titulo-modal-signos">

    {{-- Fondo --}}
    <div
        class="absolute inset-0 bg-gray-950/60 backdrop-blur-sm"
        data-cerrar-modal-signos>
    </div>

    {{-- Contenedor --}}
    <div
        class="relative flex min-h-full items-center
               justify-center p-4 sm:p-6">

        <div
            class="flex max-h-[92vh] w-full max-w-5xl
                   flex-col overflow-hidden rounded-2xl
                   bg-gray-50 shadow-2xl">

            {{-- Encabezado --}}
            <header
                class="flex shrink-0 items-start justify-between
                       gap-4 border-b border-gray-200
                       bg-white px-5 py-4 sm:px-6">

                <div>
                    <p
                        class="text-xs font-bold uppercase
                               tracking-wide text-emerald-600">
                        Valoración de enfermería
                    </p>

                    <h2
                        id="titulo-modal-signos"
                        class="mt-1 text-xl font-bold text-gray-900">
                        Registrar signos vitales
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Paciente:
                        <span
                            id="modal-nombre-paciente"
                            class="font-semibold text-gray-700">
                            {{ old('modal_paciente_nombre', 'Paciente seleccionado') }}
                        </span>
                    </p>
                </div>

                <button
                    type="button"
                    data-cerrar-modal-signos
                    class="flex h-10 w-10 shrink-0 items-center
                           justify-center rounded-xl text-gray-400
                           transition hover:bg-gray-100
                           hover:text-gray-700"
                    aria-label="Cerrar formulario">

                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            {{-- Contenido desplazable --}}
            <div class="min-h-0 flex-1 overflow-y-auto p-5 sm:p-6">

                @if ($errors->any() && old('modal_cita_id'))
                <input
                    id="modal-paciente-input"
                    type="hidden"
                    name="modal_paciente_nombre"
                    value="{{ old('modal_paciente_nombre') }}">
                <div
                    class="mb-5 rounded-xl border border-red-200
                               bg-red-50 p-4"
                    role="alert">

                    <p class="font-bold text-red-900">
                        Revisa la información capturada
                    </p>

                    <ul
                        class="mt-2 list-disc space-y-1 pl-5
                                   text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form
                    id="form-modal-signos"
                    method="POST"
                    action="{{
                        old('modal_cita_id')
                            ? route(
                                'signos-vitales.store',
                                old('modal_cita_id')
                            )
                            : '#'
                    }}"
                    class="space-y-5">

                    @csrf

                    <input
                        type="hidden"
                        name="desde_dashboard_enfermeria"
                        value="1">

                    <input
                        id="modal-cita-id"
                        type="hidden"
                        name="modal_cita_id"
                        value="{{ old('modal_cita_id') }}">

                    <input
    id="modal-paciente-input"
    type="hidden"
    name="modal_paciente_nombre"
    value="{{ old('modal_paciente_nombre') }}">

                    {{-- Medidas corporales --}}
                    <section
                        class="rounded-2xl border border-gray-200
                               bg-white p-5 shadow-sm">

                        <div>
                            <h3 class="font-bold text-gray-900">
                                Medidas corporales
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Peso y estatura son obligatorios.
                            </p>
                        </div>

                        <div
                            class="mt-5 grid gap-5
                                   sm:grid-cols-2 lg:grid-cols-3">

                            <div>
                                <label
                                    for="modal-peso"
                                    class="block text-sm font-semibold
                                           text-gray-700">
                                    Peso <span class="text-red-500">*</span>
                                </label>

                                <div class="relative mt-2">
                                    <input
                                        id="modal-peso"
                                        name="peso"
                                        type="number"
                                        inputmode="decimal"
                                        min="0.5"
                                        max="500"
                                        step="0.01"
                                        value="{{ old('peso') }}"
                                        required
                                        placeholder="Ej. 72.50"
                                        class="block w-full rounded-xl
                                               border-gray-300 pr-12
                                               shadow-sm
                                               focus:border-emerald-500
                                               focus:ring-emerald-500">

                                    <span
                                        class="pointer-events-none absolute
                                               inset-y-0 right-4 flex
                                               items-center text-sm
                                               text-gray-400">
                                        kg
                                    </span>
                                </div>

                                @error('peso')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="modal-estatura"
                                    class="block text-sm font-semibold
                                           text-gray-700">
                                    Estatura
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="relative mt-2">
                                    <input
                                        id="modal-estatura"
                                        name="estatura"
                                        type="number"
                                        inputmode="decimal"
                                        min="20"
                                        max="250"
                                        step="0.01"
                                        value="{{ old('estatura') }}"
                                        required
                                        placeholder="Ej. 175"
                                        class="block w-full rounded-xl
                                               border-gray-300 pr-12
                                               shadow-sm
                                               focus:border-emerald-500
                                               focus:ring-emerald-500">

                                    <span
                                        class="pointer-events-none absolute
                                               inset-y-0 right-4 flex
                                               items-center text-sm
                                               text-gray-400">
                                        cm
                                    </span>
                                </div>

                                @error('estatura')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <span
                                    class="block text-sm font-semibold
                                           text-gray-700">
                                    IMC calculado
                                </span>

                                <div
                                    class="mt-2 flex min-h-[42px]
                                           items-center justify-between
                                           rounded-xl border border-gray-200
                                           bg-gray-50 px-4 py-2">

                                    <span
                                        id="modal-imc-valor"
                                        class="text-lg font-bold text-gray-900">
                                        —
                                    </span>

                                    <span
                                        id="modal-imc-clasificacion"
                                        class="rounded-full bg-gray-200
                                               px-2.5 py-1 text-xs
                                               font-semibold text-gray-600">
                                        Sin calcular
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Signos vitales --}}
                    <section
                        class="rounded-2xl border border-gray-200
                               bg-white p-5 shadow-sm">

                        <h3 class="font-bold text-gray-900">
                            Signos vitales
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Captura las mediciones disponibles.
                        </p>

                        <div
                            class="mt-5 grid gap-5
                                   sm:grid-cols-2 lg:grid-cols-3">

                            @php
                            $camposSignos = [
                            [
                            'nombre' => 'temperatura',
                            'etiqueta' => 'Temperatura',
                            'min' => '30',
                            'max' => '45',
                            'step' => '0.1',
                            'unidad' => '°C',
                            'ejemplo' => '36.5',
                            ],
                            [
                            'nombre' => 'frecuencia_cardiaca',
                            'etiqueta' => 'Frecuencia cardiaca',
                            'min' => '20',
                            'max' => '300',
                            'step' => '1',
                            'unidad' => 'lpm',
                            'ejemplo' => '75',
                            ],
                            [
                            'nombre' => 'frecuencia_respiratoria',
                            'etiqueta' => 'Frecuencia respiratoria',
                            'min' => '5',
                            'max' => '100',
                            'step' => '1',
                            'unidad' => 'rpm',
                            'ejemplo' => '18',
                            ],
                            [
                            'nombre' => 'saturacion_oxigeno',
                            'etiqueta' => 'Saturación de oxígeno',
                            'min' => '50',
                            'max' => '100',
                            'step' => '1',
                            'unidad' => '%',
                            'ejemplo' => '98',
                            ],
                            [
                            'nombre' => 'glucosa',
                            'etiqueta' => 'Glucosa',
                            'min' => '20',
                            'max' => '1000',
                            'step' => '0.01',
                            'unidad' => 'mg/dL',
                            'ejemplo' => '95',
                            ],
                            ];
                            @endphp

                            @foreach ($camposSignos as $campo)
                            <div>
                                <label
                                    for="modal-{{ $campo['nombre'] }}"
                                    class="block text-sm font-semibold
                                               text-gray-700">
                                    {{ $campo['etiqueta'] }}
                                </label>

                                <div class="relative mt-2">
                                    <input
                                        id="modal-{{ $campo['nombre'] }}"
                                        name="{{ $campo['nombre'] }}"
                                        type="number"
                                        inputmode="decimal"
                                        min="{{ $campo['min'] }}"
                                        max="{{ $campo['max'] }}"
                                        step="{{ $campo['step'] }}"
                                        value="{{ old($campo['nombre']) }}"
                                        placeholder="Ej. {{ $campo['ejemplo'] }}"
                                        class="block w-full rounded-xl
                                                   border-gray-300 pr-16
                                                   shadow-sm
                                                   focus:border-emerald-500
                                                   focus:ring-emerald-500">

                                    <span
                                        class="pointer-events-none
                                                   absolute inset-y-0 right-4
                                                   flex items-center text-sm
                                                   text-gray-400">
                                        {{ $campo['unidad'] }}
                                    </span>
                                </div>

                                @error($campo['nombre'])
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                    </section>

                    {{-- Presión arterial --}}
                    <section
                        class="rounded-2xl border border-gray-200
                               bg-white p-5 shadow-sm">

                        <h3 class="font-bold text-gray-900">
                            Presión arterial
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Si capturas una medida, debes registrar ambas.
                        </p>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            @foreach ([
                            [
                            'nombre' => 'presion_sistolica',
                            'etiqueta' => 'Presión sistólica',
                            'min' => 40,
                            'max' => 300,
                            'ejemplo' => 120,
                            ],
                            [
                            'nombre' => 'presion_diastolica',
                            'etiqueta' => 'Presión diastólica',
                            'min' => 20,
                            'max' => 200,
                            'ejemplo' => 80,
                            ],
                            ] as $presion)
                            <div>
                                <label
                                    for="modal-{{ $presion['nombre'] }}"
                                    class="block text-sm font-semibold
                                               text-gray-700">
                                    {{ $presion['etiqueta'] }}
                                </label>

                                <div class="relative mt-2">
                                    <input
                                        id="modal-{{ $presion['nombre'] }}"
                                        name="{{ $presion['nombre'] }}"
                                        type="number"
                                        inputmode="numeric"
                                        min="{{ $presion['min'] }}"
                                        max="{{ $presion['max'] }}"
                                        step="1"
                                        value="{{
                                                old($presion['nombre'])
                                            }}"
                                        placeholder="Ej. {{
                                                $presion['ejemplo']
                                            }}"
                                        class="block w-full rounded-xl
                                                   border-gray-300 pr-20
                                                   shadow-sm
                                                   focus:border-emerald-500
                                                   focus:ring-emerald-500">

                                    <span
                                        class="pointer-events-none
                                                   absolute inset-y-0 right-4
                                                   flex items-center text-sm
                                                   text-gray-400">
                                        mmHg
                                    </span>
                                </div>

                                @error($presion['nombre'])
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                    </section>

                    {{-- Observaciones --}}
                    <section
                        class="rounded-2xl border border-gray-200
                               bg-white p-5 shadow-sm">

                        <label
                            for="modal-observaciones"
                            class="font-bold text-gray-900">
                            Observaciones
                        </label>

                        <p class="mt-1 text-sm text-gray-500">
                            Registra síntomas o información relevante.
                        </p>

                        <textarea
                            id="modal-observaciones"
                            name="observaciones"
                            rows="4"
                            maxlength="2000"
                            placeholder="Ej. El paciente refiere mareo..."
                            class="mt-4 block w-full resize-y
                                   rounded-xl border-gray-300 shadow-sm
                                   focus:border-emerald-500
                                   focus:ring-emerald-500">{{ old('observaciones') }}</textarea>

                        <div
                            class="mt-2 flex items-center
                                   justify-between gap-4">
                            @error('observaciones')
                            <p class="text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @else
                            <span></span>
                            @enderror

                            <p class="text-xs text-gray-400">
                                <span id="modal-contador-observaciones">
                                    0
                                </span>/2000
                            </p>
                        </div>
                    </section>
                </form>
            </div>

            {{-- Acciones fijas --}}
            <footer
                class="flex shrink-0 flex-col-reverse gap-3
                       border-t border-gray-200 bg-white
                       px-5 py-4 sm:flex-row
                       sm:items-center sm:justify-end sm:px-6">

                <button
                    type="button"
                    data-cerrar-modal-signos
                    class="inline-flex items-center justify-center
                           rounded-xl border border-gray-300
                           bg-white px-5 py-3 text-sm font-semibold
                           text-gray-700 transition hover:bg-gray-50">
                    Cancelar
                </button>

                <button
                    id="modal-boton-guardar"
                    type="submit"
                    form="form-modal-signos"
                    class="inline-flex items-center justify-center
                           gap-2 rounded-xl bg-gray-900 px-6 py-3
                           text-sm font-semibold text-white
                           shadow-sm transition hover:bg-gray-800
                           disabled:cursor-not-allowed
                           disabled:opacity-60">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>

                    <span id="modal-texto-guardar">
                        Guardar signos vitales
                    </span>
                </button>
            </footer>
        </div>
    </div>
</div>