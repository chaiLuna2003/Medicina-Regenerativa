@if ($puedeConsultarInformacionClinica)
@php
$evolucionActual =
$cita->evolucionClinica;

$casoActual =
$evolucionActual?->casoClinico;

$historiaClinica =
$cita->paciente?->historiaClinica;

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
            Expediente clínico
        </p>

        <h2
            class="mt-1 text-xl font-bold
                       text-gray-900">
            Información clínica de la cita
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Consulta el expediente y registra el seguimiento
            correspondiente a esta atención.
        </p>
    </div>

    <div
        class="grid gap-4
                   sm:grid-cols-2 xl:grid-cols-3">

        {{-- Historia clínica --}}
        <article
            class="flex flex-col rounded-2xl border
                       border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                                   tracking-wide text-slate-400">
                        Paciente
                    </p>

                    <h3
                        class="mt-2 text-lg font-bold
                                   text-slate-900">
                        Historia clínica
                    </h3>
                </div>

                <span
                    class="rounded-full px-3 py-1
                               text-xs font-semibold
                               {{ $historiaClinica
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'bg-slate-100 text-slate-500' }}">
                    {{ $historiaClinica
                            ? 'Disponible'
                            : 'Sin registro' }}
                </span>
            </div>

            <p
                class="mt-3 flex-1 text-sm
                           leading-relaxed text-slate-500">
                Antecedentes y datos clínicos generales
                del paciente.
            </p>

            <button
                type="button"
                data-modal-clinico="historia-clinica"
                class="mt-5 inline-flex items-center
                           justify-center rounded-xl border
                           border-[#0D3B7F] px-4 py-2.5
                           text-sm font-semibold text-[#0D3B7F]
                           transition hover:bg-[#0D3B7F]
                           hover:text-white">
                Ver historia clínica
            </button>
        </article>

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

        {{-- Enfermería --}}
        <article
            class="flex flex-col rounded-2xl border
                       border-cyan-200 bg-cyan-50/40
                       p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                                   tracking-wide text-cyan-600">
                        Valoración
                    </p>

                    <h3
                        class="mt-2 text-lg font-bold
                                   text-cyan-950">
                        Enfermería
                    </h3>
                </div>

                <span
                    class="rounded-full px-3 py-1
                               text-xs font-semibold
                               {{ $cita->signoVital
                                    ? 'bg-cyan-100 text-cyan-700'
                                    : 'bg-white text-slate-500' }}">
                    {{ $cita->signoVital
                            ? 'Registrada'
                            : 'Pendiente' }}
                </span>
            </div>

            <p
                class="mt-3 flex-1 text-sm
                           leading-relaxed text-cyan-800">
                Peso, presión, temperatura y demás
                parámetros de esta cita.
            </p>

            <button
                type="button"
                data-modal-clinico="enfermeria"
                class="mt-5 inline-flex items-center
                           justify-center rounded-xl border
                           border-cyan-700 px-4 py-2.5
                           text-sm font-semibold text-cyan-800
                           transition hover:bg-cyan-700
                           hover:text-white">
                Ver valoración
            </button>
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

        {{-- Estudios --}}
        <article
            class="flex flex-col rounded-2xl border
                       border-violet-200 bg-violet-50/40
                       p-5 shadow-sm">
            <p
                class="text-xs font-semibold uppercase
                           tracking-wide text-violet-600">
                Documentos
            </p>

            <h3
                class="mt-2 text-lg font-bold
                           text-violet-950">
                Estudios clínicos
            </h3>

            <p
                class="mt-3 flex-1 text-sm
                           text-violet-800">
                {{ $cita->estudios->count() }}
                archivo(s) asociado(s) con esta cita.
            </p>

            <a
                href="{{ route(
                        'pacientes.estudios.index',
                        $cita->paciente
                    ) }}"
                class="mt-5 inline-flex items-center
                           justify-center rounded-xl border
                           border-violet-700 px-4 py-2.5
                           text-sm font-semibold text-violet-800
                           transition hover:bg-violet-700
                           hover:text-white">
                Ver estudios
            </a>
        </article>

        {{-- Receta --}}
        <article
            class="flex flex-col rounded-2xl border
                       border-emerald-200 bg-emerald-50/40
                       p-5 shadow-sm">
            <p
                class="text-xs font-semibold uppercase
                           tracking-wide text-emerald-600">
                Tratamiento
            </p>

            <h3
                class="mt-2 text-lg font-bold
                           text-emerald-950">
                Receta médica
            </h3>

            <p
                class="mt-3 flex-1 text-sm
                           text-emerald-800">
                {{ $cita->receta
                        ? 'La cita tiene una receta registrada.'
                        : 'Esta cita todavía no tiene receta.' }}
            </p>

            @if ($cita->receta)
            <a
                href="{{ route(
                            'recetas.show',
                            $cita->receta
                        ) }}"
                class="mt-5 inline-flex items-center
                               justify-center rounded-xl border
                               border-emerald-700 px-4 py-2.5
                               text-sm font-semibold
                               text-emerald-800 transition
                               hover:bg-emerald-700
                               hover:text-white">
                Ver receta
            </a>
            @elseif (request()->user()->isMedico())
            <a
                href="{{ route(
                            'citas.receta.create',
                            $cita
                        ) }}"
                class="mt-5 inline-flex items-center
                               justify-center rounded-xl
                               bg-emerald-700 px-4 py-2.5
                               text-sm font-semibold text-white
                               transition hover:bg-emerald-800">
                Crear receta
            </a>
            @endif
        </article>
    </div>
</section>
@endif