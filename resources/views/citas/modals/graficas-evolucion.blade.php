@if (
$puedeConsultarInformacionClinica
&& $cita->evolucionClinica?->casoClinico
)
@php
$parametrosGraficas = [
'peso_imc' => 'Peso e IMC',
'presion' => 'Presión arterial',
'frecuencia_cardiaca' => 'Frecuencia cardiaca',
'frecuencia_respiratoria' => 'Frecuencia respiratoria',
'saturacion_oxigeno' => 'Saturación de oxígeno',
'temperatura' => 'Temperatura',
'glucosa' => 'Glucosa',
'estatura' => 'Estatura',
];
@endphp

<div
    id="modal-clinico-graficas-evolucion"
    data-modal-clinico-panel="graficas-evolucion"
    class="fixed inset-0 z-50 hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="titulo-modal-graficas">

    <div
        class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
        data-cerrar-modal-clinico>
    </div>

    <div
        class="relative flex min-h-full items-center
                   justify-center p-2 sm:p-5">

        <div
            class="relative flex max-h-[96vh] w-full
                       max-w-6xl flex-col overflow-hidden
                       rounded-2xl bg-white shadow-2xl">

            <header
                class="flex shrink-0 items-start
                           justify-between gap-5 border-b
                           border-slate-200 px-5 py-4 sm:px-6">

                <div class="min-w-0 flex-1 [overflow-wrap:anywhere]">
                    <p
                        class="text-xs font-semibold uppercase
                                   tracking-wide text-[#0D3B7F]">
                        Evolución histórica
                    </p>

                    <h2
                        id="titulo-modal-graficas"
                        class="mt-1 text-xl font-bold
                                   text-slate-900">
                        Gráficas de enfermería
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $cita->evolucionClinica
                                ->casoClinico
                                ->nombre }}
                        · únicamente citas de este caso
                    </p>
                </div>

                <button
                    type="button"
                    data-cerrar-modal-clinico
                    aria-label="Cerrar gráficas"
                    class="shrink-0 rounded-lg p-2 text-slate-600
       transition hover:bg-slate-100 hover:text-slate-900
       focus-visible:outline-none focus-visible:ring-2
       focus-visible:ring-[#0D3B7F]
       focus-visible:ring-offset-2">
                    ✕
                </button>
            </header>

            <div
                class="min-h-0 flex-1 overflow-y-auto
                           bg-slate-50/70 p-4 sm:p-6">

                {{-- Selector del parámetro --}}
                <div
                    class="flex flex-wrap gap-2 p-1"
                    role="group"
                    aria-label="Parámetro clínico">

                    @foreach (
                    $parametrosGraficas
                    as $clave => $etiqueta
                    )
                    <button
                        type="button"

                        aria-pressed="{{
    $loop->first
        ? 'true'
        : 'false'
}}"
                        data-selector-grafica="{{ $clave }}"
                        class="max-w-full rounded-full border
       px-3.5 py-2 text-xs whitespace-normal
       font-semibold transition
       focus-visible:outline-none focus-visible:ring-2
       focus-visible:ring-[#0D3B7F]
       focus-visible:ring-offset-2">
                        {{ $etiqueta }}
                    </button>
                    @endforeach
                </div>

                {{-- Gráfica principal --}}
                <section
                    class="mt-4 rounded-2xl border
                               border-slate-200 bg-white
                               p-4 shadow-sm sm:p-5">

                    <div
                        class="flex flex-col gap-1
                                   sm:flex-row sm:items-start
                                   sm:justify-between">

                        <div>
                            <h3
                                data-titulo-grafica
                                class="font-bold text-slate-900">
                            </h3>

                            <p
                                data-descripcion-grafica
                                class="mt-1 text-xs
                                           text-slate-500">
                            </p>
                        </div>

                        <p class="text-xs text-slate-400">
                            Los espacios sin captura no se
                            convierten en cero.
                        </p>
                    </div>

                    {{-- Cards KPI --}}
                    <div
                        data-kpis-grafica
                        class="mt-4 grid gap-3
                                   sm:grid-cols-2">
                    </div>

                    {{-- ApexCharts --}}
                    <div
                        data-grafica-evolucion-principal
                        class="mt-3 min-h-[270px]"
                        aria-live="polite">
                    </div>
                </section>
            </div>

            <footer
                class="flex shrink-0 justify-end
                           border-t border-slate-200
                           bg-white px-5 py-4 sm:px-6">

                <button
                    type="button"
                    data-cerrar-modal-clinico
                    class="w-full rounded-xl bg-slate-900
       px-5 py-2.5 text-sm font-semibold text-white
       transition hover:bg-slate-700
       focus-visible:outline-none focus-visible:ring-2
       focus-visible:ring-slate-900
       focus-visible:ring-offset-2 sm:w-auto">
                    Cerrar
                </button>
            </footer>
        </div>
    </div>
</div>
@endif