<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-emerald-600">
                    Panel de recepción
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Buenos días, {{ auth()->user()->name }} 👋
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
            </div>

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
                                    18
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
                                    4
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
                                    12
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
                                    2
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
                            href="#"
                            class="text-sm font-semibold text-emerald-600 transition hover:text-emerald-700"
                        >
                            Ver todas
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100">

                        {{-- Cita 1 --}}
                        <article class="group flex flex-col gap-4 px-6 py-5 transition hover:bg-gray-50 sm:flex-row sm:items-center">
                            <div class="w-20 shrink-0">
                                <p class="text-base font-bold text-gray-900">
                                    09:00
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    45 min
                                </p>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-gray-900">
                                        Juan Pérez Martínez
                                    </h4>

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Confirmada
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-gray-500">
                                    Consulta inicial · Dr. Carlos Martínez
                                </p>
                            </div>

                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-gray-300 hover:bg-white hover:text-gray-900"
                            >
                                Ver detalles
                            </button>
                        </article>

                        {{-- Cita 2 --}}
                        <article class="group flex flex-col gap-4 px-6 py-5 transition hover:bg-gray-50 sm:flex-row sm:items-center">
                            <div class="w-20 shrink-0">
                                <p class="text-base font-bold text-gray-900">
                                    10:00
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    30 min
                                </p>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-gray-900">
                                        María López Hernández
                                    </h4>

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        En espera
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-gray-500">
                                    Seguimiento · Dra. Ana García
                                </p>
                            </div>

                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-gray-300 hover:bg-white hover:text-gray-900"
                            >
                                Ver detalles
                            </button>
                        </article>

                        {{-- Cita 3 --}}
                        <article class="group flex flex-col gap-4 px-6 py-5 transition hover:bg-gray-50 sm:flex-row sm:items-center">
                            <div class="w-20 shrink-0">
                                <p class="text-base font-bold text-gray-900">
                                    11:30
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    60 min
                                </p>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-gray-900">
                                        Carlos Ruiz Sánchez
                                    </h4>

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                        En consulta
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-gray-500">
                                    Primera valoración · Dr. Luis Ramírez
                                </p>
                            </div>

                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-gray-300 hover:bg-white hover:text-gray-900"
                            >
                                Ver detalles
                            </button>
                        </article>

                        {{-- Cita 4 --}}
                        <article class="group flex flex-col gap-4 px-6 py-5 transition hover:bg-gray-50 sm:flex-row sm:items-center">
                            <div class="w-20 shrink-0">
                                <p class="text-base font-bold text-gray-900">
                                    13:00
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    30 min
                                </p>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-gray-900">
                                        Fernanda Torres Díaz
                                    </h4>

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-500"></span>
                                        Finalizada
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-gray-500">
                                    Aplicación de tratamiento · Dra. Ana García
                                </p>
                            </div>

                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-gray-300 hover:bg-white hover:text-gray-900"
                            >
                                Ver detalles
                            </button>
                        </article>

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
                                Julio 2026
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-900"
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
                            </button>

                            <button
                                type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-900"
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
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-7 gap-1 text-center">
                        @foreach (['L', 'M', 'M', 'J', 'V', 'S', 'D'] as $day)
                            <div class="py-2 text-xs font-semibold text-gray-400">
                                {{ $day }}
                            </div>
                        @endforeach

                        @foreach ([29, 30] as $day)
                            <button
                                type="button"
                                class="aspect-square rounded-lg text-sm text-gray-300"
                            >
                                {{ $day }}
                            </button>
                        @endforeach

                        @foreach (range(1, 31) as $day)
                            <button
                                type="button"
                                @class([
                                    'aspect-square rounded-lg text-sm font-medium transition',
                                    'bg-gray-900 text-white shadow-sm' => $day === 23,
                                    'text-gray-700 hover:bg-gray-100' => $day !== 23,
                                ])
                            >
                                {{ $day }}
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-6 border-t border-gray-100 pt-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    Próxima cita
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    09:00 · Juan Pérez
                                </p>
                            </div>

                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                Confirmada
                            </span>
                        </div>
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
                                    d="M18 9v6m3-3h-6M13 7a4 4 0 11-8 0 4 4 0 018 0zM3 21a6 6 0 0112 0"
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
    class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md">
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
                    </button>

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
                                    d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"
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