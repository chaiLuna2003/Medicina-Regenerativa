<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

    {{-- Información del paciente --}}
    <div class="flex items-start gap-3">

        <a
            href="{{ request()->user()->isMedico()
                ? route('citas.index')
                : route('pacientes.index') }}"
            class="mt-1 text-slate-400 transition hover:text-slate-700">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        <div>
            <h2 class="text-xl font-semibold text-slate-900">
                {{ $pacientes->nombre }}
                {{ $pacientes->apellido }}
            </h2>

            <p class="text-sm text-slate-500">
                Ficha del paciente
            </p>

            @if ($ultimaActividad)

            <div class="mt-2 flex flex-wrap items-center gap-2">

                <span class="inline-flex h-2 w-2 rounded-full bg-blue-500"></span>

                <p class="text-xs text-slate-500">
                    Última actividad:

                    <span class="font-semibold text-slate-700">
                        {{ $ultimaActividad['titulo'] }}
                    </span>

                    <span class="mx-1 text-slate-300">
                        ·
                    </span>

                    {{ $ultimaActividad['fecha']->format('d/m/Y') }}

                    <span class="text-slate-400">
                        {{ $ultimaActividad['fecha']->format('h:i A') }}
                    </span>
                </p>
            </div>

            @else

            <p class="mt-2 text-xs text-slate-400">
                Sin actividad clínica registrada
            </p>

            @endif
        </div>
    </div>

    {{-- Acciones --}}
    <div class="flex flex-wrap items-center gap-3">

        @if (
        request()->user()->isAdmin()
        || request()->user()->isRecepcionista()
        || request()->user()->isMedico()
        )
        <a
            data-accion-datos-personales
            href="{{ route('pacientes.edit', $pacientes) }}"
            class="inline-flex items-center justify-center gap-2
                   rounded-xl border border-slate-300 bg-white
                   px-4 py-2 text-sm font-semibold
                   text-slate-700 shadow-sm transition
                   hover:border-blue-300 hover:bg-blue-50
                   hover:text-blue-700">

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15.232 5.232l3.536 3.536
                       M9 11l6.232-6.232a2.5 2.5
                       0 0 1 3.536 3.536L12.536 14.536
                       M9 11l-1 4 4-1M5 19h14" />
            </svg>

            {{ request()->user()->isMedico()
                ? 'Consulta/edición de datos personales'
                : 'Editar datos' }}
        </a>
        @endif

        @if (
        request()->user()->isAdmin()
        || request()->user()->isRecepcionista()
        )
        <button
            type="button"
            onclick="abrirModalEstudiosPaciente()"
            class="inline-flex items-center justify-center gap-2
                       rounded-xl bg-[#0D3B7F] px-4 py-2
                       text-sm font-semibold text-white
                       shadow-sm transition
                       hover:bg-[#082a5d]
                       focus:outline-none focus:ring-2
                       focus:ring-[#0D3B7F]/30">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 4v16m8-8H4" />
            </svg>

            Agregar estudio
        </button>
        @endif

        @if (
        request()->user()->isAdmin()
        || request()->user()->isMedico()
        )
        @if ($pacientes->ultimoHistoriaClinicaDocumento)
        @php
        $ultimoExpediente =
        $pacientes->ultimoHistoriaClinicaDocumento;
        @endphp

        <div
            class="inline-flex overflow-hidden rounded-xl
                   border border-emerald-600 shadow-sm">

            {{-- Abrir última versión --}}
            <a
                href="{{
                    route(
                        'historias-clinicas.documentos.archivo',
                        $ultimoExpediente
                    )
                }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center
                       gap-2 bg-emerald-600 px-4 py-2
                       text-sm font-semibold text-white
                       transition hover:bg-emerald-700">

                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7
                           a2 2 0 01-2-2V5
                           a2 2 0 012-2h5.586
                           a1 1 0 01.707.293
                           l3.414 3.414A1 1 0 0117 7.414V19
                           a2 2 0 01-2 2z" />
                </svg>

                Ver expediente
            </a>

            {{-- Descargar última versión --}}
            <a
                href="{{
                    route(
                        'historias-clinicas.documentos.descargar',
                        $ultimoExpediente
                    )
                }}"
                class="inline-flex items-center justify-center
                       border-l border-emerald-500
                       bg-emerald-600 px-3 py-2
                       text-white transition
                       hover:bg-emerald-700"
                title="Descargar expediente"
                aria-label="Descargar expediente">

                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10
                           a3 3 0 003-3v-1
                           M12 4v12m0 0l-4-4m4 4l4-4" />
                </svg>
            </a>

            {{-- Generar una versión actualizada --}}
            <form
                method="POST"
                action="{{
                    route(
                        'pacientes.historia-clinica.documentos.store',
                        $pacientes
                    )
                }}"
                target="_blank"
                class="inline-flex">

                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center justify-center
                           border-l border-emerald-500
                           bg-emerald-600 px-3 py-2
                           text-white transition
                           hover:bg-emerald-700"
                    title="Generar versión actualizada"
                    aria-label="Generar versión actualizada">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v5h.582m15.356 2
                               A8.001 8.001 0 004.582 9
                               m0 0H9m11 11v-5h-.581
                               m0 0a8.003 8.003 0 01-15.357-2
                               m15.357 2H15" />
                    </svg>
                </button>
            </form>
        </div>
        @else
        {{-- Primera generación --}}
        <form
            method="POST"
            action="{{
                route(
                    'pacientes.historia-clinica.documentos.store',
                    $pacientes
                )
            }}"
            target="_blank">

            @csrf

            <button
                type="submit"
                class="inline-flex items-center justify-center
                       gap-2 rounded-xl bg-emerald-600
                       px-4 py-2 text-sm font-semibold
                       text-white shadow-sm transition
                       hover:bg-emerald-700">

                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4" />
                </svg>

                Generar expediente
            </button>
        </form>
        @endif
        @endif

        <span
            class="rounded-full px-3 py-1 text-xs font-semibold
                   {{ $pacientes->status
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'bg-red-50 text-red-700' }}">

            {{ $pacientes->status
                ? 'Activo'
                : 'Inactivo' }}
        </span>
    </div>
</div>