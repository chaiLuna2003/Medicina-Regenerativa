<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <p class="text-sm font-medium text-emerald-600">
            Panel de recepción
        </p>

        <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
            Buenos días, {{ auth()->user()->name }}
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
        </p>
    </div>

    {{-- Acciones rápidas --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <a
            href="{{ route('citas.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
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
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
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

            Nuevo paciente
        </a>
    </div>
</div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- Indicadores --}}
            <section>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    {{-- Citas de hoy --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Citas de hoy
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $totalCitasHoy }}
                                </p>
                            </div>

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Agenda programada para hoy
                        </p>
                    </article>

                    {{-- En espera --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    En espera
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $citasEnEspera }}
                                </p>
                            </div>

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Pacientes pendientes de atención
                        </p>
                    </article>

                    {{-- Confirmadas --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Confirmadas
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $citasConfirmadas }}
                                </p>
                            </div>

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Citas confirmadas por pacientes
                        </p>
                    </article>

                    {{-- Canceladas --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Canceladas
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $citasCanceladas }}
                                </p>
                            </div>

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Cancelaciones registradas hoy
                        </p>
                    </article>

                </div>
            </section>

            {{-- Agenda y calendario --}}
            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">

                {{-- Agenda del día --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                Agenda del día
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Citas programadas para hoy
                            </p>
                        </div>

                        <a
                            href="{{ route('citas.index') }}"
                            class="text-sm font-semibold text-emerald-600 transition hover:text-emerald-700">
                            Ver todas
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($citasHoy as $cita)
                        @php
                        [$estadoClases, $puntoClases, $estadoTexto] = match ($cita->estado) {
                        'confirmada' => ['bg-emerald-50 text-emerald-700', 'bg-emerald-500', 'Confirmada'],
                        'en_espera' => ['bg-amber-50 text-amber-700', 'bg-amber-500', 'En espera'],
                        'en_consulta' => ['bg-blue-50 text-blue-700', 'bg-blue-500', 'En consulta'],
                        'finalizada' => ['bg-gray-100 text-gray-600', 'bg-gray-500', 'Finalizada'],
                        'cancelada' => ['bg-red-50 text-red-700', 'bg-red-500', 'Cancelada'],
                        default => ['bg-violet-50 text-violet-700', 'bg-violet-500', 'Programada'],
                        };
                        @endphp

                        <article class="group flex flex-col gap-4 px-6 py-5 transition hover:bg-gray-50 sm:flex-row sm:items-center">
                            <div class="w-20 shrink-0">
                                <p class="text-base font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($cita->hora)->format('H:i') }}
                                </p>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-gray-900">
                                        {{ trim(
                                                ($cita->paciente?->nombre ?? '') . ' ' .
                                                ($cita->paciente?->apellido ?? '')
                                            ) ?: 'Paciente no disponible' }}
                                    </h4>

                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $estadoClases }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $puntoClases }}"></span>
                                        {{ $estadoTexto }}
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $cita->motivo }}
                                    · Dr. {{ trim(
                                            ($cita->medico?->nombre ?? '') . ' ' .
                                            ($cita->medico?->apellido_paterno ?? '')
                                        ) ?: 'No asignado' }}
                                </p>
                            </div>

                            <a
                                href="{{ route('citas.edit', $cita) }}"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-gray-300 hover:bg-white hover:text-gray-900">
                                Ver detalles
                            </a>
                        </article>
                        @empty
                        <div class="px-6 py-14 text-center">
                            <p class="font-semibold text-gray-900">
                                No hay citas programadas para hoy
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Las citas que registres para hoy aparecerán aquí.
                            </p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Calendario --}}
                <aside class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                Calendario
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ now()->locale('es')->translatedFormat('F Y') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-900">
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-900">
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-7 gap-1 text-center">
                        @foreach (['L', 'M', 'M', 'J', 'V', 'S', 'D'] as $day)
                        <div class="py-2 text-xs font-semibold text-gray-400">
                            {{ $day }}
                        </div>
                        @endforeach

                        @php
                        $inicioMes = now()->copy()->startOfMonth();
                        $diasPrevios = $inicioMes->dayOfWeekIso - 1;
                        $diasMesAnterior = $inicioMes->copy()->subMonth()->daysInMonth;
                        @endphp

                        @if ($diasPrevios > 0)
                        @foreach (range($diasMesAnterior - $diasPrevios + 1, $diasMesAnterior) as $day)
                        <span class="flex aspect-square items-center justify-center rounded-lg text-sm text-gray-300">
                            {{ $day }}
                        </span>
                        @endforeach
                        @endif

                        @foreach (range(1, now()->daysInMonth) as $day)
                        <button
                            type="button"
                            @class([ 'aspect-square rounded-lg text-sm font-medium transition' , 'bg-gray-900 text-white shadow-sm'=> $day === now()->day,
                            'text-gray-700 hover:bg-gray-100' => $day !== now()->day,
                            ])
                            >
                            {{ $day }}
                        </button>
                        @endforeach
                    </div>

                    <div class="mt-6 border-t border-gray-100 pt-5">
                        @if ($proximaCita)
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900">
                                    Próxima cita
                                </p>

                                <p class="mt-1 truncate text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($proximaCita->hora)->format('H:i') }}
                                    · {{ trim(
                                            ($proximaCita->paciente?->nombre ?? '') . ' ' .
                                            ($proximaCita->paciente?->apellido ?? '')
                                        ) ?: 'Paciente no disponible' }}
                                </p>
                            </div>

                            <a
                                href="{{ route('citas.edit', $proximaCita) }}"
                                class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                Ver cita
                            </a>
                        </div>
                        @else
                        <div>
                            <p class="text-sm font-semibold text-gray-900">
                                Sin citas próximas
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                No hay más citas pendientes para hoy.
                            </p>
                        </div>
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
                        class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M18 9v6m3-3h-6M13 7a4 4 0 11-8 0 4 4 0 018 0zM3 21a6 6 0 0112 0" />
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
                        class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4" />
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
                        class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
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