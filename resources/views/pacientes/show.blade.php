<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('pacientes.index') }}"
                    class="text-gray-400 transition hover:text-gray-600"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </a>

                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ $pacientes->nombre }}
                        {{ $pacientes->apellido }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Expediente administrativo del paciente
                    </p>
                </div>
            </div>

            @if (
                request()->user()->isAdmin()
                || request()->user()->isRecepcionista()
            )
                <a
                    href="{{ route('pacientes.edit', $pacientes) }}"
                    class="rounded-lg bg-amber-50 px-4 py-2
                           text-sm font-semibold text-amber-700
                           transition hover:bg-amber-100"
                >
                    Editar paciente
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            {{-- Perfil principal --}}
            <section class="overflow-hidden rounded-2xl bg-white shadow-sm">
                <div class="p-6 sm:p-8">
                    <div
                        class="flex flex-col gap-6
                               lg:flex-row lg:items-start
                               lg:justify-between"
                    >
                        <div class="flex items-center gap-5">
                            <img
                                src="{{ $pacientes->fotoUrl() }}"
                                alt="Foto de {{ $pacientes->nombre }}"
                                class="h-24 w-24 rounded-2xl
                                       border border-gray-200
                                       object-cover shadow-sm"
                            >

                            <div>
                                <h1 class="text-2xl font-bold text-slate-900">
                                    {{ $pacientes->nombre }}
                                    {{ $pacientes->apellido }}
                                </h1>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $pacientes->edad ?? 'Edad no disponible' }}
                                </p>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span
                                        class="rounded-full bg-blue-50
                                               px-3 py-1 text-xs
                                               font-semibold text-blue-700"
                                    >
                                        Paciente #{{ $pacientes->id }}
                                    </span>

                                    <span
                                        class="rounded-full
                                               {{ $pacientes->status
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : 'bg-red-50 text-red-700' }}
                                               px-3 py-1 text-xs font-semibold"
                                    >
                                        {{ $pacientes->status
                                            ? 'Activo'
                                            : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 lg:w-[440px]">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase
                                          tracking-wide text-slate-400">
                                    Teléfono
                                </p>

                                <p class="mt-1 text-sm font-semibold
                                          text-slate-800">
                                    {{ $pacientes->telefono ?? 'No registrado' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase
                                          tracking-wide text-slate-400">
                                    Correo
                                </p>

                                <p class="mt-1 break-all text-sm
                                          font-semibold text-slate-800">
                                    {{ $pacientes->email ?? 'No registrado' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-slate-100 pt-6">
                        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <dt class="text-xs font-semibold uppercase
                                          tracking-wide text-slate-400">
                                    Fecha de nacimiento
                                </dt>

                                <dd class="mt-1 text-sm font-semibold
                                          text-slate-800">
                                    {{ $pacientes->fecha_nacimiento?->format('d/m/Y')
                                        ?? 'No registrada' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase
                                          tracking-wide text-slate-400">
                                    Edad
                                </dt>

                                <dd class="mt-1 text-sm font-semibold
                                          text-slate-800">
                                    {{ $pacientes->edad ?? 'No disponible' }}
                                </dd>
                            </div>

                            <div class="sm:col-span-2">
                                <dt class="text-xs font-semibold uppercase
                                          tracking-wide text-slate-400">
                                    Notas
                                </dt>

                                <dd class="mt-1 whitespace-pre-line
                                          text-sm text-slate-700">
                                    {{ $pacientes->notas ?? 'Sin notas registradas.' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>

            {{-- Resumen --}}
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">
                        Citas
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ $pacientes->citas->count() }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Historial total registrado
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">
                        Estudios
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ $pacientes->estudios->count() }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Archivos clínicos asociados
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">
                        Recetas
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ $pacientes->recetas->count() }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Recetas médicas emitidas
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">
                        Signos vitales
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ $pacientes->signosVitales->count() }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Registros disponibles
                    </p>
                </div>
            </section>

            {{-- Historial de citas --}}
            <section class="overflow-hidden rounded-2xl bg-white shadow-sm">
                <div class="flex items-center justify-between
                            border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">
                            Historial de citas
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Consultas registradas para este paciente
                        </p>
                    </div>
                </div>

                @if ($pacientes->citas->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-slate-500">
                        Este paciente todavía no tiene citas registradas.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs
                                               font-semibold uppercase
                                               tracking-wide text-slate-500"
                                    >
                                        Fecha
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs
                                               font-semibold uppercase
                                               tracking-wide text-slate-500">
                                        Médico
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs
                                               font-semibold uppercase
                                               tracking-wide text-slate-500">
                                        Modalidad
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs
                                               font-semibold uppercase
                                               tracking-wide text-slate-500">
                                        Motivo
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs
                                               font-semibold uppercase
                                               tracking-wide text-slate-500">
                                        Estado
                                    </th>

                                    <th class="px-6 py-3 text-right text-xs
                                               font-semibold uppercase
                                               tracking-wide text-slate-500">
                                        Acción
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($pacientes->citas as $cita)
                                    @php
                                        $motivoTexto = match ($cita->motivo) {
                                            'consulta_inicial' =>
                                                'Consulta inicial',

                                            'consulta_subsecuente' =>
                                                'Consulta subsecuente',

                                            'consulta_emergencia' =>
                                                'Consulta de emergencia',

                                            default =>
                                                $cita->motivo
                                                    ? ucfirst(
                                                        str_replace(
                                                            '_',
                                                            ' ',
                                                            $cita->motivo
                                                        )
                                                    )
                                                    : 'No especificado',
                                        };

                                        $estado = $cita->estado_actual;
                                    @endphp

                                    <tr class="hover:bg-slate-50/70">
                                        <td class="whitespace-nowrap
                                                   px-6 py-4 text-sm
                                                   text-slate-700">
                                            <div class="font-semibold">
                                                {{ $cita->fecha?->format('d/m/Y')
                                                    ?? '—' }}
                                            </div>

                                            <div class="text-xs text-slate-400">
                                                {{ $cita->hora
                                                    ? \Carbon\Carbon::parse(
                                                        $cita->hora
                                                    )->format('h:i A')
                                                    : '—' }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-sm text-slate-700">
                                            {{ $cita->medico?->user?->name
                                                ?? trim(
                                                    ($cita->medico?->nombre ?? '')
                                                    . ' '
                                                    . ($cita->medico?->apellido_paterno ?? '')
                                                )
                                                ?: 'No disponible' }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-slate-700">
                                            {{ $cita->modalidad_texto }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-slate-700">
                                            {{ $motivoTexto }}
                                        </td>

                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex rounded-full
                                                       px-2.5 py-1 text-xs
                                                       font-semibold
                                                       @class([
                                                           'bg-emerald-50 text-emerald-700'
                                                               => $estado === 'finalizada',

                                                           'bg-blue-50 text-blue-700'
                                                               => $estado === 'programada',

                                                           'bg-amber-50 text-amber-700'
                                                               => $estado === 'en_curso',

                                                           'bg-red-50 text-red-700'
                                                               => $estado === 'cancelada',
                                                       ])"
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

                                        <td class="px-6 py-4 text-right">
                                            <a
                                                href="{{ route(
                                                    'citas.show',
                                                    $cita
                                                ) }}"
                                                class="text-sm font-semibold
                                                       text-blue-600
                                                       hover:text-blue-800"
                                            >
                                                Ver cita
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- Estudios --}}
            <section class="overflow-hidden rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-lg font-semibold text-slate-900">
                        Estudios clínicos
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Archivos y estudios asociados al paciente
                    </p>
                </div>

                @if ($pacientes->estudios->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-slate-500">
                        No hay estudios clínicos registrados.
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($pacientes->estudios as $estudio)
                            <div
                                class="flex flex-col gap-4 px-6 py-5
                                       sm:flex-row sm:items-center
                                       sm:justify-between"
                            >
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        {{ $estudio->nombre }}
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $estudio->fecha_estudio?->format('d/m/Y')
                                            ?? 'Fecha no registrada' }}
                                    </p>

                                    @if ($estudio->descripcion)
                                        <p class="mt-2 text-sm text-slate-600">
                                            {{ $estudio->descripcion }}
                                        </p>
                                    @endif
                                </div>

                                <div class="text-sm text-slate-500">
                                    {{ $estudio->archivo_original
                                        ?? 'Archivo registrado' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Recetas --}}
            @if (
                request()->user()->isAdmin()
                || request()->user()->role === 'medico'
            )
                <section class="overflow-hidden rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-6 py-5">
                        <h3 class="text-lg font-semibold text-slate-900">
                            Historial de recetas
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Recetas médicas asociadas al expediente
                        </p>
                    </div>

                    @if ($pacientes->recetas->isEmpty())
                        <div
                            class="px-6 py-10 text-center
                                   text-sm text-slate-500"
                        >
                            No hay recetas registradas.
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach ($pacientes->recetas as $receta)
                                <div
                                    class="flex flex-col gap-4 px-6 py-5
                                           sm:flex-row sm:items-center
                                           sm:justify-between"
                                >
                                    <div>
                                        <p class="font-semibold text-slate-900">
                                            Receta #{{ $receta->id }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $receta->fecha_expedicion
                                                ? \Carbon\Carbon::parse(
                                                    $receta->fecha_expedicion
                                                )->format('d/m/Y')
                                                : 'Fecha no disponible' }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-600">
                                            Médico:
                                            {{ $receta->cita?->medico?->user?->name
                                                ?? 'No disponible' }}
                                        </p>
                                    </div>

                                    <a
                                        href="{{ route(
                                            'recetas.show',
                                            $receta
                                        ) }}"
                                        class="text-sm font-semibold
                                               text-blue-600
                                               hover:text-blue-800"
                                    >
                                        Ver receta
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            {{-- Signos vitales --}}
            @if (
                request()->user()->isAdmin()
                || request()->user()->role === 'medico'
                || request()->user()->role === 'enfermero'
            )
                <section class="overflow-hidden rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-6 py-5">
                        <h3 class="text-lg font-semibold text-slate-900">
                            Signos vitales
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Registros clínicos recientes
                        </p>
                    </div>

                    @if ($pacientes->signosVitales->isEmpty())
                        <div
                            class="px-6 py-10 text-center
                                   text-sm text-slate-500"
                        >
                            No hay signos vitales registrados.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs
                                                   font-semibold uppercase
                                                   tracking-wide text-slate-500">
                                            Fecha
                                        </th>

                                        <th class="px-6 py-3 text-left text-xs
                                                   font-semibold uppercase
                                                   tracking-wide text-slate-500">
                                            Peso
                                        </th>

                                        <th class="px-6 py-3 text-left text-xs
                                                   font-semibold uppercase
                                                   tracking-wide text-slate-500">
                                            Presión
                                        </th>

                                        <th class="px-6 py-3 text-left text-xs
                                                   font-semibold uppercase
                                                   tracking-wide text-slate-500">
                                            FC
                                        </th>

                                        <th class="px-6 py-3 text-left text-xs
                                                   font-semibold uppercase
                                                   tracking-wide text-slate-500">
                                            SpO₂
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach (
                                        $pacientes->signosVitales
                                        as $signo
                                    )
                                        <tr>
                                            <td class="px-6 py-4 text-sm
                                                       text-slate-700">
                                                {{ $signo->created_at?->format(
                                                    'd/m/Y H:i'
                                                ) ?? '—' }}
                                            </td>

                                            <td class="px-6 py-4 text-sm
                                                       text-slate-700">
                                                {{ $signo->peso
                                                    ? $signo->peso . ' kg'
                                                    : '—' }}
                                            </td>

                                            <td class="px-6 py-4 text-sm
                                                       text-slate-700">
                                                @if (
                                                    $signo->presion_sistolica
                                                    && $signo->presion_diastolica
                                                )
                                                    {{ $signo->presion_sistolica }}
                                                    /
                                                    {{ $signo->presion_diastolica }}
                                                @else
                                                    —
                                                @endif
                                            </td>

                                            <td class="px-6 py-4 text-sm
                                                       text-slate-700">
                                                {{ $signo->frecuencia_cardiaca
                                                    ?? '—' }}
                                            </td>

                                            <td class="px-6 py-4 text-sm
                                                       text-slate-700">
                                                {{ $signo->spo2
                                                    ? $signo->spo2 . '%'
                                                    : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @endif

        </div>
    </div>
</x-app-layout>