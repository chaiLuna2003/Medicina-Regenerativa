<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detalle de signos vitales
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Valoración registrada para la cita #{{ $signoVital->cita_id }}
                </p>
            </div>

            <a
                href="{{ route('signos-vitales.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
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
                        d="M15 19l-7-7 7-7"
                    />
                </svg>

                Volver al historial
            </a>
        </div>
    </x-slot>

    @php
        $paciente = $signoVital->paciente;
        $cita = $signoVital->cita;
        $medico = $cita?->medico;
        $enfermero = $signoVital->enfermero;

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

        $fechaNacimiento = $paciente?->fecha_nacimiento
            ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y')
            : 'No registrada';

        $fechaCita = $cita?->fecha
            ? \Carbon\Carbon::parse($cita->fecha)
                ->locale('es')
                ->translatedFormat('d \d\e F \d\e Y')
            : 'No disponible';

        $horaCita = $cita?->hora
            ? \Carbon\Carbon::parse($cita->hora)->format('H:i')
            : 'No disponible';

        $fechaRegistro = $signoVital->created_at
            ? $signoVital->created_at
                ->locale('es')
                ->translatedFormat('d \d\e F \d\e Y, H:i')
            : 'No disponible';

        $imc = $signoVital->imc;

        if ($imc === null) {
            $clasificacionImc = 'Sin calcular';
            $claseImc = 'bg-gray-100 text-gray-700';
        } elseif ($imc < 18.5) {
            $clasificacionImc = 'Peso bajo';
            $claseImc = 'bg-blue-100 text-blue-700';
        } elseif ($imc < 25) {
            $clasificacionImc = 'Rango normal';
            $claseImc = 'bg-emerald-100 text-emerald-700';
        } elseif ($imc < 30) {
            $clasificacionImc = 'Sobrepeso';
            $claseImc = 'bg-amber-100 text-amber-700';
        } else {
            $clasificacionImc = 'Obesidad';
            $claseImc = 'bg-red-100 text-red-700';
        }
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="rounded-full bg-emerald-100 p-2 text-emerald-700">
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

                        <div>
                            <p class="font-semibold text-emerald-900">
                                Registro completado
                            </p>

                            <p class="mt-1 text-sm text-emerald-700">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm font-medium text-blue-800">
                        {{ session('info') }}
                    </p>
                </div>
            @endif

            {{-- Encabezado de la valoración --}}
            <section class="relative overflow-hidden rounded-2xl bg-gray-900 p-6 text-white shadow-sm">
                <div class="absolute -right-16 -top-16 h-52 w-52 rounded-full bg-emerald-400/10"></div>
                <div class="absolute -bottom-20 right-32 h-48 w-48 rounded-full bg-blue-400/10"></div>

                <div class="relative grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="md:col-span-2">
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-300">
                            Paciente
                        </p>

                        <h3 class="mt-2 text-2xl font-bold">
                            {{ $nombrePaciente ?: 'Paciente no disponible' }}
                        </h3>

                        <p class="mt-2 text-sm text-gray-300">
                            Fecha de nacimiento: {{ $fechaNacimiento }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-400">
                            Fecha de la cita
                        </p>

                        <p class="mt-2 font-semibold">
                            {{ $fechaCita }}
                        </p>

                        <p class="mt-1 text-sm text-gray-300">
                            {{ $horaCita }} horas
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-400">
                            Médico asignado
                        </p>

                        <p class="mt-2 font-semibold">
                            {{ $nombreMedico ? 'Dr. ' . $nombreMedico : 'No asignado' }}
                        </p>

                        @if ($cita?->motivo)
                            <p class="mt-1 text-sm text-gray-300">
                                {{ $cita->motivo }}
                            </p>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Resumen --}}
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Peso
                    </p>

                    <div class="mt-3 flex items-end gap-2">
                        <p class="text-3xl font-bold text-gray-900">
                            {{ number_format((float) $signoVital->peso, 2) }}
                        </p>

                        <span class="pb-1 text-sm font-semibold text-gray-400">
                            kg
                        </span>
                    </div>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Estatura
                    </p>

                    <div class="mt-3 flex items-end gap-2">
                        <p class="text-3xl font-bold text-gray-900">
                            {{ number_format((float) $signoVital->estatura, 2) }}
                        </p>

                        <span class="pb-1 text-sm font-semibold text-gray-400">
                            cm
                        </span>
                    </div>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Índice de masa corporal
                    </p>

                    <div class="mt-3 flex items-center justify-between gap-3">
                        <p class="text-3xl font-bold text-gray-900">
                            {{ $imc !== null ? number_format($imc, 2) : '—' }}
                        </p>

                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $claseImc }}">
                            {{ $clasificacionImc }}
                        </span>
                    </div>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Presión arterial
                    </p>

                    <div class="mt-3 flex items-end gap-2">
                        <p class="text-3xl font-bold text-gray-900">
                            @if (
                                $signoVital->presion_sistolica !== null &&
                                $signoVital->presion_diastolica !== null
                            )
                                {{ $signoVital->presion_sistolica }}/{{ $signoVital->presion_diastolica }}
                            @else
                                —
                            @endif
                        </p>

                        @if (
                            $signoVital->presion_sistolica !== null &&
                            $signoVital->presion_diastolica !== null
                        )
                            <span class="pb-1 text-sm font-semibold text-gray-400">
                                mmHg
                            </span>
                        @endif
                    </div>
                </article>
            </section>

            {{-- Signos vitales --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-lg font-bold text-gray-900">
                        Mediciones registradas
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Resultados capturados durante la valoración del paciente.
                    </p>
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-xl bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Temperatura
                        </dt>

                        <dd class="mt-2 text-xl font-bold text-gray-900">
                            {{ $signoVital->temperatura !== null
                                ? number_format((float) $signoVital->temperatura, 1) . ' °C'
                                : 'No registrada' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Frecuencia cardiaca
                        </dt>

                        <dd class="mt-2 text-xl font-bold text-gray-900">
                            {{ $signoVital->frecuencia_cardiaca !== null
                                ? $signoVital->frecuencia_cardiaca . ' lpm'
                                : 'No registrada' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Frecuencia respiratoria
                        </dt>

                        <dd class="mt-2 text-xl font-bold text-gray-900">
                            {{ $signoVital->frecuencia_respiratoria !== null
                                ? $signoVital->frecuencia_respiratoria . ' rpm'
                                : 'No registrada' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Saturación de oxígeno
                        </dt>

                        <dd class="mt-2 text-xl font-bold text-gray-900">
                            {{ $signoVital->saturacion_oxigeno !== null
                                ? $signoVital->saturacion_oxigeno . ' %'
                                : 'No registrada' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Glucosa
                        </dt>

                        <dd class="mt-2 text-xl font-bold text-gray-900">
                            {{ $signoVital->glucosa !== null
                                ? number_format((float) $signoVital->glucosa, 2) . ' mg/dL'
                                : 'No registrada' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Presión arterial
                        </dt>

                        <dd class="mt-2 text-xl font-bold text-gray-900">
                            @if (
                                $signoVital->presion_sistolica !== null &&
                                $signoVital->presion_diastolica !== null
                            )
                                {{ $signoVital->presion_sistolica }}/{{ $signoVital->presion_diastolica }}
                                <span class="text-sm font-semibold text-gray-400">
                                    mmHg
                                </span>
                            @else
                                No registrada
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- Observaciones --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900">
                    Observaciones
                </h3>

                <div class="mt-4 rounded-xl border border-gray-100 bg-gray-50 p-5">
                    @if ($signoVital->observaciones)
                        <p class="whitespace-pre-line text-sm leading-7 text-gray-700">
                            {{ $signoVital->observaciones }}
                        </p>
                    @else
                        <p class="text-sm italic text-gray-400">
                            No se registraron observaciones para esta valoración.
                        </p>
                    @endif
                </div>
            </section>

            {{-- Información del registro --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900">
                    Información del registro
                </h3>

                <dl class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Registrado por
                        </dt>

                        <dd class="mt-1 font-semibold text-gray-900">
                            {{ $enfermero?->name ?? 'Usuario no disponible' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Fecha de registro
                        </dt>

                        <dd class="mt-1 font-semibold text-gray-900">
                            {{ $fechaRegistro }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Folio de valoración
                        </dt>

                        <dd class="mt-1 font-semibold text-gray-900">
                            #{{ str_pad((string) $signoVital->id, 6, '0', STR_PAD_LEFT) }}
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- Acciones --}}
            <section class="flex flex-col-reverse gap-3 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                >
                    Ir al dashboard
                </a>

                <a
                    href="{{ route('signos-vitales.index') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800"
                >
                    Ver historial de valoraciones
                </a>
            </section>
        </div>
    </div>
</x-app-layout>