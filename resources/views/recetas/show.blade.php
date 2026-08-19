<x-app-layout>
    @php
    $cita = $receta->cita;
    $paciente = $cita->paciente;
    $medico = $cita->medico;
    $signoVital = $cita->signoVital;
    $usuario = auth()->user();

    $esMedicoResponsable = $usuario->role === 'medico'
    && (int) $medico?->user_id === (int) $usuario->id;
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-[#0D3B7F]">
                    Expediente clínico
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Receta médica
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Expedida el
                    {{ $receta->fecha_expedicion?->locale('es')->translatedFormat('d \d\e F \d\e Y, h:i A') }}
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a
                    href="{{ route('recetas.pdf', $receta) }}"
                    class="inline-flex items-center justify-center gap-2
           rounded-xl bg-emerald-600 px-5 py-2.5
           text-sm font-semibold text-white shadow-sm
           transition hover:bg-emerald-700">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 3v12m0 0 4-4m-4 4-4-4M5.25
               15.75v2.5A2.75 2.75 0 0 0 8
               21h8a2.75 2.75 0 0 0 2.75-2.75
               v-2.5" />
                    </svg>

                    Descargar PDF
                </a>

                <a
                    href="{{ route('citas.show', $cita) }}"
                    class="inline-flex items-center justify-center rounded-xl border
                           border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold
                           text-gray-700 transition hover:bg-gray-50">
                    Volver a la cita
                </a>

                @if ($esMedicoResponsable)
                <a
                    href="{{ route('recetas.edit', $receta) }}"
                    class="inline-flex items-center justify-center rounded-xl
                               bg-[#0D3B7F] px-5 py-2.5 text-sm font-semibold
                               text-white transition hover:bg-[#082a5d]">
                    Editar receta
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            @if (session('success'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4">
                <p class="text-sm font-semibold text-green-800">
                    {{ session('success') }}
                </p>
            </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Contenido principal --}}
                <div class="space-y-6 lg:col-span-2">

                    {{-- Receta --}}
                    <section class="overflow-hidden rounded-2xl border border-gray-200
                                    bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="rounded-xl bg-blue-100 p-3 text-[#0D3B7F]">
                                    <svg
                                        class="h-6 w-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0
                                               01-2-2V5a2 2 0 012-2h5.586a1
                                               1 0 01.707.293l3.414 3.414A1
                                               1 0 0117 7.414V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">
                                        Indicaciones médicas
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Tratamiento indicado durante la consulta.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="min-h-48 whitespace-pre-line text-base
                                        leading-8 text-gray-800">
                                {{ $receta->contenido }}
                            </div>
                        </div>

                        <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                            <p class="text-xs text-gray-500">
                                Última actualización:
                                {{ $receta->updated_at->format('d/m/Y, h:i A') }}
                            </p>
                        </div>
                    </section>

                    {{-- Signos vitales --}}
                    <section class="rounded-2xl border border-gray-200 bg-white
                                    p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900">
                            Signos vitales
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Información registrada para esta consulta.
                        </p>

                        @if ($signoVital)
                        <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            <div class="rounded-xl bg-blue-50 p-4">
                                <p class="text-xs font-semibold uppercase text-blue-600">
                                    Peso
                                </p>

                                <p class="mt-1 font-bold text-blue-950">
                                    {{ number_format((float) $signoVital->peso, 2) }} kg
                                </p>
                            </div>

                            <div class="rounded-xl bg-blue-50 p-4">
                                <p class="text-xs font-semibold uppercase text-blue-600">
                                    Estatura
                                </p>

                                <p class="mt-1 font-bold text-blue-950">
                                    {{ number_format((float) $signoVital->estatura, 2) }} cm
                                </p>
                            </div>

                            <div class="rounded-xl bg-blue-50 p-4">
                                <p class="text-xs font-semibold uppercase text-blue-600">
                                    Temperatura
                                </p>

                                <p class="mt-1 font-bold text-blue-950">
                                    {{ $signoVital->temperatura !== null
                                            ? number_format((float) $signoVital->temperatura, 1).' °C'
                                            : 'No registrada' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-blue-50 p-4">
                                <p class="text-xs font-semibold uppercase text-blue-600">
                                    Presión arterial
                                </p>

                                <p class="mt-1 font-bold text-blue-950">
                                    @if (
                                    $signoVital->presion_sistolica !== null
                                    && $signoVital->presion_diastolica !== null
                                    )
                                    {{ $signoVital->presion_sistolica }}/{{ $signoVital->presion_diastolica }}
                                    mmHg
                                    @else
                                    No registrada
                                    @endif
                                </p>
                            </div>

                            <div class="rounded-xl bg-blue-50 p-4">
                                <p class="text-xs font-semibold uppercase text-blue-600">
                                    Frecuencia cardiaca
                                </p>

                                <p class="mt-1 font-bold text-blue-950">
                                    {{ $signoVital->frecuencia_cardiaca !== null
                                            ? $signoVital->frecuencia_cardiaca.' lpm'
                                            : 'No registrada' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-blue-50 p-4">
                                <p class="text-xs font-semibold uppercase text-blue-600">
                                    Saturación
                                </p>

                                <p class="mt-1 font-bold text-blue-950">
                                    {{ $signoVital->saturacion_oxigeno !== null
                                            ? $signoVital->saturacion_oxigeno.'%'
                                            : 'No registrada' }}
                                </p>
                            </div>
                        </div>

                        @if ($signoVital->observaciones)
                        <div class="mt-5 rounded-xl border border-blue-100
                                            bg-blue-50/60 p-4">
                            <p class="text-xs font-semibold uppercase
                                              tracking-wide text-blue-600">
                                Observaciones
                            </p>

                            <p class="mt-2 whitespace-pre-line text-sm text-blue-950">
                                {{ $signoVital->observaciones }}
                            </p>
                        </div>
                        @endif
                        @else
                        <div class="mt-5 rounded-xl border border-dashed
                                        border-gray-300 bg-gray-50 p-4">
                            <p class="text-sm text-gray-600">
                                Esta consulta no tiene signos vitales registrados.
                            </p>
                        </div>
                        @endif
                    </section>
                </div>

                {{-- Columna lateral --}}
                <aside class="space-y-6">

                    {{-- Paciente --}}
                    <section class="rounded-2xl border border-gray-200 bg-white
                                    p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <img
                                src="{{ $paciente->fotoUrl() }}"
                                alt="Foto de {{ $paciente->nombre }}"
                                class="h-16 w-16 shrink-0 rounded-full border-2
                                       border-blue-100 object-cover">

                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase
                                          tracking-wide text-gray-400">
                                    Paciente
                                </p>

                                <h3 class="mt-1 font-bold text-gray-900">
                                    {{ $paciente->nombre }}
                                    {{ $paciente->apellido }}
                                </h3>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $paciente->edad }}
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ route('pacientes.recetas.index', $paciente) }}"
                            class="mt-5 inline-flex w-full items-center justify-center
                                   rounded-xl border border-gray-300 bg-white px-4 py-3
                                   text-sm font-semibold text-gray-700 transition
                                   hover:bg-gray-50">
                            Ver historial de recetas
                        </a>
                    </section>

                    {{-- Médico --}}
                    <section class="rounded-2xl border border-gray-200 bg-white
                                    p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Médico responsable
                        </p>

                        <h3 class="mt-2 text-lg font-bold text-gray-900">
                            Dr. {{ $medico?->nombre }}
                            {{ $medico?->apellido_paterno }}
                        </h3>

                        <p class="mt-2 text-sm text-gray-600">
                            {{ $medico?->especialidad ?: 'Sin especialidad registrada' }}
                        </p>
                    </section>

                    {{-- Consulta --}}
                    <section class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Consulta relacionada
                        </p>

                        <p class="mt-3 font-semibold text-gray-900">
                            {{ $cita->fecha->format('d/m/Y') }}
                        </p>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                        </p>

                        <p class="mt-4 text-sm leading-6 text-gray-700">
                            {{ $cita->motivo ?: 'Sin motivo registrado.' }}
                        </p>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>