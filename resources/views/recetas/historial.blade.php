


<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-[#0D3B7F]">Expediente clínico</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Historial de recetas
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Consulta las indicaciones médicas registradas para este paciente.
                </p>
            </div>

            <a
                href="{{ url()->previous() }}"
                class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            >
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $nombrePaciente = trim(
            ($paciente?->nombre ?? '') . ' ' .
            ($paciente?->apellido_paterno ?? $paciente?->apellido ?? '') . ' ' .
            ($paciente?->apellido_materno ?? '')
        );
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="relative overflow-hidden rounded-2xl bg-[#0D3B7F] p-6 text-white shadow-sm">
                <div class="absolute -right-16 -top-16 h-52 w-52 rounded-full bg-blue-300/10"></div>

                <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <img
                            src="{{ $paciente->fotoUrl() }}"
                            alt="Foto de {{ $nombrePaciente }}"
                            class="h-20 w-20 shrink-0 rounded-2xl border-2 border-white/30 object-cover"
                        >

                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-blue-200">
                                Paciente
                            </p>
                            <h3 class="mt-2 text-2xl font-bold">
                                {{ $nombrePaciente ?: 'Paciente no disponible' }}
                            </h3>
                            <p class="mt-1 text-sm text-blue-100">
                                Edad: {{ $paciente->edad ?? 'No disponible' }}
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/10 px-6 py-4 text-center backdrop-blur-sm">
                        <p class="text-3xl font-bold">{{ $recetas->count() }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-blue-100">
                            {{ $recetas->count() === 1 ? 'Receta registrada' : 'Recetas registradas' }}
                        </p>
                    </div>
                </div>
            </section>

            @if ($recetas->isEmpty())
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center shadow-sm">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-[#0D3B7F]">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-gray-900">Sin recetas registradas</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                        Este paciente todavía no cuenta con recetas médicas dentro de su historial.
                    </p>
                </section>
            @else
                <section class="space-y-4">
                    @foreach ($recetas as $receta)
                        @php
                            $cita = $receta->cita;
                            $medico = $cita?->medico;
                            $nombreMedico = trim(
                                ($medico?->nombre ?? '') . ' ' .
                                ($medico?->apellido_paterno ?? '') . ' ' .
                                ($medico?->apellido_materno ?? '')
                            );

                            if ($nombreMedico === '') {
                                $nombreMedico = $medico?->user?->name ?? '';
                            }

                            $fecha = $receta->fecha_expedicion
                                ? $receta->fecha_expedicion
                                    ->locale('es')
                                    ->translatedFormat('d \d\e F \d\e Y, h:i A')
                                : 'Fecha no disponible';
                        @endphp

                        <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:border-blue-200 hover:shadow-md">
                            <div class="grid gap-5 p-6 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#0D3B7F]">
                                            {{ $fecha }}
                                        </span>

                                        @if ($medico?->especialidad)
                                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                {{ $medico->especialidad }}
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="mt-4 text-base font-bold text-gray-900">
                                        {{ $nombreMedico !== '' ? 'Dr. '.$nombreMedico : 'Médico no disponible' }}
                                    </h3>

                                    <p class="mt-2 line-clamp-3 whitespace-pre-line text-sm leading-6 text-gray-600">
                                        {{ $receta->contenido }}
                                    </p>

                                    @if ($cita?->motivo)
                                        <p class="mt-3 text-xs text-gray-500">
                                            <span class="font-semibold text-gray-700">Motivo:</span>
                                            {{ $cita->motivo }}
                                        </p>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('recetas.show', $receta) }}"
                                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-[#0D3B7F] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#082a5d]"
                                >
                                    Ver receta
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </section>
            @endif
        </div>
    </div>
</x-app-layout>