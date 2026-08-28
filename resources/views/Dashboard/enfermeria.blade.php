<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-emerald-600">
                    Panel de enfermería
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Buenos días, {{ auth()->user()->name }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
            </div>

<div class="flex flex-col gap-3 sm:flex-row">

    <x-hoja-diaria-button class="w-full sm:w-auto" />

    <a
        href="{{ route('signos-vitales.index') }}"
        class="inline-flex w-full items-center justify-center
               gap-2 rounded-xl border border-gray-300
               bg-white px-5 py-3 text-sm font-semibold
               text-gray-700 shadow-sm transition
               hover:border-gray-400 hover:bg-gray-50
               focus:outline-none focus:ring-2
               focus:ring-gray-900 focus:ring-offset-2
               sm:w-auto">

        <svg
            class="h-5 w-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7
                   a2 2 0 01-2-2V5a2 2 0 012-2
                   h5.586a1 1 0 01.707.293
                   l3.414 3.414A1 1 0 0117 7.414V19
                   a2 2 0 01-2 2z" />
        </svg>

        Historial de signos
    </a>
</div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- Mensajes del sistema --}}
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm font-medium text-blue-700">
                    {{ session('info') }}
                </div>
            @endif

            {{-- Indicadores --}}
            <section>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    {{-- Citas activas --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Citas activas
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $citasHoy->count() }}
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
                                        d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"
                                    />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Citas no canceladas para hoy
                        </p>
                    </article>

                    {{-- Pendientes --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Pendientes
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $citasPendientes }}
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
                                        d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Citas sin signos vitales
                        </p>
                    </article>

                    {{-- Valoraciones realizadas --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Valoraciones realizadas
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $valoracionesRealizadas }}
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
                            Signos vitales registrados hoy
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
                            Citas canceladas durante el día
                        </p>
                    </article>

                </div>
            </section>

            {{-- Próxima valoración --}}
            <section>
                @if ($proximaCita)
                    <article class="relative overflow-hidden rounded-2xl bg-gray-900 p-6 text-white shadow-sm">
                        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-emerald-400/10"></div>
                        <div class="absolute -bottom-16 right-24 h-40 w-40 rounded-full bg-blue-400/10"></div>

                        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/10 text-emerald-300">
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
                                            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-emerald-300">
                                        Próxima valoración
                                    </p>

                                    <h3 class="mt-1 text-xl font-bold">
                                        {{ trim(
                                            ($proximaCita->paciente?->nombre ?? '') . ' ' .
                                            ($proximaCita->paciente?->apellido_paterno ?? $proximaCita->paciente?->apellido ?? '')
                                        ) ?: 'Paciente no disponible' }}
                                    </h3>

                                    <p class="mt-2 text-sm text-gray-300">
                                        {{ \Carbon\Carbon::parse($proximaCita->hora)->format('H:i') }}
                                        · Dr. {{ trim(
                                            ($proximaCita->medico?->nombre ?? '') . ' ' .
                                            ($proximaCita->medico?->apellido_paterno ?? '')
                                        ) ?: 'No asignado' }}
                                    </p>

                                    @if ($proximaCita->motivo)
                                        <p class="mt-1 text-sm text-gray-400">
                                            {{ $proximaCita->motivo }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <a
                                href="{{ route('signos-vitales.create', $proximaCita) }}"
                                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-gray-900"
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

                                Registrar signos vitales
                            </a>
                        </div>
                    </article>
                @else
                    <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
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
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h3 class="font-bold text-emerald-900">
                                    No hay valoraciones próximas
                                </h3>

                                <p class="mt-1 text-sm text-emerald-700">
                                    No quedan citas futuras pendientes de signos vitales para hoy.
                                </p>
                            </div>
                        </div>
                    </article>
                @endif
            </section>

            {{-- Citas del día --}}
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-gray-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            Pacientes del día
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Seguimiento de signos vitales por cita
                        </p>
                    </div>

                    <a
                        href="{{ route('signos-vitales.index') }}"
                        class="text-sm font-semibold text-emerald-600 transition hover:text-emerald-700"
                    >
                        Consultar historial
                    </a>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($citasHoy as $cita)
                        @php
                            [$estadoClases, $puntoClases, $estadoTexto] = match ($cita->estado) {
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
                                'en_consulta' => [
                                    'bg-blue-50 text-blue-700',
                                    'bg-blue-500',
                                    'En consulta',
                                ],
                                'finalizada' => [
                                    'bg-gray-100 text-gray-600',
                                    'bg-gray-500',
                                    'Finalizada',
                                ],
                                default => [
                                    'bg-violet-50 text-violet-700',
                                    'bg-violet-500',
                                    'Programada',
                                ],
                            };

                            $nombrePaciente = trim(
                                ($cita->paciente?->nombre ?? '') . ' ' .
                                ($cita->paciente?->apellido_paterno ?? $cita->paciente?->apellido ?? '') . ' ' .
                                ($cita->paciente?->apellido_materno ?? '')
                            );

                            $nombreMedico = trim(
                                ($cita->medico?->nombre ?? '') . ' ' .
                                ($cita->medico?->apellido_paterno ?? '')
                            );
                        @endphp

                        <article class="group flex flex-col gap-4 px-6 py-5 transition hover:bg-gray-50 lg:flex-row lg:items-center">
                            <div class="flex items-center gap-4 lg:w-24 lg:shrink-0">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-sm font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($cita->hora)->format('H:i') }}
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-gray-900">
                                        {{ $nombrePaciente ?: 'Paciente no disponible' }}
                                    </h4>

                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $estadoClases }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $puntoClases }}"></span>
                                        {{ $estadoTexto }}
                                    </span>

                                    @if ($cita->signoVital)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            <svg
                                                class="h-3.5 w-3.5"
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

                                            Signos registrados
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Pendiente de valoración
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-1 text-sm text-gray-500">
                                    Dr. {{ $nombreMedico ?: 'No asignado' }}
                                </p>

                                @if ($cita->motivo)
                                    <p class="mt-1 truncate text-sm text-gray-400">
                                        {{ $cita->motivo }}
                                    </p>
                                @endif
                            </div>

                            <div class="shrink-0">
                                @if ($cita->signoVital)
                                    <a
                                        href="{{ route('signos-vitales.show', $cita->signoVital) }}"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100 lg:w-auto"
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
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />
                                        </svg>

                                        Ver signos vitales
                                    </a>
                                @else
                                    <a
                                        href="{{ route('signos-vitales.create', $cita) }}"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 lg:w-auto"
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
                                                d="M12 4v16m8-8H4"
                                            />
                                        </svg>

                                        Registrar signos
                                    </a>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
                                <svg
                                    class="h-7 w-7"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"
                                    />
                                </svg>
                            </div>

                            <p class="mt-4 font-semibold text-gray-900">
                                No hay citas activas para hoy
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Las citas asignadas aparecerán en este espacio.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>