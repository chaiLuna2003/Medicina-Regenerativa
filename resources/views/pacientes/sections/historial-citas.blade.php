  {{-- ================================================= --}}
                    {{-- CITAS --}}
                    {{-- ================================================= --}}
                    <details
                        class="group overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm"
                        open>
                        {{-- Encabezado del acordeón --}}
                        <summary
                            class="flex cursor-pointer
               list-none items-center
               justify-between gap-4
               px-6 py-5">
                            <div class="flex items-center gap-3">

                                {{-- Icono --}}
                                <div
                                    class="flex h-9 w-9 items-center
                       justify-center rounded-lg
                       bg-blue-50 text-blue-600">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Historial de citas
                                    </h3>

                                    <p class="text-xs text-slate-400">
                                        Consultas y expediente asociado
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">

                                {{-- Contador --}}
                                <span
                                    class="rounded-full bg-blue-50
                       px-2.5 py-1
                       text-xs font-semibold
                       text-blue-700">
                                    {{ $pacientes->citas->count() }}
                                </span>

                                {{-- Flecha --}}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                       transition
                       group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        {{-- Contenido --}}
                        <div class="border-t border-slate-100">

                            @if ($pacientes->citas->isEmpty())

                            <div
                                class="px-6 py-10 text-center
                       text-sm text-slate-500">
                                <p class="font-medium text-slate-600">
                                    No hay citas registradas.
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Las consultas del paciente aparecerán aquí.
                                </p>
                            </div>

                            @else

                            <div class="overflow-x-auto">

                                <table class="min-w-full">

                                    {{-- ================================================= --}}
                                    {{-- ENCABEZADOS --}}
                                    {{-- ================================================= --}}
                                    <thead class="bg-slate-50">
                                        <tr>

                                            <th
                                                class="px-5 py-3
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500">
                                                Fecha
                                            </th>

                                            <th
                                                class="px-5 py-3
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500">
                                                Médico
                                            </th>

                                            <th
                                                class="px-5 py-3
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500">
                                                Motivo
                                            </th>

                                            <th
                                                class="px-5 py-3
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500">
                                                Estado
                                            </th>

                                            <th
                                                class="px-5 py-3
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500">
                                                Expediente
                                            </th>

                                            <th
                                                class="px-5 py-3
                                       text-right
                                       text-xs font-semibold
                                       text-slate-500">
                                                Acción
                                            </th>

                                        </tr>
                                    </thead>

                                    {{-- ================================================= --}}
                                    {{-- CITAS --}}
                                    {{-- ================================================= --}}
                                    <tbody class="divide-y divide-slate-100">

                                        @foreach ($pacientes->citas as $cita)

                                        @php
                                        /*
                                        |--------------------------------------------------------------------------
                                        | Motivo legible
                                        |--------------------------------------------------------------------------
                                        */

                                        $motivoTexto = match ($cita->motivo) {
                                        'consulta_inicial'
                                        => 'Consulta inicial',

                                        'consulta_subsecuente'
                                        => 'Consulta subsecuente',

                                        'consulta_emergencia'
                                        => 'Consulta de emergencia',

                                        default => $cita->motivo
                                        ? ucfirst(
                                        str_replace(
                                        '_',
                                        ' ',
                                        $cita->motivo
                                        )
                                        )
                                        : 'No especificado',
                                        };

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Estado calculado
                                        |--------------------------------------------------------------------------
                                        */

                                        $estado = $cita->estado_actual;

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Recursos asociados
                                        |--------------------------------------------------------------------------
                                        */

                                        $tieneSignos =
                                        $cita->signoVital !== null;

                                        $tieneReceta =
                                        $cita->receta !== null;

                                        $cantidadEstudios =
                                        $cita->estudios->count();
                                        @endphp

                                        <tr
                                            class="transition
                                       hover:bg-slate-50/70">

                                            {{-- ===================================== --}}
                                            {{-- FECHA --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="whitespace-nowrap
                                           px-5 py-4
                                           align-top
                                           text-sm text-slate-700">
                                                <p class="font-semibold text-slate-800">
                                                    {{ $cita->fecha?->format('d/m/Y')
                                            ?? '—' }}
                                                </p>

                                                <p
                                                    class="mt-0.5
                                               text-xs text-slate-400">
                                                    {{ $cita->hora
                                            ? \Carbon\Carbon::parse(
                                                $cita->hora
                                            )->format('h:i A')
                                            : '—' }}
                                                </p>
                                            </td>

                                            {{-- ===================================== --}}
                                            {{-- MÉDICO --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="px-5 py-4
                                           align-top
                                           text-sm text-slate-700">
                                                <p class="font-medium text-slate-800">
                                                    {{ $cita
                                            ->medico
                                            ?->user
                                            ?->name
                                            ?? 'No disponible' }}
                                                </p>

                                                @if ($cita->medico?->especialidad)
                                                <p
                                                    class="mt-0.5
                                                   text-xs text-slate-400">
                                                    {{ $cita->medico->especialidad }}
                                                </p>
                                                @endif
                                            </td>

                                            {{-- ===================================== --}}
                                            {{-- MOTIVO --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="px-5 py-4
                                           align-top
                                           text-sm text-slate-700">
                                                <p class="font-medium">
                                                    {{ $motivoTexto }}
                                                </p>

                                                @if ($cita->modalidad)
                                                <p
                                                    class="mt-1
                                                   text-xs text-slate-400">
                                                    {{ ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $cita->modalidad
                                                )
                                            ) }}
                                                </p>
                                                @endif
                                            </td>

                                            {{-- ===================================== --}}
                                            {{-- ESTADO --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="px-5 py-4
                                           align-top">
                                                <span
                                                    @class([ 'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold' , 'bg-emerald-50 text-emerald-700'=> $estado === 'finalizada',

                                                    'bg-blue-50 text-blue-700'
                                                    => $estado === 'programada',

                                                    'bg-amber-50 text-amber-700'
                                                    => $estado === 'en_curso',

                                                    'bg-red-50 text-red-700'
                                                    => $estado === 'cancelada',

                                                    'bg-slate-100 text-slate-600'
                                                    => !in_array(
                                                    $estado,
                                                    [
                                                    'finalizada',
                                                    'programada',
                                                    'en_curso',
                                                    'cancelada',
                                                    ],
                                                    true
                                                    ),
                                                    ])
                                                    >
                                                    {{ ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $estado
                                            )
                                        ) }}
                                                </span>
                                            </td>

                                            {{-- ===================================== --}}
                                            {{-- EXPEDIENTE --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="px-5 py-4
                                           align-top">
                                                <div class="space-y-2">

                                                    {{-- Signos vitales --}}
                                                    <div
                                                        class="flex items-center
                                                   gap-2">
                                                        @if ($tieneSignos)

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-emerald-500"></span>

                                                        <span
                                                            class="text-xs
                                                           font-medium
                                                           text-slate-600">
                                                            Signos vitales
                                                        </span>

                                                        @else

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-slate-300"></span>

                                                        <span
                                                            class="text-xs
                                                           text-slate-400">
                                                            Sin signos vitales
                                                        </span>

                                                        @endif
                                                    </div>

                                                    {{-- Receta --}}
                                                    <div
                                                        class="flex items-center
                                                   gap-2">
                                                        @if ($tieneReceta)

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-emerald-500"></span>

                                                        <span
                                                            class="text-xs
                                                           font-medium
                                                           text-slate-600">
                                                            Receta emitida
                                                        </span>

                                                        @else

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-slate-300"></span>

                                                        <span
                                                            class="text-xs
                                                           text-slate-400">
                                                            Sin receta
                                                        </span>

                                                        @endif
                                                    </div>

                                                    {{-- Estudios --}}
                                                    <div
                                                        class="flex items-center
                                                   gap-2">
                                                        @if ($cantidadEstudios > 0)

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-violet-500"></span>

                                                        <span
                                                            class="text-xs
                                                           font-medium
                                                           text-slate-600">
                                                            {{ $cantidadEstudios }}

                                                            {{ $cantidadEstudios === 1
                                                        ? 'estudio'
                                                        : 'estudios' }}
                                                        </span>

                                                        @else

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-slate-300"></span>

                                                        <span
                                                            class="text-xs
                                                           text-slate-400">
                                                            Sin estudios
                                                        </span>

                                                        @endif
                                                    </div>

                                                </div>
                                            </td>

                                            {{-- ===================================== --}}
                                            {{-- ACCIÓN --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="whitespace-nowrap
                                           px-5 py-4
                                           text-right
                                           align-top">
                                                <a
                                                    href="{{ route(
                                            'citas.show',
                                            $cita
                                        ) }}"
                                                    class="inline-flex
                                               items-center gap-1.5
                                               rounded-lg
                                               border border-slate-200
                                               bg-white
                                               px-3 py-2
                                               text-xs font-semibold
                                               text-slate-700
                                               shadow-sm
                                               transition
                                               hover:border-blue-200
                                               hover:bg-blue-50
                                               hover:text-blue-700">
                                                    Ver cita

                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-3.5 w-3.5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="2">
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </td>

                                        </tr>

                                        @endforeach

                                    </tbody>
                                </table>

                            </div>

                            @endif

                        </div>
                    </details>