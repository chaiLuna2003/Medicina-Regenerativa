@if (
    $puedeConsultarInformacionClinica
    && $cita->evolucionClinica
)
    @php
        $evolucionParaAparatos = $cita->evolucionClinica;

        $valoracionesGuardadas = $evolucionParaAparatos
            ->aparatos
            ->keyBy('aparato');

        $erroresAparatos = $errors->getBag('aparatosEvolucion');

        $puedeEditarAparatos =
            request()->user()->isMedico()
            && request()->user()->medico
            && (int) request()->user()->medico->id
                === (int) $evolucionParaAparatos->medico_id
            && $evolucionParaAparatos->casoClinico?->estaActivo()
            && $cita->estado !== 'cancelada';
    @endphp

    <div
        id="modal-clinico-aparatos"
        data-modal-clinico-panel="aparatos"
        class="fixed inset-0 z-50 hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-aparatos">

        <div
            class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
            data-cerrar-modal-clinico>
        </div>

        <div
            class="relative flex min-h-full items-center
                   justify-center p-2 sm:p-5">

            <div
                class="relative flex max-h-[96vh] w-full min-w-0
                       max-w-7xl flex-col overflow-hidden
                       rounded-2xl bg-white shadow-2xl
                       [overflow-wrap:anywhere]">

                <header
                    class="flex shrink-0 items-start justify-between
                           gap-5 border-b border-slate-200
                           px-5 py-4 sm:px-6">

                    <div class="min-w-0 flex-1">
                        <p
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-amber-700">
                            Evolución clínica
                        </p>

                        <h2
                            id="titulo-modal-aparatos"
                            class="mt-1 text-xl font-bold text-slate-900">
                            Valoración de aparatos
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $evolucionParaAparatos->casoClinico?->nombre }}
                            ·
                            {{ $cita->fecha->format('d/m/Y') }}
                        </p>
                    </div>

                    <button
                        type="button"
                        data-cerrar-modal-clinico
                        aria-label="Cerrar aparatos"
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
                    action="{{ route(
                        'evoluciones.aparatos.update',
                        $evolucionParaAparatos
                    ) }}"
                    class="flex min-h-0 min-w-0 flex-1 flex-col">

                    @csrf
                    @method('PUT')

                    <div
                        class="min-h-0 min-w-0 flex-1 overflow-y-auto
                               bg-slate-50/70 p-4 sm:p-6">

                        {{-- Leyenda --}}
                        <div
                            class="mb-5 flex flex-wrap items-center
                                   gap-x-5 gap-y-2 rounded-xl
                                   border border-slate-200
                                   bg-white px-4 py-3">

                            @foreach ([
                                [
                                    'color' => 'bg-slate-300',
                                    'texto' => 'No evaluado',
                                ],
                                [
                                    'color' => 'bg-emerald-500',
                                    'texto' => 'Normal',
                                ],
                                [
                                    'color' => 'bg-amber-400',
                                    'texto' => 'Requiere atención',
                                ],
                                [
                                    'color' => 'bg-red-500',
                                    'texto' => 'Crítico',
                                ],
                            ] as $leyenda)
                                <div
                                    class="flex items-center gap-2
                                           text-xs font-semibold
                                           text-slate-600">
                                    <span
                                        aria-hidden="true"
                                        class="h-3 w-3 shrink-0 rounded-full
                                               {{ $leyenda['color'] }}">
                                    </span>

                                    {{ $leyenda['texto'] }}
                                </div>
                            @endforeach

                            <span
                                class="ml-auto rounded-full px-3 py-1
                                       text-xs font-semibold
                                       {{ $puedeEditarAparatos
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-slate-100 text-slate-600' }}">
                                {{ $puedeEditarAparatos
                                    ? 'Editable'
                                    : 'Solo lectura' }}
                            </span>
                        </div>

                        {{-- Cuadrícula --}}
                        <div
                            class="grid gap-4 sm:grid-cols-2
                                   lg:grid-cols-3 xl:grid-cols-4">

                            @foreach (
                                \App\Models\EvolucionAparato::APARATOS
                                as $clave => $configuracion
                            )
                                @php
                                    $valoracionGuardada = $valoracionesGuardadas
                                        ->get($clave);

                                    $estadoInicial = $erroresAparatos->any()
                                        ? old(
                                            "aparatos.{$clave}.estado",
                                            'no_evaluado'
                                        )
                                        : (
                                            $valoracionGuardada?->estado
                                            ?? 'no_evaluado'
                                        );

                                    $observacionInicial = $erroresAparatos->any()
                                        ? old("aparatos.{$clave}.observaciones")
                                        : $valoracionGuardada?->observaciones;
                                @endphp

                                <article
                                    x-data="{ estado: @js($estadoInicial) }"
                                    class="min-w-0 rounded-2xl border
                                           border-slate-200 bg-white
                                           p-4 shadow-sm">

                                    {{-- Aparato --}}
                                    <div class="flex items-center gap-3">
                                        <img
                                            src="{{ asset($configuracion['imagen']) }}"
                                            alt=""
                                            class="h-14 w-14 shrink-0 rounded-xl
                                                   border border-slate-200
                                                   bg-slate-50 object-cover">

                                        <div class="min-w-0">
                                            <h3
                                                class="font-bold text-slate-900">
                                                {{ $configuracion['nombre'] }}
                                            </h3>

                                            <p
                                                class="mt-0.5 text-xs text-slate-600">
                                                {{ $puedeEditarAparatos
                                                    ? 'Selecciona su estado'
                                                    : 'Valoración registrada' }}
                                            </p>
                                        </div>
                                    </div>

                                    <input
                                        type="hidden"
                                        name="aparatos[{{ $clave }}][estado]"
                                        x-model="estado">

                                    {{-- Estados --}}
                                    <div
                                        class="mt-4 grid grid-cols-4 gap-2"
                                        role="group"
                                        aria-label="Estado de {{ $configuracion['nombre'] }}">

                                        @foreach ([
                                            [
                                                'estado' => 'no_evaluado',
                                                'texto' => 'No evaluado',
                                                'color' => 'bg-slate-300',
                                                'ring' => 'ring-slate-400',
                                            ],
                                            [
                                                'estado' => 'normal',
                                                'texto' => 'Normal',
                                                'color' => 'bg-emerald-500',
                                                'ring' => 'ring-emerald-500',
                                            ],
                                            [
                                                'estado' => 'requiere_atencion',
                                                'texto' => 'Requiere atención',
                                                'color' => 'bg-amber-400',
                                                'ring' => 'ring-amber-500',
                                            ],
                                            [
                                                'estado' => 'critico',
                                                'texto' => 'Crítico',
                                                'color' => 'bg-red-500',
                                                'ring' => 'ring-red-500',
                                            ],
                                        ] as $opcion)
                                            <button
                                                type="button"
                                                title="{{ $opcion['texto'] }}"
                                                aria-label="{{ $opcion['texto'] }}"
                                                x-bind:aria-pressed="
                                                    estado === @js($opcion['estado'])
                                                        ? 'true'
                                                        : 'false'
                                                "
                                                @disabled(! $puedeEditarAparatos)
                                                x-on:click="
                                                    estado = @js($opcion['estado'])
                                                "
                                                x-bind:class="
                                                    estado === @js($opcion['estado'])
                                                        ? 'ring-2 ring-offset-2 {{ $opcion['ring'] }}'
                                                        : 'opacity-55 hover:opacity-100'
                                                "
                                                class="mx-auto flex h-10 w-10
                                                       items-center justify-center
                                                       rounded-full transition
                                                       disabled:cursor-default
                                                       focus-visible:outline
                                                       focus-visible:outline-2
                                                       focus-visible:outline-offset-4
                                                       focus-visible:outline-[#0D3B7F]">
                                                <span
                                                    aria-hidden="true"
                                                    class="h-5 w-5 rounded-full
                                                           {{ $opcion['color'] }}">
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>

                                    {{-- Estado seleccionado --}}
                                    <p
                                        class="mt-3 text-center text-xs font-semibold">
                                        <span
                                            x-show="estado === 'no_evaluado'"
                                            class="text-slate-600">
                                            No evaluado
                                        </span>

                                        <span
                                            x-show="estado === 'normal'"
                                            x-cloak
                                            class="text-emerald-700">
                                            Normal
                                        </span>

                                        <span
                                            x-show="estado === 'requiere_atencion'"
                                            x-cloak
                                            class="text-amber-700">
                                            Requiere atención
                                        </span>

                                        <span
                                            x-show="estado === 'critico'"
                                            x-cloak
                                            class="text-red-700">
                                            Crítico
                                        </span>
                                    </p>

                                    {{-- Observaciones --}}
                                    <div
                                        class="mt-3"
                                        x-show="estado !== 'no_evaluado'"
                                        x-cloak>

                                        <label
                                            for="observaciones-{{ $clave }}"
                                            class="block text-xs font-semibold
                                                   text-slate-600">
                                            Observaciones
                                        </label>

                                        <textarea
                                            id="observaciones-{{ $clave }}"
                                            name="aparatos[{{ $clave }}][observaciones]"
                                            rows="2"
                                            maxlength="5000"
                                            @readonly(! $puedeEditarAparatos)
                                            x-bind:required="
                                                estado === 'requiere_atencion'
                                                || estado === 'critico'
                                            "
                                            class="mt-1 block w-full min-w-0
                                                   resize-none rounded-lg
                                                   border-slate-300
                                                   text-xs shadow-sm
                                                   read-only:bg-slate-50
                                                   focus:border-[#0D3B7F]
                                                   focus:ring-[#0D3B7F]">{{ $observacionInicial }}</textarea>

                                        @error(
                                            "aparatos.{$clave}.observaciones",
                                            'aparatosEvolucion'
                                        )
                                            <p class="mt-1 text-xs text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    @error(
                                        "aparatos.{$clave}.estado",
                                        'aparatosEvolucion'
                                    )
                                        <p class="mt-2 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </article>
                            @endforeach
                        </div>
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
                            Cerrar
                        </button>

                        @if ($puedeEditarAparatos)
                            <button
                                type="submit"
                                class="w-full rounded-xl bg-[#0D3B7F]
                                       px-5 py-2.5 text-sm font-semibold
                                       text-white transition
                                       hover:bg-[#082a5d]
                                       focus-visible:outline-none
                                       focus-visible:ring-2
                                       focus-visible:ring-[#0D3B7F]
                                       focus-visible:ring-offset-2 sm:w-auto">
                                Guardar valoración
                            </button>
                        @endif
                    </footer>
                </form>
            </div>
        </div>
    </div>

    @if ($erroresAparatos->any())
        <script>
            document.addEventListener(
                'DOMContentLoaded',
                function () {
                    abrirModalClinico('aparatos');
                }
            );
        </script>
    @endif
@endif