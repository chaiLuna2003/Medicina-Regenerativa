<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Agenda de citas
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Consulta y administra las citas registradas.
                </p>
            </div>

            @if (in_array(auth()->user()->role, ['admin', 'recepcionista'], true))
            <a
                href="{{ route('citas.create') }}"
                class="inline-flex items-center justify-center rounded-xl
                           bg-[#0D3B7F] px-5 py-2.5 text-sm font-semibold
                           text-white transition hover:bg-[#082a5d]">
                Registrar nueva cita
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Mensaje de éxito --}}
            @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
            @endif

            {{-- Filtros de búsqueda --}}
            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form
                    method="GET"
                    action="{{ route('citas.index') }}"
                    class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    {{-- Buscar paciente --}}
                    <div>
                        <label
                            for="buscar"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Paciente
                        </label>

                        <input
                            type="search"
                            id="buscar"
                            name="buscar"
                            value="{{ request('buscar') }}"
                            placeholder="Nombre o apellido"
                            class="w-full rounded-xl border-gray-300 text-sm
                                   shadow-sm focus:border-[#0D3B7F]
                                   focus:ring-[#0D3B7F]">
                    </div>

                    {{-- Filtrar por médico --}}
                    @if (auth()->user()->role !== 'medico')
                    <div>
                        <label
                            for="medico_id"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Médico
                        </label>

                        <select
                            id="medico_id"
                            name="medico_id"
                            class="w-full rounded-xl border-gray-300 text-sm
                                       shadow-sm focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">
                            <option value="">
                                Todos los médicos
                            </option>

                            @foreach ($medicos as $medico)
                            <option
                                value="{{ $medico->id }}"
                                @selected(
                                (string) request('medico_id')===(string) $medico->id
                                )
                                >
                                Dr. {{ $medico->nombre }}
                                {{ $medico->apellido_paterno }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Filtrar por modalidad --}}
                    <div>
                        <label
                            for="modalidad"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Modalidad
                        </label>

                        <select
                            id="modalidad"
                            name="modalidad"
                            class="w-full rounded-xl border-gray-300 text-sm
                                   shadow-sm focus:border-[#0D3B7F]
                                   focus:ring-[#0D3B7F]">
                            <option value="">
                                Todas las modalidades
                            </option>

                            <option
                                value="presencial"
                                @selected(request('modalidad')==='presencial' )>
                                Presencial
                            </option>

                            <option
                                value="videoconsulta"
                                @selected(request('modalidad')==='videoconsulta' )>
                                Videoconsulta
                            </option>
                        </select>
                    </div>

                    {{-- Acciones de filtros --}}
                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="inline-flex flex-1 items-center justify-center
                                   rounded-xl bg-[#0D3B7F] px-5 py-2.5
                                   text-sm font-semibold text-white transition
                                   hover:bg-[#082a5d]">
                            Buscar
                        </button>

                        @if (
                        request()->filled('buscar') ||
                        request()->filled('medico_id') ||
                        request()->filled('modalidad')
                        )
                        <a
                            href="{{ route('citas.index') }}"
                            class="inline-flex items-center justify-center
                                       rounded-xl border border-gray-300
                                       bg-white px-4 py-2.5 text-sm
                                       font-semibold text-gray-700 transition
                                       hover:bg-gray-100">
                            Limpiar
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Listado --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                @if ($citas->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                        <svg
                            class="h-7 w-7"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3.75
                                       18.75V7.5A2.25 2.25 0 0 1 6
                                       5.25h12a2.25 2.25 0 0 1 2.25
                                       2.25v11.25M3.75 18.75A2.25
                                       2.25 0 0 0 6 21h12a2.25
                                       2.25 0 0 0 2.25-2.25M3.75
                                       18.75v-7.5A2.25 2.25 0 0 1
                                       6 9h12a2.25 2.25 0 0 1 2.25
                                       2.25v7.5" />
                        </svg>
                    </div>

                    <h3 class="mt-4 text-lg font-semibold text-gray-900">
                        @if (
                        request()->filled('buscar') ||
                        request()->filled('medico_id') ||
                        request()->filled('modalidad')
                        )
                        No encontramos citas
                        @else
                        No hay citas registradas
                        @endif
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        @if (
                        request()->filled('buscar') ||
                        request()->filled('medico_id') ||
                        request()->filled('modalidad')
                        )
                        Intenta modificar o limpiar los filtros de búsqueda.
                        @else
                        Registra la primera cita para comenzar a construir la agenda.
                        @endif
                    </p>

                    @if (
                    request()->filled('buscar') ||
                    request()->filled('medico_id') ||
                    request()->filled('modalidad')
                    )
                    <a
                        href="{{ route('citas.index') }}"
                        class="mt-6 inline-flex rounded-xl border
                                       border-gray-300 bg-white px-5 py-2.5
                                       text-sm font-semibold text-gray-700
                                       transition hover:bg-gray-100">
                        Limpiar filtros
                    </a>
                    @elseif (in_array(auth()->user()->role, ['admin', 'recepcionista'], true))
                    <a
                        href="{{ route('citas.create') }}"
                        class="mt-6 inline-flex rounded-xl bg-[#0D3B7F]
                                       px-5 py-2.5 text-sm font-semibold
                                       text-white transition hover:bg-[#082a5d]">
                        Registrar cita
                    </a>
                    @endif
                </div>
                @else
                {{-- Resumen --}}
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-semibold text-gray-900">
                                Citas registradas
                            </h3>

                            <span
                                class="inline-flex rounded-full bg-blue-50 px-2.5 py-1
                   text-xs font-semibold text-[#0D3B7F]">
                                Más recientes primero
                            </span>
                        </div>

                        <p class="mt-1 text-sm text-gray-500">
                            Mostrando {{ $citas->firstItem() }}–{{ $citas->lastItem() }}
                            de {{ $citas->total() }} resultados
                        </p>
                    </div>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1150px]">
                        <thead class="bg-slate-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Fecha
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Paciente
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Médico
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Motivo
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Modalidad
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Estado
                                </th>

                                <th class="sticky right-0 bg-slate-50 px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($citas as $cita)
                            @php
                            $estadoActual = $cita->estado_actual;

                            $estadoClases = match ($estadoActual) {
                            'programada' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                            'en_curso' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                            'finalizada' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'cancelada' => 'bg-red-50 text-red-700 ring-red-600/20',
                            default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                            };

                            $estadoPunto = match ($estadoActual) {
                            'programada' => 'bg-blue-500',
                            'en_curso' => 'bg-amber-500',
                            'finalizada' => 'bg-emerald-500',
                            'cancelada' => 'bg-red-500',
                            default => 'bg-gray-500',
                            };

                            $estadoTexto = match ($estadoActual) {
                            'programada' => 'Programada',
                            'en_curso' => 'En curso',
                            'finalizada' => 'Finalizada',
                            'cancelada' => 'Cancelada',
                            default => 'Sin estado',
                            };

                            $motivoClases = match ($cita->motivo) {
                            'consulta_inicial' =>
                            'bg-blue-50 text-blue-700 ring-blue-600/20',

                            'consulta_subsecuente' =>
                            'bg-emerald-50 text-emerald-700 ring-emerald-600/20',

                            'consulta_emergencia' =>
                            'bg-red-50 text-red-700 ring-red-600/20',

                            default =>
                            'bg-gray-50 text-gray-700 ring-gray-600/20',
                            };

                            $motivoPunto = match ($cita->motivo) {
                            'consulta_inicial' => 'bg-blue-500',
                            'consulta_subsecuente' => 'bg-emerald-500',
                            'consulta_emergencia' => 'bg-red-500',
                            default => 'bg-gray-500',
                            };

                            $nombrePaciente = trim(
                            ($cita->paciente?->nombre ?? '') . ' ' .
                            ($cita->paciente?->apellido ?? '')
                            );

                            $inicialPaciente = mb_strtoupper(
                            mb_substr(
                            $cita->paciente?->nombre ?? 'P',
                            0,
                            1
                            )
                            );
                            @endphp

                            <tr class="group transition hover:bg-slate-50">

                                {{-- Fecha y hora --}}
                                <td class="whitespace-nowrap px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 flex-col items-center justify-center rounded-xl bg-[#0D3B7F]/10 text-[#0D3B7F]">
                                            <span class="text-xs font-semibold uppercase">
                                                {{ $cita->fecha->translatedFormat('M') }}
                                            </span>

                                            <span class="text-lg font-bold leading-none">
                                                {{ $cita->fecha->format('d') }}
                                            </span>
                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $cita->fecha->format('d/m/Y') }}
                                            </p>

                                            <p class="mt-1 text-sm font-medium text-gray-600">
                                                {{ \Carbon\Carbon::parse(
                                                 $cita->hora
                                                    )->format('h:i A') }}

                                                <span class="mx-1 text-gray-400">
                                                    –
                                                </span>

                                                {{ $cita->hora_fin->format('h:i A') }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-400">
                                                {{ $cita->duracion_minutos ?? 15 }}
                                                minutos
                                            </p>

                                            <p
                                                class="mt-2 text-xs text-gray-400"
                                                title="{{ $cita->created_at->format('d/m/Y h:i A') }}">
                                                Registrada {{ $cita->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Paciente --}}
                                <td class="whitespace-nowrap px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                                            {{ $inicialPaciente }}
                                        </div>

                                        <div>
                                            <p class="font-semibold text-gray-900">
                                                {{ $nombrePaciente ?: 'Paciente no disponible' }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                ID #{{ $cita->paciente_id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Médico --}}
                                <td class="whitespace-nowrap px-6 py-5">
                                    <p class="font-semibold text-gray-900">
                                        Dr. {{ $cita->medico?->nombre }}
                                        {{ $cita->medico?->apellido_paterno }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $cita->medico?->especialidad ?: 'Sin especialidad registrada' }}
                                    </p>
                                </td>

                                {{-- Motivo --}}
                                {{-- Motivo --}}
                                <td class="px-6 py-5">
                                    <span
                                        title="{{ $cita->motivo_texto }}"
                                        class="inline-flex max-w-[240px] items-center gap-2
               rounded-full px-3 py-1.5 text-xs font-semibold
               ring-1 ring-inset {{ $motivoClases }}">
                                        <span
                                            class="h-2 w-2 shrink-0 rounded-full
                   {{ $motivoPunto }}"></span>

                                        <span class="truncate">
                                            {{ $cita->motivo_texto }}
                                        </span>
                                    </span>
                                </td>

                                {{-- Modalidad --}}
                                <td class="whitespace-nowrap px-6 py-5">
                                    @if ($cita->modalidad === 'videoconsulta')
                                    <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                        Videoconsulta
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        Presencial
                                    </span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="whitespace-nowrap px-6 py-5">
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $estadoClases }}">
                                        <span class="h-2 w-2 rounded-full {{ $estadoPunto }}"></span>
                                        {{ $estadoTexto }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="sticky right-0 whitespace-nowrap bg-white px-6 py-5 text-right transition group-hover:bg-slate-50">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('citas.show', $cita) }}"
                                            title="Ver detalles"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-gray-400 hover:bg-gray-100">
                                            Ver
                                        </a>

                                        @if (in_array(auth()->user()->role, ['admin', 'recepcionista'], true))
                                        <a
                                            href="{{ route('citas.edit', $cita) }}"
                                            title="Editar cita"
                                            class="inline-flex items-center gap-2 rounded-lg border border-[#0D3B7F] px-3 py-2 text-sm font-semibold text-[#0D3B7F] transition hover:bg-[#0D3B7F] hover:text-white">
                                            <svg
                                                class="h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.8"
                                                stroke="currentColor">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875
                                                                   1.875 0 1 1 2.652 2.652L10.582
                                                                   16.07a4.5 4.5 0 0 1-1.897
                                                                   1.13L6 18l.8-2.685a4.5 4.5
                                                                   0 0 1 1.13-1.897l8.932-8.931Z" />
                                            </svg>

                                            Editar
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $citas->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>