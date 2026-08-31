@if (
    request()->user()->isMedico()
    && ! $cita->evolucionClinica
)
    @php
        $erroresCaso = $errors->getBag('casoClinico');

        $erroresSeguimiento = $errors->getBag('seguimientoClinico');

        $modoInicial = $erroresSeguimiento->any()
            ? 'existente'
            : old('modo_evolucion', 'nuevo');

        $primerErrorClinico = function (string $campo) use (
            $erroresCaso,
            $erroresSeguimiento
        ): ?string {
            return $erroresCaso->first($campo)
                ?: $erroresSeguimiento->first($campo);
        };
    @endphp

    <div
        id="modal-clinico-crear-evolucion"
        data-modal-clinico-panel="crear-evolucion"
        class="fixed inset-0 z-50 hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-crear-evolucion"
        x-data="{
            modo: @js($modoInicial),
            casoId: @js((string) old('caso_seleccionado', '')),
            urlNuevo: @js(
                route('citas.casos-clinicos.store', $cita)
            ),
            urlCasos: @js(
                url('/citas/' . $cita->id . '/casos-clinicos')
            )
        }">

        <div
            class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
            data-cerrar-modal-clinico>
        </div>

        <div
            class="relative flex min-h-full items-center
                   justify-center p-3 sm:p-6">

            <div
                class="relative flex max-h-[94vh] w-full min-w-0
                       max-w-4xl flex-col overflow-hidden
                       rounded-2xl bg-white shadow-2xl
                       [overflow-wrap:anywhere]">

                <header
                    class="flex shrink-0 items-start justify-between
                           gap-5 border-b border-slate-200
                           px-5 py-4 sm:px-6">

                    <div class="min-w-0 flex-1">
                        <p
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-[#0D3B7F]">
                            Seguimiento médico
                        </p>

                        <h2
                            id="titulo-modal-crear-evolucion"
                            class="mt-1 text-xl font-bold text-slate-900">
                            Registrar evolución clínica
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $cita->paciente?->nombre }}
                            {{ $cita->paciente?->apellido }}
                            ·
                            {{ $cita->fecha->format('d/m/Y') }}
                        </p>
                    </div>

                    <button
                        type="button"
                        data-cerrar-modal-clinico
                        aria-label="Cerrar evolución"
                        class="shrink-0 rounded-lg p-2 text-slate-600
                               transition hover:bg-slate-100
                               hover:text-slate-900
                               focus-visible:outline-none
                               focus-visible:ring-2
                               focus-visible:ring-[#0D3B7F]
                               focus-visible:ring-offset-2">
                        ✕
                    </button>
                </header>

                <form
                    method="POST"
                    x-bind:action="
                        modo === 'nuevo'
                            ? urlNuevo
                            : urlCasos + '/' + casoId + '/evoluciones'
                    "
                    class="flex min-h-0 min-w-0 flex-1 flex-col">

                    @csrf

                    <input
                        type="hidden"
                        name="modo_evolucion"
                        x-model="modo">

                    <div
                        class="min-h-0 min-w-0 flex-1 overflow-y-auto
                               bg-slate-50/70 p-5 sm:p-6">

                        {{-- Tipo de registro --}}
                        <div
                            role="group"
                            aria-label="Tipo de seguimiento"
                            class="grid gap-3 rounded-2xl
                                   border border-slate-200
                                   bg-white p-2 sm:grid-cols-2">

                            <button
                                type="button"
                                x-on:click="modo = 'nuevo'"
                                x-bind:aria-pressed="
                                    modo === 'nuevo' ? 'true' : 'false'
                                "
                                x-bind:class="
                                    modo === 'nuevo'
                                        ? 'border-[#0D3B7F] bg-blue-50 text-[#0D3B7F]'
                                        : 'border-transparent text-slate-500 hover:bg-slate-50'
                                "
                                class="min-w-0 rounded-xl border px-4 py-3
                                       text-left transition
                                       [overflow-wrap:anywhere]
                                       focus-visible:outline-none
                                       focus-visible:ring-2
                                       focus-visible:ring-[#0D3B7F]
                                       focus-visible:ring-offset-2">
                                <span class="block text-sm font-bold">
                                    Abrir caso nuevo
                                </span>

                                <span
                                    class="mt-1 block text-xs font-normal">
                                    Inicia un seguimiento para
                                    un problema nuevo.
                                </span>
                            </button>

                            <button
                                type="button"
                                x-on:click="modo = 'existente'"
                                x-bind:aria-pressed="
                                    modo === 'existente' ? 'true' : 'false'
                                "
                                @disabled($casosClinicosActivos->isEmpty())
                                x-bind:class="
                                    modo === 'existente'
                                        ? 'border-[#0D3B7F] bg-blue-50 text-[#0D3B7F]'
                                        : 'border-transparent text-slate-500 hover:bg-slate-50'
                                "
                                class="min-w-0 rounded-xl border px-4 py-3
                                       text-left transition
                                       [overflow-wrap:anywhere]
                                       disabled:cursor-not-allowed
                                       disabled:opacity-50
                                       focus-visible:outline-none
                                       focus-visible:ring-2
                                       focus-visible:ring-[#0D3B7F]
                                       focus-visible:ring-offset-2">
                                <span class="block text-sm font-bold">
                                    Caso existente
                                </span>

                                <span
                                    class="mt-1 block text-xs font-normal">
                                    Continúa un seguimiento
                                    activo del paciente.
                                </span>
                            </button>
                        </div>

                        {{-- Datos del caso nuevo --}}
                        <section
                            x-show="modo === 'nuevo'"
                            x-cloak
                            class="mt-5 min-w-0 rounded-2xl border
                                   border-blue-200 bg-blue-50/50 p-5">

                            <h3 class="font-bold text-blue-950">
                                Identificación del caso
                            </h3>

                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div class="min-w-0">
                                    <label
                                        for="nombre-caso-clinico"
                                        class="block text-sm font-semibold
                                               text-slate-700">
                                        Nombre o motivo
                                    </label>

                                    <input
                                        id="nombre-caso-clinico"
                                        type="text"
                                        name="nombre"
                                        value="{{ old('nombre') }}"
                                        maxlength="150"
                                        x-bind:required="modo === 'nuevo'"
                                        placeholder="Ej. Fractura de tobillo"
                                        class="mt-1.5 block w-full min-w-0
                                               rounded-xl border-slate-300
                                               text-sm shadow-sm
                                               focus:border-[#0D3B7F]
                                               focus:ring-[#0D3B7F]">

                                    @error('nombre', 'casoClinico')
                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="min-w-0">
                                    <label
                                        for="descripcion-caso-clinico"
                                        class="block text-sm font-semibold
                                               text-slate-700">
                                        Descripción inicial
                                    </label>

                                    <textarea
                                        id="descripcion-caso-clinico"
                                        name="descripcion_inicial"
                                        rows="2"
                                        maxlength="20000"
                                        placeholder="Contexto inicial del seguimiento..."
                                        class="mt-1.5 block w-full min-w-0
                                               resize-none rounded-xl
                                               border-slate-300 text-sm
                                               shadow-sm
                                               focus:border-[#0D3B7F]
                                               focus:ring-[#0D3B7F]">{{ old('descripcion_inicial') }}</textarea>

                                    @error('descripcion_inicial', 'casoClinico')
                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </section>

                        {{-- Selección de caso existente --}}
                        <section
                            x-show="modo === 'existente'"
                            x-cloak
                            class="mt-5 min-w-0 rounded-2xl border
                                   border-emerald-200
                                   bg-emerald-50/50 p-5">

                            @if ($casosClinicosActivos->isEmpty())
                                <h3
                                    class="text-sm font-semibold text-slate-700">
                                    Caso clínico activo
                                </h3>

                                <p class="mt-2 text-sm text-slate-500">
                                    El paciente no tiene casos
                                    clínicos activos.
                                </p>
                            @else
                                <label
                                    for="caso-clinico-existente"
                                    class="block text-sm font-semibold
                                           text-slate-700">
                                    Caso clínico activo
                                </label>

                                <select
                                    id="caso-clinico-existente"
                                    name="caso_seleccionado"
                                    x-model="casoId"
                                    x-bind:required="modo === 'existente'"
                                    class="mt-1.5 block w-full min-w-0 max-w-full
                                           rounded-xl border-slate-300
                                           text-sm shadow-sm
                                           focus:border-[#0D3B7F]
                                           focus:ring-[#0D3B7F]">
                                    <option value="">
                                        Selecciona un seguimiento
                                    </option>

                                    @foreach ($casosClinicosActivos as $caso)
                                        <option value="{{ $caso->id }}">
                                            {{ $caso->nombre }}
                                            ·
                                            {{ $caso->evoluciones_count }}
                                            evolución(es)
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </section>

                        {{-- Datos clínicos --}}
                        <section
                            class="mt-5 min-w-0 rounded-2xl border
                                   border-slate-200 bg-white p-5">

                            <h3 class="font-bold text-slate-900">
                                Valoración de esta cita
                            </h3>

                            <div class="mt-4 space-y-4">
                                <div class="min-w-0">
                                    <label
                                        for="nueva-evolucion-clinica"
                                        class="block text-sm font-semibold
                                               text-slate-700">
                                        Evolución clínica
                                    </label>

                                    <textarea
                                        id="nueva-evolucion-clinica"
                                        name="evolucion_clinica"
                                        rows="4"
                                        required
                                        maxlength="50000"
                                        placeholder="Describe el estado y los cambios observados..."
                                        class="mt-1.5 block w-full min-w-0
                                               rounded-xl border-slate-300
                                               text-sm shadow-sm
                                               focus:border-[#0D3B7F]
                                               focus:ring-[#0D3B7F]">{{ old('evolucion_clinica') }}</textarea>

                                    @if ($primerErrorClinico('evolucion_clinica'))
                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $primerErrorClinico('evolucion_clinica') }}
                                        </p>
                                    @endif
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    @foreach ([
                                        [
                                            'campo' => 'diagnostico',
                                            'titulo' => 'Diagnóstico actual',
                                        ],
                                        [
                                            'campo' => 'tratamiento',
                                            'titulo' => 'Tratamiento',
                                        ],
                                        [
                                            'campo' => 'plan_recomendaciones',
                                            'titulo' => 'Plan y recomendaciones',
                                        ],
                                        [
                                            'campo' => 'indicaciones_enfermeria',
                                            'titulo' => 'Indicaciones para enfermería',
                                        ],
                                    ] as $campoClinico)
                                        <div class="min-w-0">
                                            <label
                                                for="nueva-{{ $campoClinico['campo'] }}"
                                                class="block text-sm font-semibold
                                                       text-slate-700">
                                                {{ $campoClinico['titulo'] }}
                                            </label>

                                            <textarea
                                                id="nueva-{{ $campoClinico['campo'] }}"
                                                name="{{ $campoClinico['campo'] }}"
                                                rows="3"
                                                maxlength="50000"
                                                class="mt-1.5 block w-full min-w-0
                                                       rounded-xl border-slate-300
                                                       text-sm shadow-sm
                                                       focus:border-[#0D3B7F]
                                                       focus:ring-[#0D3B7F]">{{ old($campoClinico['campo']) }}</textarea>

                                            @if ($primerErrorClinico($campoClinico['campo']))
                                                <p class="mt-1 text-xs text-red-600">
                                                    {{ $primerErrorClinico($campoClinico['campo']) }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <div class="min-w-0">
                                    <label
                                        for="nuevas-observaciones-evolucion"
                                        class="block text-sm font-semibold
                                               text-slate-700">
                                        Observaciones adicionales
                                    </label>

                                    <textarea
                                        id="nuevas-observaciones-evolucion"
                                        name="observaciones"
                                        rows="3"
                                        maxlength="50000"
                                        class="mt-1.5 block w-full min-w-0
                                               rounded-xl border-slate-300
                                               text-sm shadow-sm
                                               focus:border-[#0D3B7F]
                                               focus:ring-[#0D3B7F]">{{ old('observaciones') }}</textarea>

                                    @if ($primerErrorClinico('observaciones'))
                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $primerErrorClinico('observaciones') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </section>
                    </div>

                    <footer
                        class="flex shrink-0 flex-col gap-3
                               border-t border-slate-200 bg-white
                               px-5 py-4 sm:flex-row
                               sm:justify-end sm:px-6">

                        <button
                            type="button"
                            data-cerrar-modal-clinico
                            class="w-full rounded-xl border border-slate-300
                                   px-5 py-2.5 text-sm font-semibold
                                   text-slate-700 transition
                                   hover:bg-slate-50
                                   focus-visible:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-[#0D3B7F]
                                   focus-visible:ring-offset-2 sm:w-auto">
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            x-bind:disabled="
                                modo === 'existente'
                                && ! casoId
                            "
                            class="w-full rounded-xl bg-[#0D3B7F]
                                   px-5 py-2.5 text-sm font-semibold
                                   text-white transition
                                   hover:bg-[#082a5d]
                                   disabled:cursor-not-allowed
                                   disabled:opacity-50
                                   focus-visible:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-[#0D3B7F]
                                   focus-visible:ring-offset-2 sm:w-auto">
                            Guardar evolución
                        </button>
                    </footer>
                </form>
            </div>
        </div>
    </div>

    @if ($erroresCaso->any() || $erroresSeguimiento->any())
        <script>
            document.addEventListener(
                'DOMContentLoaded',
                function () {
                    abrirModalClinico('crear-evolucion');
                }
            );
        </script>
    @endif
@endif