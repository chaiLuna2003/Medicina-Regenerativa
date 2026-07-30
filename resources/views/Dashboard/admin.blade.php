<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Panel administrativo
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Resumen general de Medicina Regenerativa
                </p>
            </div>

            <p class="text-sm text-slate-500">
                {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
            </p>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- Bienvenida --}}
            <section class="relative overflow-hidden rounded-2xl bg-slate-900 px-6 py-8 text-white shadow-sm sm:px-8">
                <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl"></div>

                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-blue-100">
                            Administrador
                        </span>

                        <h1 class="mt-4 text-2xl font-bold sm:text-3xl">
                            Bienvenido, {{ explode(' ', auth()->user()->name)[0] }}
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">
                            Consulta el estado general del sistema y administra pacientes,
                            médicos, citas y usuarios desde un solo lugar.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a
                            href="{{ route('pacientes.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500"
                        >
                            Nuevo paciente
                        </a>

                        <a
                            href="{{ route('citas.create') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20"
                        >
                            Nueva cita
                        </a>
                    </div>
                </div>
            </section>

            {{-- Estadísticas --}}
            <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Pacientes --}}
                <a
                    href="{{ route('pacientes.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"
                >
                    <div class="flex items-start justify-between">
                        <div class="rounded-xl bg-blue-50 p-3 text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M18 18.72a9.094 9.094 0 003.741-.479
                                      3 3 0 00-4.682-2.72m.94 3.198v.001
                                      c0 .504-.123.978-.34 1.395A11.95
                                      11.95 0 0112 21c-2.036 0-3.954-.51-5.63-1.41
                                      A2.99 2.99 0 016 18.72m12 0a5.971
                                      5.971 0 00-.941-3.197m0 0A5.995
                                      5.995 0 0012 12.75a5.995 5.995 0
                                      00-5.058 2.772m0 0a3 3 0
                                      00-4.681 2.72 8.986 8.986 0
                                      003.74.477m.94-3.197A5.971
                                      5.971 0 006 18.719M15 6.75a3 3 0
                                      11-6 0 3 3 0 016 0zm6 3a2.25 2.25
                                      0 11-4.5 0 2.25 2.25 0
                                      014.5 0zm-13.5 0a2.25 2.25 0
                                      11-4.5 0 2.25 2.25 0 014.5 0z"/>
                            </svg>
                        </div>

                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                            +{{ $pacientesNuevos }} esta semana
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-bold text-slate-900">
                        {{ number_format($totalPacientes) }}
                    </p>

                    <div class="mt-1 flex items-center justify-between">
                        <p class="text-sm text-slate-500">Pacientes registrados</p>
                        <span class="text-sm text-blue-600 transition group-hover:translate-x-1">
                            Ver →
                        </span>
                    </div>
                </a>

                {{-- Médicos --}}
                <a
                    href="{{ route('medicos.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-violet-200 hover:shadow-md"
                >
                    <div class="flex items-start justify-between">
                        <div class="rounded-xl bg-violet-50 p-3 text-violet-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>

                        <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-medium text-violet-700">
                            {{ $medicosActivos }} activos
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-bold text-slate-900">
                        {{ number_format($totalMedicos) }}
                    </p>

                    <div class="mt-1 flex items-center justify-between">
                        <p class="text-sm text-slate-500">Médicos registrados</p>
                        <span class="text-sm text-violet-600 transition group-hover:translate-x-1">
                            Ver →
                        </span>
                    </div>
                </a>

                {{-- Citas --}}
                <a
                    href="{{ route('citas.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-md"
                >
                    <div class="flex items-start justify-between">
                        <div class="rounded-xl bg-amber-50 p-3 text-amber-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6.75 3v2.25M17.25 3v2.25M3.75
                                      18.75V7.5A2.25 2.25 0 016
                                      5.25h12a2.25 2.25 0 012.25
                                      2.25v11.25M3.75 18.75A2.25
                                      2.25 0 006 21h12a2.25 2.25 0
                                      002.25-2.25M3.75 18.75v-7.5A2.25
                                      2.25 0 016 9h12a2.25 2.25 0
                                      012.25 2.25v7.5"/>
                            </svg>
                        </div>

                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                            {{ $citasConfirmadasHoy }} confirmadas
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-bold text-slate-900">
                        {{ number_format($totalCitasHoy) }}
                    </p>

                    <div class="mt-1 flex items-center justify-between">
                        <p class="text-sm text-slate-500">Citas programadas hoy</p>
                        <span class="text-sm text-amber-600 transition group-hover:translate-x-1">
                            Ver →
                        </span>
                    </div>
                </a>

                {{-- Usuarios --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.75 6a3.75 3.75 0 11-7.5
                                      0 3.75 3.75 0 017.5 0zM4.5
                                      20.118a7.5 7.5 0 0115 0A17.933
                                      17.933 0 0112 21.75c-2.676
                                      0-5.216-.584-7.5-1.632z"/>
                            </svg>
                        </div>

                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                            Próximo módulo
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-bold text-slate-900">
                        {{ number_format($totalUsuarios) }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Usuarios del sistema
                    </p>
                </div>
            </section>

            {{-- Contenido principal --}}
            <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                {{-- Accesos a módulos --}}
                <div class="xl:col-span-2">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-slate-900">
                            Módulos administrativos
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Selecciona el área que deseas administrar.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <a href="{{ route('pacientes.index') }}"
                           class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
                            <div class="rounded-xl bg-blue-50 p-3 text-blue-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M18 18.72a9.094 9.094 0 003.741-.479
                                          3 3 0 00-4.682-2.72M15 6.75a3
                                          3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-slate-900">
                                    Pacientes
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    Consultar, registrar y editar pacientes.
                                </p>
                            </div>

                            <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">
                                →
                            </span>
                        </a>

                        <a href="{{ route('medicos.index') }}"
                           class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-violet-300 hover:shadow-sm">
                            <div class="rounded-xl bg-violet-50 p-3 text-violet-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-slate-900">
                                    Médicos
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    Administrar personal médico y especialidades.
                                </p>
                            </div>

                            <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-violet-600">
                                →
                            </span>
                        </a>

                        <a href="{{ route('citas.index') }}"
                           class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-amber-300 hover:shadow-sm">
                            <div class="rounded-xl bg-amber-50 p-3 text-amber-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M6.75 3v2.25M17.25 3v2.25M3.75
                                          9h16.5M5.25 5.25h13.5a1.5
                                          1.5 0 011.5 1.5v12a1.5 1.5
                                          0 01-1.5 1.5H5.25a1.5 1.5
                                          0 01-1.5-1.5v-12a1.5 1.5
                                          0 011.5-1.5z"/>
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-slate-900">
                                    Citas
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    Consultar y gestionar la agenda médica.
                                </p>
                            </div>

                            <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600">
                                →
                            </span>
                        </a>

                        <div class="flex items-center gap-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5">
                            <div class="rounded-xl bg-slate-200 p-3 text-slate-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.75 6a3.75 3.75 0 11-7.5
                                          0 3.75 3.75 0 017.5 0z"/>
                                </svg>
                            </div>

                            <div>
                                <h3 class="font-semibold text-slate-700">
                                    Usuarios y roles
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    Próximamente construiremos este módulo.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Estado del día --}}
                <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">
                        Estado de hoy
                    </h2>

                    <div class="mt-6 space-y-5">
                        <div>
                            <div class="mb-2 flex items-center justify-between text-sm">
                                <span class="text-slate-600">Citas confirmadas</span>
                                <span class="font-semibold text-emerald-600">
                                    {{ $citasConfirmadasHoy }}
                                </span>
                            </div>

                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-full rounded-full bg-emerald-500"
                                    style="width: {{ $totalCitasHoy > 0
                                        ? ($citasConfirmadasHoy / $totalCitasHoy) * 100
                                        : 0 }}%"
                                ></div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between text-sm">
                                <span class="text-slate-600">En espera</span>
                                <span class="font-semibold text-amber-600">
                                    {{ $citasPendientesHoy }}
                                </span>
                            </div>

                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-full rounded-full bg-amber-500"
                                    style="width: {{ $totalCitasHoy > 0
                                        ? ($citasPendientesHoy / $totalCitasHoy) * 100
                                        : 0 }}%"
                                ></div>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-5">
                            <p class="text-sm text-slate-500">
                                Total de citas programadas
                            </p>
                            <p class="mt-1 text-2xl font-bold text-slate-900">
                                {{ $totalCitasHoy }}
                            </p>
                        </div>
                    </div>
                </aside>
            </section>

            {{-- Registros recientes --}}
            <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

                {{-- Últimos pacientes --}}
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                        <div>
                            <h2 class="font-semibold text-slate-900">
                                Pacientes recientes
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Últimos registros del sistema
                            </p>
                        </div>

                        <a href="{{ route('pacientes.index') }}"
                           class="text-sm font-medium text-blue-600 hover:text-blue-700">
                            Ver todos
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($ultimosPacientes as $paciente)
                            <div class="flex items-center gap-3 px-6 py-4">
                                <img
                                    src="{{ $paciente->fotoUrl() }}"
                                    alt="{{ $paciente->nombre }}"
                                    class="h-10 w-10 rounded-full border border-slate-200 object-cover"
                                >

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-800">
                                        {{ $paciente->nombre }}
                                        {{ $paciente->apellido }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $paciente->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <a
                                    href="{{ route('pacientes.show', $paciente) }}"
                                    class="text-sm text-blue-600 hover:text-blue-700"
                                >
                                    Ver
                                </a>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center text-sm text-slate-500">
                                Todavía no hay pacientes registrados.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Próximas citas --}}
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                        <div>
                            <h2 class="font-semibold text-slate-900">
                                Próximas citas
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Agenda más cercana
                            </p>
                        </div>

                        <a href="{{ route('citas.index') }}"
                           class="text-sm font-medium text-blue-600 hover:text-blue-700">
                            Ver agenda
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($proximasCitas as $cita)
                            <div class="flex items-center gap-4 px-6 py-4">
                                <div class="min-w-[64px] rounded-xl bg-slate-100 px-3 py-2 text-center">
                                    <p class="text-xs font-medium uppercase text-slate-500">
                                        {{ $cita->fecha->translatedFormat('d M') }}
                                    </p>
                                    <p class="mt-0.5 text-sm font-bold text-slate-800">
                                        {{ Carbon\Carbon::parse($cita->hora)->format('H:i') }}
                                    </p>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-800">
                                        {{ $cita->paciente?->nombre }}
                                        {{ $cita->paciente?->apellido }}
                                    </p>

                                    <p class="truncate text-xs text-slate-500">
                                        Dr. {{ $cita->medico?->nombre }}
                                        {{ $cita->medico?->apellido_paterno }}
                                    </p>
                                </div>

                                <span @class([
                                    'rounded-full px-2.5 py-1 text-xs font-medium',
                                    'bg-emerald-50 text-emerald-700' => $cita->estado === 'confirmada',
                                    'bg-amber-50 text-amber-700' => $cita->estado === 'en_espera',
                                    'bg-slate-100 text-slate-600' => !in_array($cita->estado, ['confirmada', 'en_espera']),
                                ])>
                                    {{ ucfirst(str_replace('_', ' ', $cita->estado)) }}
                                </span>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center text-sm text-slate-500">
                                No hay próximas citas programadas.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>