<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-600">
                    Panel de recepción
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Buenos días, {{ auth()->user()->name }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a
                    href="{{ route('citas.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl
                           bg-[#0D3B7F] px-5 py-3 text-sm font-semibold text-white
                           shadow-sm transition hover:bg-[#082a5d]"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                    Nueva cita
                </a>

                <a
                    href="{{ route('pacientes.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl
                           border border-gray-300 bg-white px-5 py-3 text-sm
                           font-semibold text-gray-700 shadow-sm transition
                           hover:border-gray-400 hover:bg-gray-50"
                >
                    Nuevo paciente
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- Indicadores de hoy --}}
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Total --}}
                <article class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="absolute inset-x-0 top-0 h-1 bg-blue-500"></div>

                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Citas de hoy
                            </p>

                            <p class="mt-3 text-3xl font-bold text-gray-900">
                                {{ $totalCitasHoy }}
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3M5 11h14M5 5h14a2
                                       2 0 012 2v12a2 2 0 01-2 2H5a2
                                       2 0 01-2-2V7a2 2 0 012-2z"
                                />
                            </svg>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-gray-400">
                        Total de consultas registradas
                    </p>
                </article>

                {{-- En espera --}}
                <article class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="absolute inset-x-0 top-0 h-1 bg-amber-500"></div>

                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                En espera
                            </p>

                            <p class="mt-3 text-3xl font-bold text-gray-900">
                                {{ $citasEnEspera }}
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 2m6-2a9 9 0
                                       11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-gray-400">
                        Pendientes de atención
                    </p>
                </article>

                {{-- Confirmadas --}}
                <article class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="absolute inset-x-0 top-0 h-1 bg-emerald-500"></div>

                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Confirmadas
                            </p>

                            <p class="mt-3 text-3xl font-bold text-gray-900">
                                {{ $citasConfirmadas }}
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-gray-400">
                        Consultas confirmadas
                    </p>
                </article>

                {{-- Canceladas --}}
                <article class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="absolute inset-x-0 top-0 h-1 bg-red-500"></div>

                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Canceladas
                            </p>

                            <p class="mt-3 text-3xl font-bold text-gray-900">
                                {{ $citasCanceladas }}
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-gray-400">
                        Cancelaciones registradas hoy
                    </p>
                </article>
            </section>

            {{-- Agenda y calendario --}}
            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_390px]">

                {{-- Agenda de la fecha seleccionada --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 flex-col items-center justify-center rounded-xl bg-[#0D3B7F]/10 text-[#0D3B7F]">
                                    <span class="text-xs font-bold uppercase">
                                        {{ $fechaSeleccionada->locale('es')->translatedFormat('M') }}
                                    </span>

                                    <span class="text-lg font-bold leading-none">
                                        {{ $fechaSeleccionada->format('d') }}
                                    </span>
                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">
                                        @if ($fechaSeleccionada->isToday())
                                            Agenda de hoy
                                        @else
                                            Agenda del día
                                        @endif
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $fechaSeleccionada->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ $citasSeleccionadas->count() }}
                                {{ $citasSeleccionadas->count() === 1 ? 'cita' : 'citas' }}
                            </span>

                            <a
                                href="{{ route('citas.index') }}"
                                class="text-sm font-semibold text-[#0D3B7F] transition hover:text-[#082a5d]"
                            >
                                Ver todas
                            </a>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($citasSeleccionadas as $cita)
                            @php
                                $estadoActual = $cita->estado_actual;

                                [$estadoClases, $puntoClases, $estadoTexto] = match ($estadoActual) {
                                    'confirmada' => [
                                        'bg-emerald-50 text-emerald-700',
                                        'bg-emerald-500',
                                        'Confirmada',
                                    ],
                                    'en_espera' => [
                                        'bg-amber-50 text-amber-700',
                                        'bg-amber-500',
                                        'En espera',
                                    ],
                                    'en_curso', 'en_consulta' => [
                                        'bg-blue-50 text-blue-700',
                                        'bg-blue-500',
                                        'En consulta',
                                    ],
                                    'finalizada' => [
                                        'bg-gray-100 text-gray-600',
                                        'bg-gray-500',
                                        'Finalizada',
                                    ],
                                    'cancelada' => [
                                        'bg-red-50 text-red-700',
                                        'bg-red-500',
                                        'Cancelada',
                                    ],
                                    default => [
                                        'bg-violet-50 text-violet-700',
                                        'bg-violet-500',
                                        'Programada',
                                    ],
                                };

                                $nombrePaciente = trim(
                                    ($cita->paciente?->nombre ?? '') . ' ' .
                                    ($cita->paciente?->apellido ?? '')
                                );

                                $nombreMedico = trim(
                                    ($cita->medico?->nombre ?? '') . ' ' .
                                    ($cita->medico?->apellido_paterno ?? '')
                                );

                                $inicialPaciente = mb_strtoupper(
                                    mb_substr(
                                        $cita->paciente?->nombre ?? 'P',
                                        0,
                                        1
                                    )
                                );
                            @endphp

                            <article class="group px-6 py-5 transition hover:bg-slate-50">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

                                    {{-- Horario --}}
                                    <div class="flex w-24 shrink-0 items-center gap-3 sm:block">
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($cita->hora)->format('h:i') }}
                                        </p>

                                        <p class="text-xs font-semibold uppercase text-gray-400">
                                            {{ \Carbon\Carbon::parse($cita->hora)->format('A') }}
                                        </p>
                                    </div>

                                    {{-- Paciente --}}
                                    <div class="flex min-w-0 flex-1 items-center gap-3">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                                            {{ $inicialPaciente }}
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="truncate font-semibold text-gray-900">
                                                    {{ $nombrePaciente ?: 'Paciente no disponible' }}
                                                </h4>

                                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $estadoClases }}">
                                                    <span class="h-1.5 w-1.5 rounded-full {{ $puntoClases }}"></span>
                                                    {{ $estadoTexto }}
                                                </span>
                                            </div>

                                            <p class="mt-1 truncate text-sm text-gray-500">
                                                {{ $cita->motivo ?: 'Sin motivo registrado' }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-400">
                                                Dr. {{ $nombreMedico ?: 'No asignado' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Modalidad --}}
                                    <div class="shrink-0">
                                        @if ($cita->modalidad === 'videoconsulta')
                                            <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700">
                                                <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                                Videoconsulta
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                Presencial
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Acción --}}
                                    <a
                                        href="{{ route('citas.show', $cita) }}"
                                        class="inline-flex shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-[#0D3B7F] hover:text-[#0D3B7F]"
                                    >
                                        Ver
                                    </a>
                                </div>
                            </article>
                        @empty
                            <div class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <svg
                                        class="h-7 w-7"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M8 7V3m8 4V3M5 11h14M5
                                               5h14a2 2 0 012 2v12a2 2
                                               0 01-2 2H5a2 2 0
                                               01-2-2V7a2 2 0 012-2z"
                                        />
                                    </svg>
                                </div>

                                <h4 class="mt-4 font-semibold text-gray-900">
                                    No hay citas para esta fecha
                                </h4>

                                <p class="mt-1 text-sm text-gray-500">
                                    Selecciona otro día o registra una nueva cita.
                                </p>

                                <a
                                    href="{{ route('citas.create') }}"
                                    class="mt-5 inline-flex rounded-xl bg-[#0D3B7F] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#082a5d]"
                                >
                                    Registrar cita
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Calendario funcional --}}
                <aside class="h-fit rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">
                                Seleccionar fecha
                            </p>

                            <h3 class="mt-1 text-lg font-bold capitalize text-gray-900">
                                {{ $mesCalendario->locale('es')->translatedFormat('F Y') }}
                            </h3>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Mes anterior --}}
                            <a
                                href="{{ route('dashboard', [
                                    'mes' => $mesAnterior->format('Y-m'),
                                    'fecha' => $mesAnterior->format('Y-m-d'),
                                ]) }}"
                                title="Mes anterior"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>
                            </a>

                            {{-- Mes siguiente --}}
                            <a
                                href="{{ route('dashboard', [
                                    'mes' => $mesSiguiente->format('Y-m'),
                                    'fecha' => $mesSiguiente->format('Y-m-d'),
                                ]) }}"
                                title="Mes siguiente"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Regresar a hoy --}}
                    @if (!$fechaSeleccionada->isToday())
                        <a
                            href="{{ route('dashboard') }}"
                            class="mt-4 inline-flex text-sm font-semibold text-[#0D3B7F] hover:text-[#082a5d]"
                        >
                            Regresar a hoy
                        </a>
                    @endif

                    {{-- Calendario --}}
                    <div class="mt-5 grid grid-cols-7 gap-1 text-center">
                        @foreach (['L', 'M', 'M', 'J', 'V', 'S', 'D'] as $nombreDia)
                            <div class="py-2 text-xs font-semibold text-gray-400">
                                {{ $nombreDia }}
                            </div>
                        @endforeach

                        @foreach ($diasCalendario as $dia)
                            @php
                                $fechaDia = $dia->format('Y-m-d');
                                $informacionDia = $citasPorDia->get($fechaDia);
                                $totalDia = $informacionDia['total'] ?? 0;

                                $esMesActual = $dia->month === $mesCalendario->month
                                    && $dia->year === $mesCalendario->year;

                                $esSeleccionado = $dia->isSameDay($fechaSeleccionada);
                                $esHoy = $dia->isToday();
                            @endphp

                            <a
                                href="{{ route('dashboard', [
                                    'fecha' => $fechaDia,
                                    'mes' => $dia->format('Y-m'),
                                ]) }}"
                                title="{{ $totalDia }} {{ $totalDia === 1 ? 'cita' : 'citas' }}"
                                @class([
                                    'relative flex aspect-square flex-col items-center justify-center rounded-xl text-sm font-semibold transition',

                                    'bg-[#0D3B7F] text-white shadow-sm' => $esSeleccionado,

                                    'bg-blue-50 text-[#0D3B7F] ring-1 ring-inset ring-blue-200'
                                        => $esHoy && !$esSeleccionado,

                                    'text-gray-700 hover:bg-gray-100'
                                        => $esMesActual && !$esSeleccionado && !$esHoy,

                                    'text-gray-300 hover:bg-gray-50'
                                        => !$esMesActual && !$esSeleccionado,
                                ])
                            >
                                <span>
                                    {{ $dia->day }}
                                </span>

                                @if ($totalDia > 0)
                                    <span
                                        @class([
                                            'absolute bottom-1 h-1.5 w-1.5 rounded-full',
                                            'bg-white' => $esSeleccionado,
                                            'bg-emerald-500' => !$esSeleccionado,
                                        ])
                                    ></span>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    {{-- Leyenda --}}
                    <div class="mt-5 flex flex-wrap items-center gap-4 border-t border-gray-100 pt-4 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Día con citas
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-[#0D3B7F]"></span>
                            Seleccionado
                        </span>
                    </div>

                    {{-- Próxima cita --}}
                    <div class="mt-5 rounded-xl bg-slate-50 p-4">
                        @if ($proximaCita)
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                @if ($fechaSeleccionada->isToday())
                                    Próxima cita
                                @else
                                    Primera cita activa
                                @endif
                            </p>

                            <div class="mt-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($proximaCita->hora)->format('h:i A') }}
                                    </p>

                                    <p class="mt-1 truncate text-sm text-gray-500">
                                        {{ trim(
                                            ($proximaCita->paciente?->nombre ?? '') . ' ' .
                                            ($proximaCita->paciente?->apellido ?? '')
                                        ) ?: 'Paciente no disponible' }}
                                    </p>
                                </div>

                                <a
                                    href="{{ route('citas.show', $proximaCita) }}"
                                    class="shrink-0 rounded-lg bg-white px-3 py-2 text-xs font-semibold text-[#0D3B7F] shadow-sm transition hover:bg-[#0D3B7F] hover:text-white"
                                >
                                    Ver
                                </a>
                            </div>
                        @else
                            <p class="text-sm font-semibold text-gray-900">
                                Sin citas activas
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                No hay consultas pendientes para esta fecha.
                            </p>
                        @endif
                    </div>
                </aside>
            </section>

            {{-- Acciones rápidas --}}
            <section>
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900">
                        Acciones rápidas
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Accesos frecuentes del área de recepción
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <a
                        href="{{ route('pacientes.create') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md"
                    >
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M18 9v6m3-3h-6M13 7a4
                                       4 0 11-8 0 4 4 0 018
                                       0zM3 21a6 6 0 0112 0"
                                />
                            </svg>
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900">
                                Registrar paciente
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Crear un nuevo expediente
                            </p>
                        </div>
                    </a>

                    <a
                        href="{{ route('citas.create') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md"
                    >
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900">
                                Programar cita
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Agendar una nueva consulta
                            </p>
                        </div>
                    </a>

                    <a
                        href="{{ route('pacientes.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md"
                    >
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 6h13M8 12h13M8
                                       18h13M3 6h.01M3
                                       12h.01M3 18h.01"
                                />
                            </svg>
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900">
                                Buscar paciente
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Consultar pacientes registrados
                            </p>
                        </div>
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>