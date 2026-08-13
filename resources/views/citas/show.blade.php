<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-[#0D3B7F]">
                    Detalle de la cita
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    {{ $cita->paciente?->nombre }}
                    {{ $cita->paciente?->apellido }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Consulta la información completa de la cita.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a
                    href="{{ route('citas.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border
                           border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold
                           text-gray-700 transition hover:bg-gray-50"
                >
                    Volver a la agenda
                </a>

                @if (in_array(auth()->user()->role, ['admin', 'recepcionista'], true))
                    <a
                        href="{{ route('citas.edit', $cita) }}"
                        class="inline-flex items-center justify-center rounded-xl
                               bg-[#0D3B7F] px-5 py-2.5 text-sm font-semibold
                               text-white transition hover:bg-[#082a5d]"
                    >
                        Editar cita
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    @php
        $usuario = auth()->user();
        $esAdministrador = $usuario->role === 'admin';
        $esMedico = $usuario->role === 'medico';

        /*
         * Comprueba que el usuario autenticado sea el usuario
         * asociado con el médico asignado a esta cita.
         */
        $esMedicoResponsable = $esMedico
            && (int) $cita->medico?->user_id === (int) $usuario->id;

        $receta = $cita->receta;

        $estadoClases = match ($cita->estado) {
            'confirmada' => 'bg-green-100 text-green-700',
            'en_espera' => 'bg-amber-100 text-amber-700',
            'en_consulta' => 'bg-blue-100 text-blue-700',
            'finalizada' => 'bg-gray-100 text-gray-700',
            'cancelada' => 'bg-red-100 text-red-700',
            default => 'bg-indigo-100 text-indigo-700',
        };

        $estadoTexto = match ($cita->estado) {
            'en_espera' => 'En espera',
            'en_consulta' => 'En consulta',
            default => ucfirst($cita->estado),
        };
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4">
                    <p class="text-sm font-semibold text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-semibold text-red-800">
                        {{ session('error') }}
                    </p>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Información principal --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200
                            bg-white shadow-sm lg:col-span-2">

                    <div class="border-b border-gray-200 px-6 py-5">
                        <div class="flex flex-col gap-3 sm:flex-row
                                    sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    Información de la cita
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Fecha, horario y motivo de atención.
                                </p>
                            </div>

                            <span class="inline-flex w-fit rounded-full px-3 py-1
                                         text-xs font-semibold {{ $estadoClases }}">
                                {{ $estadoTexto }}
                            </span>
                        </div>
                    </div>

                    <div class="grid gap-6 p-6 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Fecha
                            </p>

                            <p class="mt-2 font-semibold text-gray-900">
                                {{ $cita->fecha->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Hora
                            </p>

                            <p class="mt-2 font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Motivo
                            </p>

                            <p class="mt-2 text-gray-700">
                                {{ $cita->motivo }}
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Notas adicionales
                            </p>

                            <p class="mt-2 whitespace-pre-line text-gray-700">
                                {{ $cita->notas ?: 'No se agregaron notas para esta cita.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Signos vitales --}}
                    <div class="border-t border-gray-200 px-6 py-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                Signos vitales
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Datos registrados por el personal de enfermería.
                            </p>
                        </div>

                        @if ($cita->signoVital)
                            @php
                                $signoVital = $cita->signoVital;

                                $pastillasSignosVitales = [
                                    [
                                        'Peso',
                                        number_format((float) $signoVital->peso, 2).' kg'
                                    ],
                                    [
                                        'Estatura',
                                        number_format((float) $signoVital->estatura, 2).' cm'
                                    ],
                                    [
                                        'IMC',
                                        $signoVital->imc !== null
                                            ? number_format((float) $signoVital->imc, 2)
                                            : 'No disponible'
                                    ],
                                    [
                                        'Temperatura',
                                        $signoVital->temperatura !== null
                                            ? number_format(
                                                (float) $signoVital->temperatura,
                                                1
                                            ).' °C'
                                            : 'No disponible'
                                    ],
                                    [
                                        'Presión arterial',
                                        $signoVital->presion_sistolica !== null
                                        && $signoVital->presion_diastolica !== null
                                            ? $signoVital->presion_sistolica.'/'.
                                              $signoVital->presion_diastolica.' mmHg'
                                            : 'No disponible'
                                    ],
                                    [
                                        'Frecuencia cardiaca',
                                        $signoVital->frecuencia_cardiaca !== null
                                            ? $signoVital->frecuencia_cardiaca.' lpm'
                                            : 'No disponible'
                                    ],
                                    [
                                        'Frecuencia respiratoria',
                                        $signoVital->frecuencia_respiratoria !== null
                                            ? $signoVital->frecuencia_respiratoria.' rpm'
                                            : 'No disponible'
                                    ],
                                    [
                                        'Saturación de oxígeno',
                                        $signoVital->saturacion_oxigeno !== null
                                            ? $signoVital->saturacion_oxigeno.'%'
                                            : 'No disponible'
                                    ],
                                    [
                                        'Glucosa',
                                        $signoVital->glucosa !== null
                                            ? number_format(
                                                (float) $signoVital->glucosa,
                                                2
                                            ).' mg/dL'
                                            : 'No disponible'
                                    ],
                                ];
                            @endphp

                            <div class="mt-5 flex flex-wrap gap-3">
                                @foreach ($pastillasSignosVitales as [$etiqueta, $valor])
                                    <div class="inline-flex items-center gap-2 rounded-full
                                                border border-blue-200 bg-blue-50 px-4 py-2
                                                text-sm text-blue-900">
                                        <span class="font-medium text-blue-600">
                                            {{ $etiqueta }}
                                        </span>

                                        <span class="font-bold">
                                            {{ $valor }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if ($signoVital->observaciones)
                                <div class="mt-5 rounded-xl border border-blue-100
                                            bg-blue-50/60 p-4">
                                    <p class="text-xs font-semibold uppercase
                                              tracking-wide text-blue-600">
                                        Observaciones de enfermería
                                    </p>

                                    <p class="mt-2 whitespace-pre-line text-sm text-blue-950">
                                        {{ $signoVital->observaciones }}
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="mt-5 rounded-xl border border-dashed
                                        border-blue-200 bg-blue-50 px-4 py-4
                                        text-sm font-medium text-blue-700">
                                Enfermería todavía no ha registrado signos vitales
                                para esta cita.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Columna lateral --}}
                <div class="space-y-6">

                    {{-- Paciente --}}
                    @if ($cita->paciente)
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                            <div class="flex items-center gap-4">
                                <img
                                    src="{{ $cita->paciente->fotoUrl() }}"
                                    alt="Foto de {{ $cita->paciente->nombre }}"
                                    class="h-16 w-16 shrink-0 rounded-full border-2
                                           border-blue-100 object-cover shadow-sm"
                                >

                                <div class="min-w-0">
                                    <h3 class="text-lg font-bold text-gray-900">
                                        {{ $cita->paciente->nombre }}
                                        {{ $cita->paciente->apellido }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-600">
                                        <span class="font-semibold">Edad:</span>
                                        {{ $cita->paciente->edad }}
                                    </p>
                                </div>
                            </div>

                            {{-- Información completa solo para administrador --}}
                            @if ($esAdministrador)
                                <div class="mt-5 grid gap-4 border-t border-gray-100
                                            pt-5 sm:grid-cols-2 lg:grid-cols-1">
                                    <div>
                                        <p class="text-xs font-semibold uppercase
                                                  tracking-wide text-gray-400">
                                            Teléfono
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-gray-700">
                                            {{ $cita->paciente->telefono ?: 'No registrado' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase
                                                  tracking-wide text-gray-400">
                                            Correo electrónico
                                        </p>

                                        <p class="mt-1 break-all text-sm font-medium text-gray-700">
                                            {{ $cita->paciente->email ?: 'No registrado' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase
                                                  tracking-wide text-gray-400">
                                            Fecha de nacimiento
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-gray-700">
                                            {{ $cita->paciente->fecha_nacimiento?->format('d/m/Y') ?? 'No registrada' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase
                                                  tracking-wide text-gray-400">
                                            Estado
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-gray-700">
                                            {{ $cita->paciente->status ? 'Activo' : 'Inactivo' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                            <p class="text-sm text-gray-500">
                                Paciente no disponible.
                            </p>
                        </div>
                    @endif

                    {{-- Acciones de receta --}}
                    @if (($esAdministrador || $esMedicoResponsable) && $cita->paciente)
                        <div class="overflow-hidden rounded-2xl border border-gray-200
                                    bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="rounded-xl bg-indigo-100 p-2.5 text-indigo-700">
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
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0
                                                   01-2-2V5a2 2 0 012-2h5.586a1
                                                   1 0 01.707.293l3.414 3.414A1
                                                   1 0 0117 7.414V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="font-bold text-gray-900">
                                            Receta médica
                                        </h3>

                                        <p class="mt-0.5 text-xs text-gray-500">
                                            Tratamiento e historial clínico.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3 p-6">
                                @if ($receta)
                                    {{-- Administrador y médico responsable --}}
                                    <a
                                        href="{{ route('recetas.show', $receta) }}"
                                        class="inline-flex w-full items-center justify-center
                                               gap-2 rounded-xl bg-[#0D3B7F] px-4 py-3
                                               text-sm font-semibold text-white transition
                                               hover:bg-[#082a5d]"
                                    >
                                        Ver receta
                                    </a>

                                    {{-- Únicamente el médico responsable --}}
                                    @if ($esMedicoResponsable)
                                        <a
                                            href="{{ route('recetas.edit', $receta) }}"
                                            class="inline-flex w-full items-center justify-center
                                                   gap-2 rounded-xl border border-[#0D3B7F]
                                                   bg-white px-4 py-3 text-sm font-semibold
                                                   text-[#0D3B7F] transition hover:bg-blue-50"
                                        >
                                            Editar receta
                                        </a>
                                    @endif
                                @elseif ($esMedicoResponsable)
                                    <div class="rounded-xl border border-dashed
                                                border-amber-300 bg-amber-50 p-4">
                                        <p class="text-sm font-semibold text-amber-900">
                                            Esta cita todavía no tiene receta
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-amber-700">
                                            Puedes registrar el tratamiento e indicaciones
                                            correspondientes a la consulta.
                                        </p>
                                    </div>

                                    <a
                                        href="{{ route('citas.receta.create', $cita) }}"
                                        class="inline-flex w-full items-center justify-center
                                               gap-2 rounded-xl bg-[#0D3B7F] px-4 py-3
                                               text-sm font-semibold text-white transition
                                               hover:bg-[#082a5d]"
                                    >
                                        Elaborar receta
                                    </a>
                                @else
                                    <div class="rounded-xl border border-dashed
                                                border-gray-300 bg-gray-50 p-4">
                                        <p class="text-sm text-gray-600">
                                            Esta cita todavía no tiene una receta registrada.
                                        </p>
                                    </div>
                                @endif

                                {{-- Administrador o médico relacionado --}}
                                <a
                                    href="{{ route(
                                        'pacientes.recetas.index',
                                        $cita->paciente
                                    ) }}"
                                    class="inline-flex w-full items-center justify-center
                                           gap-2 rounded-xl border border-gray-300 bg-white
                                           px-4 py-3 text-sm font-semibold text-gray-700
                                           transition hover:bg-gray-50"
                                >
                                    Ver historial de recetas
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Médico --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Médico asignado
                        </p>

                        <h3 class="mt-2 text-lg font-bold text-gray-900">
                            Dr. {{ $cita->medico?->nombre }}
                            {{ $cita->medico?->apellido_paterno }}
                        </h3>

                        <p class="mt-2 text-sm text-gray-600">
                            {{ $cita->medico?->especialidad ?: 'Sin especialidad registrada' }}
                        </p>
                    </div>

                    {{-- Usuario que registró la cita --}}
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Registrada por
                        </p>

                        <p class="mt-2 font-semibold text-gray-900">
                            {{ $cita->creadoPor?->name ?: 'Usuario no disponible' }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $cita->created_at->format('d/m/Y, h:i A') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>