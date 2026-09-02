@if ($puedeConsultarInformacionClinica)
@php
$evolucionActual =
$cita->evolucionClinica;

$casoActual =
$evolucionActual?->casoClinico;

$aparatosEvaluados =
$evolucionActual
? $evolucionActual
->aparatos
->where(
'estado',
'!=',
'no_evaluado'
)
->count()
: 0;
@endphp

<section class="mt-8">
    <div class="mb-5">
      <p
    class="text-xs font-semibold uppercase
           tracking-wide text-[#0D3B7F]">
    Seguimiento clínico
</p>

<h2 class="mt-1 text-xl font-bold text-gray-900">
    Evolución y valoración por aparatos
</h2>

<p class="mt-1 text-sm text-gray-500">
    Registra el seguimiento de esta consulta y consulta
    la valoración correspondiente.
</p>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">

        {{-- Evolución clínica --}}
        <article
            class="flex flex-col rounded-2xl border
                       border-blue-200 bg-blue-50/40
                       p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                                   tracking-wide text-blue-500">
                        Seguimiento
                    </p>

                    <h3
                        class="mt-2 text-lg font-bold
                                   text-blue-950">
                        Evolución clínica
                    </h3>
                </div>

                <span
                    class="rounded-full px-3 py-1
           text-xs font-semibold
           {{ $casoActual?->estaCerrado()
                ? 'bg-red-100 text-red-700'
                : (
                    $evolucionActual
                        ? 'bg-blue-100 text-blue-700'
                        : 'bg-white text-slate-500'
                ) }}">
                    {{ $casoActual?->estaCerrado()
        ? 'Caso cerrado'
        : (
            $evolucionActual
                ? 'Registrada'
                : 'Sin evolución'
        ) }}
                </span>
            </div>

            @if ($evolucionActual)
            <p
                class="mt-3 text-sm font-semibold
                               text-blue-950">
                {{ $evolucionActual
                            ->casoClinico
                            ?->nombre }}
            </p>

          <p
    class="mt-1 flex-1 text-sm
           text-blue-700">
    {{ $casoActual?->estaCerrado()
        ? 'Seguimiento finalizado. Disponible solo para consulta.'
        : 'Evolución asociada a esta cita.' }}
</p>

            <button
                type="button"
                data-modal-clinico="evolucion"
                class="mt-5 inline-flex items-center
                               justify-center rounded-xl
                               bg-[#0D3B7F] px-4 py-2.5
                               text-sm font-semibold text-white
                               transition hover:bg-[#082a5d]">
                Ver evolución
            </button>

            @can(
            'cerrar',
            $evolucionActual->casoClinico
            )
            <button
                type="button"
                data-modal-clinico="cerrar-caso"
                class="mt-2 inline-flex items-center
               justify-center rounded-xl border
               border-red-300 bg-white
               px-4 py-2.5 text-sm
               font-semibold text-red-700
               transition hover:border-red-600
               hover:bg-red-600 hover:text-white">
                Cerrar caso
            </button>
            @endcan
            @else
            <p
                class="mt-3 flex-1 text-sm
                               leading-relaxed text-blue-700">
                Esta cita todavía no pertenece a
                ningún seguimiento clínico.
            </p>

            @if (request()->user()->isMedico())
            <button
                type="button"
                data-modal-clinico="crear-evolucion"
                class="mt-5 inline-flex items-center
                                   justify-center rounded-xl
                                   bg-[#0D3B7F] px-4 py-2.5
                                   text-sm font-semibold text-white
                                   transition hover:bg-[#082a5d]">
                Registrar evolución
            </button>
            @endif
            @endif
        </article>

        {{-- Aparatos --}}
        <article
            class="flex flex-col rounded-2xl border
                       border-amber-200 bg-amber-50/40
                       p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                                   tracking-wide text-amber-600">
                        Evaluación
                    </p>

                    <h3
                        class="mt-2 text-lg font-bold
                                   text-amber-950">
                        Aparatos
                    </h3>
                </div>

                <span
                    class="rounded-full bg-amber-100
                               px-3 py-1 text-xs font-semibold
                               text-amber-800">
                    {{ $aparatosEvaluados }}/18
                </span>
            </div>

            <p
                class="mt-3 flex-1 text-sm
                           leading-relaxed text-amber-800">
                Valoración independiente de los aparatos
                para esta evolución.
            </p>

            @if ($evolucionActual)
            <button
                type="button"
                data-modal-clinico="aparatos"
                class="mt-5 inline-flex items-center
                               justify-center rounded-xl border
                               border-amber-700 px-4 py-2.5
                               text-sm font-semibold text-amber-800
                               transition hover:bg-amber-700
                               hover:text-white">
                Ver aparatos
            </button>
            @else
            <span
                class="mt-5 rounded-xl bg-white px-4
                               py-2.5 text-center text-sm
                               font-medium text-slate-400">
                Requiere una evolución
            </span>
            @endif
        </article>

    </div>
</section>
@endif
