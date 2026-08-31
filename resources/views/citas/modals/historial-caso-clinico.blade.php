@if (
$puedeConsultarInformacionClinica
&& $cita->evolucionClinica?->casoClinico
)
@php
$casoHistorial =
$cita->evolucionClinica->casoClinico;

$evolucionesHistorial =
$casoHistorial->evoluciones;
@endphp

<div
    id="modal-clinico-historial-caso"
    data-modal-clinico-panel="historial-caso"
    class="fixed inset-0 z-50 hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="titulo-modal-historial-caso">
    <div
        class="absolute inset-0 bg-slate-950/60
                   backdrop-blur-sm"
        data-cerrar-modal-clinico></div>

    <div
        class="relative flex min-h-full items-center
                   justify-center p-2 sm:p-5">
        <div
            class="relative flex max-h-[96vh] w-full min-w-0
           max-w-6xl flex-col overflow-hidden
           [overflow-wrap:anywhere]
           rounded-2xl bg-white shadow-2xl">

            <header
                class="flex shrink-0 items-start
                           justify-between gap-5 border-b
                           border-slate-200 px-5 py-4 sm:px-6">
                <div class="min-w-0 flex-1">
                    <p
                        class="text-xs font-semibold uppercase
                                   tracking-wide text-[#0D3B7F]">
                        Historial de seguimiento
                    </p>

                    <h2
                        id="titulo-modal-historial-caso"
                        class="mt-1 break-words [overflow-wrap:anywhere]
           text-xl font-bold text-slate-900">
                        {{ $casoHistorial->nombre }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $cita->paciente?->nombre }}
                        {{ $cita->paciente?->apellido }}
                    </p>
                </div>

                <button
                    type="button"
                    data-cerrar-modal-clinico
                    aria-label="Cerrar historial"
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

            <div
                class="min-h-0 flex-1 overflow-y-auto
                           bg-slate-50/70 p-4 sm:p-6">

                {{-- Resumen del caso --}}
                <section
                    class="rounded-2xl border
                               border-blue-200 bg-blue-50 p-5">
                    <div
                        class="grid gap-4
                                   sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <p
                                class="text-xs font-semibold
                                           uppercase tracking-wide
                                           text-blue-500">
                                Fecha de inicio
                            </p>

                            <p
                                class="mt-1 font-bold
                                           text-blue-950">
                                {{ $casoHistorial
                                        ->fecha_inicio
                                        ->format('d/m/Y') }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold
                                           uppercase tracking-wide
                                           text-blue-500">
                                Estado
                            </p>

                            <span
                                class="mt-1 inline-flex rounded-full
                                           px-3 py-1 text-xs
                                           font-semibold
                                           {{ $casoHistorial->estado
                                                === 'activo'
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-slate-200 text-slate-600' }}">
                                {{ $casoHistorial->estado
                                        === 'activo'
                                            ? 'Activo'
                                            : 'Cerrado' }}
                            </span>
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold
                                           uppercase tracking-wide
                                           text-blue-500">
                                Evoluciones
                            </p>

                            <p
                                class="mt-1 font-bold
                                           text-blue-950">
                                {{ $evolucionesHistorial->count() }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold
                                           uppercase tracking-wide
                                           text-blue-500">
                                Última actualización
                            </p>

                            <p
                                class="mt-1 font-bold
                                           text-blue-950">
                                {{ $evolucionesHistorial
                                        ->first()
                                        ?->fecha
                                        ?->format('d/m/Y')
                                        ?? 'Sin evoluciones' }}
                            </p>
                        </div>
                    </div>

                    @if ($casoHistorial->descripcion_inicial)
                    <div
                        class="mt-4 border-t
                                       border-blue-200 pt-4">
                        <p
                            class="text-xs font-semibold
                                           uppercase tracking-wide
                                           text-blue-500">
                            Descripción inicial
                        </p>

                        <p
                            class="mt-1 whitespace-pre-line
                                           text-sm text-blue-950">
                            {{ $casoHistorial
                                        ->descripcion_inicial }}
                        </p>
                    </div>
                    @endif
                </section>

                {{-- Información del cierre --}}
                @if ($casoHistorial->estaCerrado())
                <section
                    aria-labelledby="titulo-cierre-historial"
                    class="mt-4 min-w-0 rounded-2xl border
               border-slate-300 bg-slate-100 p-5
               [overflow-wrap:anywhere]">

                    <h3
                        id="titulo-cierre-historial"
                        class="font-bold text-slate-900">
                        Cierre del caso
                    </h3>

                    <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="min-w-0">
                            <dt
                                class="text-xs font-semibold uppercase
                           tracking-wide text-slate-600">
                                Fecha de cierre
                            </dt>

                            <dd
                                class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $casoHistorial->fecha_cierre
                        ?->format('d/m/Y H:i')
                        ?? 'No disponible' }}
                            </dd>
                        </div>

                        <div class="min-w-0">
                            <dt
                                class="text-xs font-semibold uppercase
                           tracking-wide text-slate-600">
                                Cerrado por
                            </dt>

                            <dd
                                class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $casoHistorial->cerradoPor?->name
                        ?? 'Usuario no disponible' }}
                            </dd>
                        </div>

                        <div
                            class="min-w-0 border-t border-slate-300
                       pt-4 sm:col-span-2">
                            <dt
                                class="text-xs font-semibold uppercase
                           tracking-wide text-slate-600">
                                Motivo de cierre
                            </dt>

                            <dd
                                class="mt-1 whitespace-pre-line
                           text-sm leading-6 text-slate-800">{{ $casoHistorial->motivo_cierre ?: 'No disponible' }}</dd>
                        </div>
                    </dl>
                </section>
                @endif

                {{-- Línea cronológica --}}
                <div class="mt-6">
                    <h3
                        class="text-lg font-bold
                                   text-slate-900">
                        Evolución cita por cita
                    </h3>

                    <div
                        class="relative mt-5 space-y-4
                                   before:absolute before:bottom-0
                                   before:left-[15px] before:top-0
                                   before:w-px before:bg-blue-200
                                   sm:before:left-[19px]">
                        @foreach (
                        $evolucionesHistorial
                        as $indice => $evolucion
                        )
                        @php
                        $signos =
                        $evolucion->cita
                        ?->signoVital;

                        $aparatos =
                        $evolucion->aparatos;

                        $normales =
                        $aparatos
                        ->where(
                        'estado',
                        'normal'
                        )
                        ->count();

                        $atencion =
                        $aparatos
                        ->where(
                        'estado',
                        'requiere_atencion'
                        )
                        ->count();

                        $criticos =
                        $aparatos
                        ->where(
                        'estado',
                        'critico'
                        )
                        ->count();

                        $esEvolucionActual =
                        (int) $evolucion->id
                        === (int) $cita
                        ->evolucionClinica
                        ->id;
                        @endphp

                        <div
                            class="relative pl-10 sm:pl-12">
                            <span
                                class="absolute left-2 top-5
                                               h-4 w-4 rounded-full
                                               border-4 border-white
                                               shadow
                                               {{ $esEvolucionActual
                                                    ? 'bg-[#0D3B7F]'
                                                    : 'bg-blue-300' }}">
                            </span>

                            <details
                                class="group overflow-hidden
                                               rounded-2xl border
                                               {{ $esEvolucionActual
                                                    ? 'border-blue-300 bg-blue-50/40'
                                                    : 'border-slate-200 bg-white' }}"
                                @if ($indice===0) open @endif>
                                <summary
                                    class="flex cursor-pointer
                                                   list-none items-center
                                                   justify-between gap-4
                                                   px-5 py-4">
                                    <div>
                                        <div
                                            class="flex flex-wrap
                                                           items-center gap-2">
                                            <p
                                                class="font-bold
                                                               text-slate-900">
                                                {{ $evolucion
                                                            ->fecha
                                                            ->format('d/m/Y') }}
                                            </p>

                                            @if ($esEvolucionActual)
                                            <span
                                                class="rounded-full
                                                                   bg-blue-100
                                                                   px-2.5 py-1
                                                                   text-xs
                                                                   font-semibold
                                                                   text-blue-700">
                                                Cita actual
                                            </span>
                                            @endif
                                        </div>

                                        <p
                                            class="mt-1 text-sm
                                                           text-slate-500">
                                            Dr.
                                            {{ $evolucion
                                                        ->medico
                                                        ?->nombre }}
                                            {{ $evolucion
                                                        ->medico
                                                        ?->apellido_paterno }}
                                            · Cita
                                            #{{ $evolucion->cita_id }}
                                        </p>
                                    </div>

                                    <span
                                        class="text-slate-400
                                                       transition
                                                       group-open:rotate-180">
                                        ▾
                                    </span>
                                </summary>

                                <div
                                    class="border-t border-slate-200
                                                   px-5 py-5">
                                    <div
                                        class="grid gap-4
                                                       md:grid-cols-2">
                                        @foreach ([
                                        [
                                        'titulo' => 'Evolución clínica',
                                        'valor' => $evolucion
                                        ->evolucion_clinica,
                                        ],
                                        [
                                        'titulo' => 'Diagnóstico',
                                        'valor' => $evolucion
                                        ->diagnostico,
                                        ],
                                        [
                                        'titulo' => 'Tratamiento',
                                        'valor' => $evolucion
                                        ->tratamiento,
                                        ],
                                        [
                                        'titulo' => 'Plan y recomendaciones',
                                        'valor' => $evolucion
                                        ->plan_recomendaciones,
                                        ],
                                        [
                                        'titulo' => 'Indicaciones para enfermería',
                                        'valor' => $evolucion
                                        ->indicaciones_enfermeria,
                                        ],
                                        [
                                        'titulo' => 'Observaciones',
                                        'valor' => $evolucion
                                        ->observaciones,
                                        ],
                                        ] as $dato)
                                        <section
                                            class="rounded-xl
                                                               border
                                                               border-slate-200
                                                               bg-white p-4">
                                            <p
                                                class="text-xs
                                                                   font-semibold
                                                                   uppercase
                                                                   tracking-wide
                                                                   text-slate-400">
                                                {{ $dato['titulo'] }}
                                            </p>

                                            <p
                                                class="mt-2
                                                                   whitespace-pre-line
                                                                   text-sm
                                                                   text-slate-700">
                                                {{ $dato['valor']
                                                                ?: 'Sin registro' }}
                                            </p>
                                        </section>
                                        @endforeach
                                    </div>

                                    {{-- Enfermería --}}
                                    <section
                                        class="mt-4 rounded-xl
                                                       border
                                                       border-cyan-200
                                                       bg-cyan-50 p-4">
                                        <div
                                            class="flex items-center
                                                           justify-between
                                                           gap-4">
                                            <p
                                                class="font-bold
                                                               text-cyan-950">
                                                Enfermería
                                            </p>

                                            <span
                                                class="text-xs
                                                               font-semibold
                                                               text-cyan-700">
                                                {{ $signos
                                                            ? 'Registrada'
                                                            : 'Sin valoración' }}
                                            </span>
                                        </div>

                                        @if ($signos)
                                        <div
                                            class="mt-3 flex
                                                               flex-wrap gap-2">
                                            @foreach ([
                                            'Peso: '
                                            . $signos->peso
                                            . ' kg',
                                            'IMC: '
                                            . (
                                            $signos->imc
                                            ?? 'N/D'
                                            ),
                                            'Presión: '
                                            . (
                                            $signos
                                            ->presion_sistolica
                                            ?? 'N/D'
                                            )
                                            . '/'
                                            . (
                                            $signos
                                            ->presion_diastolica
                                            ?? 'N/D'
                                            ),
                                            'FC: '
                                            . (
                                            $signos
                                            ->frecuencia_cardiaca
                                            ?? 'N/D'
                                            ),
                                            'SatO₂: '
                                            . (
                                            $signos
                                            ->saturacion_oxigeno
                                            ?? 'N/D'
                                            )
                                            . '%',
                                            ] as $datoSigno)
                                            <span
                                                class="rounded-full
                                                                       bg-white
                                                                       px-3 py-1.5
                                                                       text-xs
                                                                       font-semibold
                                                                       text-cyan-800">
                                                {{ $datoSigno }}
                                            </span>
                                            @endforeach
                                        </div>
                                        @endif
                                    </section>

                                    {{-- Aparatos --}}
                                    <section
                                        class="mt-4 rounded-xl border
           border-amber-200 bg-amber-50 p-4">

                                        <div
                                            class="flex flex-col gap-3
               sm:flex-row sm:items-start
               sm:justify-between">

                                            <div>
                                                <p class="font-bold text-amber-950">
                                                    Aparatos
                                                </p>

                                                <p
                                                    class="mt-1 text-xs
                       text-amber-800/70">
                                                    Valoración correspondiente a esta
                                                    fecha y cita.
                                                </p>
                                            </div>

                                            <div class="flex flex-wrap gap-2">
                                                <span
                                                    class="rounded-full bg-emerald-100
                       px-3 py-1.5 text-xs
                       font-semibold text-emerald-700">
                                                    Normales:
                                                    {{ $normales }}
                                                </span>

                                                <span
                                                    class="rounded-full bg-amber-100
                       px-3 py-1.5 text-xs
                       font-semibold text-amber-700">
                                                    Atención:
                                                    {{ $atencion }}
                                                </span>

                                                <span
                                                    class="rounded-full bg-red-100
                       px-3 py-1.5 text-xs
                       font-semibold text-red-700">
                                                    Críticos:
                                                    {{ $criticos }}
                                                </span>
                                            </div>
                                        </div>

                                        @include(
                                        'citas.partials.historial-aparatos',
                                        [
                                        'aparatos' => $aparatos,
                                        ]
                                        )
                                    </section>
                                </div>
                            </details>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <footer
                class="flex shrink-0 flex-col gap-3 border-t
           border-slate-200 bg-white px-5 py-4
           sm:flex-row sm:justify-end sm:px-6">

                <button
                    type="button"
                    data-modal-clinico="graficas-evolucion"
                    class="w-full rounded-xl border border-[#0D3B7F]
               px-5 py-2.5 text-sm font-semibold
               text-[#0D3B7F] transition
               hover:bg-[#0D3B7F] hover:text-white
               focus-visible:outline-none
               focus-visible:ring-2
               focus-visible:ring-[#0D3B7F]
               focus-visible:ring-offset-2 sm:w-auto">
                    Ver gráficas
                </button>

                <button
                    type="button"
                    data-cerrar-modal-clinico
                    class="w-full rounded-xl bg-slate-900 px-5
               py-2.5 text-sm font-semibold
               text-white transition hover:bg-slate-700
               focus-visible:outline-none
               focus-visible:ring-2
               focus-visible:ring-slate-900
               focus-visible:ring-offset-2 sm:w-auto">
                    Cerrar
                </button>
            </footer>
        </div>
    </div>
</div>
@endif