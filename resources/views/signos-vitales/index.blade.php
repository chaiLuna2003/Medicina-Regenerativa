<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Historial de signos vitales
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Consulta las valoraciones registradas de los pacientes.
                </p>
            </div>

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>

                Volver al dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm font-medium text-blue-800">
                    {{ session('info') }}
                </div>
            @endif

            {{-- Resumen --}}
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Valoraciones mostradas
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $signosVitales->count() }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        En esta página
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Total de valoraciones
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $signosVitales->total() }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Registros encontrados
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-gray-900 p-5 text-white shadow-sm sm:col-span-2 lg:col-span-1">
                    <p class="text-sm font-medium text-gray-300">
                        Página actual
                    </p>

                    <p class="mt-2 text-3xl font-bold">
                        {{ $signosVitales->currentPage() }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        De {{ $signosVitales->lastPage() }}
                    </p>
                </article>
            </section>

            {{-- Historial --}}
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="text-lg font-bold text-gray-900">
                        Valoraciones registradas
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Ordenadas desde la más reciente.
                    </p>
                </div>

                @if ($signosVitales->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <svg
                                class="h-7 w-7"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>

                        <h4 class="mt-4 text-lg font-bold text-gray-900">
                            Todavía no hay valoraciones
                        </h4>

                        <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                            Los signos vitales registrados desde las citas aparecerán
                            en este historial.
                        </p>

                        <a
                            href="{{ route('dashboard') }}"
                            class="mt-6 inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                        >
                            Revisar citas pendientes
                        </a>
                    </div>
                @else
                    {{-- Escritorio --}}
                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Paciente
                                    </th>

                                    <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Cita
                                    </th>

                                    <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Mediciones
                                    </th>

                                    <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Registrado por
                                    </th>

                                    <th class="px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Acción
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($signosVitales as $signoVital)
                                    @php
                                        $paciente = $signoVital->paciente;
                                        $cita = $signoVital->cita;
                                        $medico = $cita?->medico;

                                        $nombrePaciente = trim(
                                            ($paciente?->nombre ?? '') . ' ' .
                                            ($paciente?->apellido_paterno ?? $paciente?->apellido ?? '') . ' ' .
                                            ($paciente?->apellido_materno ?? '')
                                        );

                                        $nombreMedico = trim(
                                            ($medico?->nombre ?? '') . ' ' .
                                            ($medico?->apellido_paterno ?? '') . ' ' .
                                            ($medico?->apellido_materno ?? '')
                                        );

                                        $fechaCita = $cita?->fecha
                                            ? \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y')
                                            : 'Sin fecha';

                                        $horaCita = $cita?->hora
                                            ? \Carbon\Carbon::parse($cita->hora)->format('H:i')
                                            : 'Sin hora';
                                    @endphp

                                    <tr class="transition hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">
                                                    {{ mb_strtoupper(mb_substr($nombrePaciente ?: 'P', 0, 1)) }}
                                                </div>

                                                <div>
                                                    <p class="font-semibold text-gray-900">
                                                        {{ $nombrePaciente ?: 'Paciente no disponible' }}
                                                    </p>

                                                    <p class="mt-0.5 text-xs text-gray-500">
                                                        Valoración #{{ str_pad((string) $signoVital->id, 6, '0', STR_PAD_LEFT) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-4">
                                            <p class="font-medium text-gray-900">
                                                {{ $fechaCita }} · {{ $horaCita }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $nombreMedico ? 'Dr. ' . $nombreMedico : 'Médico no asignado' }}
                                            </p>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-2 text-xs font-semibold">
                                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-gray-700">
                                                    {{ number_format((float) $signoVital->peso, 2) }} kg
                                                </span>

                                                @if ($signoVital->temperatura !== null)
                                                    <span class="rounded-full bg-orange-50 px-2.5 py-1 text-orange-700">
                                                        {{ number_format((float) $signoVital->temperatura, 1) }} °C
                                                    </span>
                                                @endif

                                                @if (
                                                    $signoVital->presion_sistolica !== null &&
                                                    $signoVital->presion_diastolica !== null
                                                )
                                                    <span class="rounded-full bg-red-50 px-2.5 py-1 text-red-700">
                                                        {{ $signoVital->presion_sistolica }}/{{ $signoVital->presion_diastolica }}
                                                    </span>
                                                @endif

                                                @if ($signoVital->saturacion_oxigeno !== null)
                                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-blue-700">
                                                        SpO₂ {{ $signoVital->saturacion_oxigeno }}%
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-4">
                                            <p class="font-medium text-gray-900">
                                                {{ $signoVital->enfermero?->name ?? 'No disponible' }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $signoVital->created_at?->format('d/m/Y H:i') }}
                                            </p>
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-4 text-right">
                                            <a
                                                href="{{ route('signos-vitales.show', $signoVital) }}"
                                                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800"
                                            >
                                                Ver detalle
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Móvil y tablet --}}
                    <div class="divide-y divide-gray-100 lg:hidden">
                        @foreach ($signosVitales as $signoVital)
                            @php
                                $paciente = $signoVital->paciente;
                                $cita = $signoVital->cita;
                                $medico = $cita?->medico;

                                $nombrePaciente = trim(
                                    ($paciente?->nombre ?? '') . ' ' .
                                    ($paciente?->apellido_paterno ?? $paciente?->apellido ?? '') . ' ' .
                                    ($paciente?->apellido_materno ?? '')
                                );

                                $nombreMedico = trim(
                                    ($medico?->nombre ?? '') . ' ' .
                                    ($medico?->apellido_paterno ?? '') . ' ' .
                                    ($medico?->apellido_materno ?? '')
                                );

                                $fechaCita = $cita?->fecha
                                    ? \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y')
                                    : 'Sin fecha';

                                $horaCita = $cita?->hora
                                    ? \Carbon\Carbon::parse($cita->hora)->format('H:i')
                                    : 'Sin hora';
                            @endphp

                            <article class="p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">
                                            {{ mb_strtoupper(mb_substr($nombrePaciente ?: 'P', 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <h4 class="truncate font-bold text-gray-900">
                                                {{ $nombrePaciente ?: 'Paciente no disponible' }}
                                            </h4>

                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $fechaCita }} · {{ $horaCita }}
                                            </p>
                                        </div>
                                    </div>

                                    <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600">
                                        #{{ $signoVital->id }}
                                    </span>
                                </div>

                                <dl class="mt-5 grid grid-cols-2 gap-3">
                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <dt class="text-xs font-medium text-gray-500">
                                            Peso
                                        </dt>

                                        <dd class="mt-1 font-bold text-gray-900">
                                            {{ number_format((float) $signoVital->peso, 2) }} kg
                                        </dd>
                                    </div>

                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <dt class="text-xs font-medium text-gray-500">
                                            Presión
                                        </dt>

                                        <dd class="mt-1 font-bold text-gray-900">
                                            @if (
                                                $signoVital->presion_sistolica !== null &&
                                                $signoVital->presion_diastolica !== null
                                            )
                                                {{ $signoVital->presion_sistolica }}/{{ $signoVital->presion_diastolica }}
                                            @else
                                                No registrada
                                            @endif
                                        </dd>
                                    </div>

                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <dt class="text-xs font-medium text-gray-500">
                                            Médico
                                        </dt>

                                        <dd class="mt-1 truncate text-sm font-semibold text-gray-900">
                                            {{ $nombreMedico ?: 'No asignado' }}
                                        </dd>
                                    </div>

                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <dt class="text-xs font-medium text-gray-500">
                                            Registró
                                        </dt>

                                        <dd class="mt-1 truncate text-sm font-semibold text-gray-900">
                                            {{ $signoVital->enfermero?->name ?? 'No disponible' }}
                                        </dd>
                                    </div>
                                </dl>

                                <a
                                    href="{{ route('signos-vitales.show', $signoVital) }}"
                                    class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                                >
                                    Ver detalle
                                </a>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            @if ($signosVitales->hasPages())
                <div class="rounded-2xl border border-gray-200 bg-white px-5 py-4 shadow-sm">
                    {{ $signosVitales->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>