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

            <div class="flex flex-wrap items-center gap-3 sm:justify-end">


                {{-- Acciones exclusivas de administración y recepción --}}
                @if (in_array(auth()->user()->role, ['admin', 'recepcionista'], true))

                <a
                    href="{{ route('citas.edit', $cita) }}"
                    class="inline-flex items-center justify-center rounded-xl
                   bg-[#0D3B7F] px-5 py-2.5 text-sm font-semibold
                   text-white transition hover:bg-[#082a5d]">
                    Editar cita
                </a>

                <button
                    type="button"
                    data-abrir-estudios
                    onclick="abrirModalEstudios(this)"
                    class="inline-flex items-center justify-center rounded-xl
                   bg-emerald-600 px-5 py-2.5 text-sm font-semibold
                   text-white transition hover:bg-emerald-700">
                    Agregar estudios
                </button>

                @endif
                @if (auth()->user()->role === 'medico')
                @if ($cita->paciente)
                <a
                    href="{{ route('pacientes.show', $cita->paciente) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center
               whitespace-nowrap rounded-xl
               bg-emerald-600 px-5 py-2.5
               text-sm font-semibold text-white
               shadow-sm transition
               hover:bg-emerald-700
               focus-visible:outline-none
               focus-visible:ring-2
               focus-visible:ring-emerald-600
               focus-visible:ring-offset-2">

                    <svg
                        class="mr-2 h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6M7 3h7l4 4v14H7
                   a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5
                   a2 2 0 0 1 2-2h5l5 5v11
                   a2 2 0 0 1-2 2z" />
                    </svg>

                    Ver ficha del paciente
                </a>
                @endif
                @if ($cita->receta)
                <a
                    href="{{ route('recetas.show', $cita->receta) }}"
                    class="inline-flex whitespace-nowrap items-center justify-center rounded-xl
                   border border-[#0D3B7F] bg-white px-4 py-2.5
                   text-sm font-semibold text-[#0D3B7F]
                   transition hover:bg-[#0D3B7F] hover:text-white">
                    Ver receta
                </a>
                @else
                <a
                    href="{{ route('citas.receta.create', $cita) }}"
                    class="inline-flex items-center justify-center
           whitespace-nowrap rounded-xl
           bg-[#0D3B7F] px-5 py-2.5
           text-sm font-semibold text-white
           shadow-sm transition hover:bg-[#082a5d]">
                    Crear receta
                </a>
                @endif
                @endif
            </div>

        </div>
    </x-slot>

    @php
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
            @if (session('success'))
            <div
                class="mb-6 rounded-xl border
               border-green-200 bg-green-50
               p-4 text-sm font-semibold
               text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div
                class="mb-6 rounded-xl border
               border-red-200 bg-red-50
               p-4 text-sm font-semibold
               text-red-800">
                {{ session('error') }}
            </div>
            @endif

            @if ($cita->modalidad === 'videoconsulta')
            @php
            /*
            * Normaliza números mexicanos para wa.me.
            */
            $normalizarTelefono = function (
            ?string $telefono
            ): ?string {
            $telefono = preg_replace(
            '/\D+/',
            '',
            (string) $telefono
            );

            if (strlen($telefono) === 10) {
            $telefono = '52' . $telefono;
            }

            return $telefono !== ''
            ? $telefono
            : null;
            };

            $fechaVideoconsulta =
            $cita->fecha->format('d/m/Y');

            $horaVideoconsulta =
            \Carbon\Carbon::parse(
            $cita->hora
            )->format('h:i A');

            $telefonoPaciente =
            $normalizarTelefono(
            $cita->paciente?->telefono
            );

            $telefonoMedico =
            $normalizarTelefono(
            $cita->medico?->telefono
            );

            $mensajePaciente = rawurlencode(
            "Hola {$cita->paciente?->nombre}, "
            . "su videoconsulta está programada "
            . "para el {$fechaVideoconsulta} "
            . "a las {$horaVideoconsulta}. "
            . "Enlace de Google Meet: "
            . "{$cita->google_meet_url}"
            );

            $mensajeMedico = rawurlencode(
            "Doctor {$cita->medico?->nombre}, "
            . "tiene una videoconsulta programada "
            . "para el {$fechaVideoconsulta} "
            . "a las {$horaVideoconsulta}. "
            . "Enlace de Google Meet: "
            . "{$cita->google_meet_url}"
            );

            $estadoMeetTexto = match (
            $cita->estado_videoconferencia
            ) {
            'disponible' => 'Enlace disponible',
            'pendiente' => 'Generando enlace',
            'fallido' => 'Error al generar enlace',
            'cancelado' => 'Videoconsulta cancelada',
            default => 'Sin configurar',
            };

            $estadoMeetClases = match (
            $cita->estado_videoconferencia
            ) {
            'disponible' =>
            'bg-green-100 text-green-700',

            'pendiente' =>
            'bg-amber-100 text-amber-700',

            'fallido' =>
            'bg-red-100 text-red-700',

            'cancelado' =>
            'bg-gray-200 text-gray-700',

            default =>
            'bg-gray-100 text-gray-600',
            };
            @endphp

            <section
                class="mb-6 rounded-2xl border
               border-blue-200 bg-blue-50
               p-6 shadow-sm">
                <div
                    class="flex flex-col gap-5
                   sm:flex-row sm:items-center
                   sm:justify-between">
                    <div>
                        <p
                            class="text-xs font-semibold
                           uppercase tracking-wide
                           text-blue-600">
                            Videoconsulta
                        </p>

                        <h3
                            class="mt-1 text-xl font-bold
                           text-blue-950">
                            Google Meet
                        </h3>

                        <span
                            class="mt-3 inline-flex
                           rounded-full px-3 py-1
                           text-xs font-semibold
                           {{ $estadoMeetClases }}">
                            {{ $estadoMeetTexto }}
                        </span>
                    </div>

                    @if (
                    $cita->google_meet_url &&
                    $cita->estado !== 'cancelada' &&
                    $cita->estado_videoconferencia !== 'cancelado'
                    )
                    <a
                        href="{{ $cita->google_meet_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center
               justify-center rounded-xl
               bg-[#0D3B7F] px-5 py-3
               text-sm font-semibold
               text-white transition
               hover:bg-[#082a5d]">
                        Entrar a Google Meet
                    </a>
                    @elseif (
                    !$cita->google_meet_url &&
                    $cita->estado !== 'cancelada' &&
                    $cita->estado_videoconferencia !== 'cancelado' &&
                    in_array(
                    auth()->user()->role,
                    ['admin', 'recepcionista'],
                    true
                    )
                    )
                    <form
                        method="POST"
                        action="{{ route('citas.generar-meet', $cita) }}">
                        @csrf

                        <button
                            type="submit"
                            class="inline-flex items-center
                   justify-center rounded-xl
                   bg-[#0D3B7F] px-5 py-3
                   text-sm font-semibold
                   text-white transition
                   hover:bg-[#082a5d]">
                            Generar o consultar enlace
                        </button>
                    </form>
                    @endif
                </div>

                @if (
                $cita->google_meet_url
                && in_array(
                auth()->user()->role,
                ['admin', 'recepcionista'],
                true
                )
                )
                <div
                    class="mt-5 grid gap-3 border-t
                       border-blue-200 pt-5
                       sm:grid-cols-2">
                    {{-- WhatsApp del paciente --}}
                    @if ($telefonoPaciente)
                    <a
                        href="https://wa.me/{{ $telefonoPaciente }}?text={{ $mensajePaciente }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center
                               justify-center rounded-xl
                               bg-green-600 px-4 py-3
                               text-sm font-semibold
                               text-white transition
                               hover:bg-green-700">
                        Enviar al paciente
                    </a>
                    @else
                    <p
                        class="rounded-xl bg-white p-3
                               text-sm text-gray-600">
                        El paciente no tiene teléfono.
                    </p>
                    @endif

                    {{-- WhatsApp del médico --}}
                    @if ($telefonoMedico)
                    <a
                        href="https://wa.me/{{ $telefonoMedico }}?text={{ $mensajeMedico }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center
                               justify-center rounded-xl
                               bg-green-600 px-4 py-3
                               text-sm font-semibold
                               text-white transition
                               hover:bg-green-700">
                        Enviar al médico
                    </a>
                    @else
                    <p
                        class="rounded-xl bg-white p-3
                               text-sm text-gray-600">
                        El médico no tiene teléfono.
                    </p>
                    @endif
                </div>
                @endif
            </section>
            @endif
            @if (
            $cita->modalidad === 'fuera_instalaciones'
            && $cita->direccion_cita
            )
            @php
            $direccionCodificada = rawurlencode(
            $cita->direccion_cita
            );

            $googleMapsUrl =
            'https://www.google.com/maps/search/?api=1&query='
            . $direccionCodificada;

            $wazeUrl =
            'https://waze.com/ul?q='
            . $direccionCodificada
            . '&navigate=yes';

            $telefonoMedicoDireccion = preg_replace(
            '/\D+/',
            '',
            (string) $cita->medico?->telefono
            );

            if (strlen($telefonoMedicoDireccion) === 10) {
            $telefonoMedicoDireccion =
            '52' . $telefonoMedicoDireccion;
            }

            $fechaCitaExterna =
            $cita->fecha->format('d/m/Y');

            $horaCitaExterna =
            \Carbon\Carbon::parse($cita->hora)
            ->format('h:i A');

            $mensajeDireccionMedico = rawurlencode(
            "Doctor {$cita->medico?->nombre}, "
            . "la cita fuera de las instalaciones "
            . "del {$fechaCitaExterna} "
            . "a las {$horaCitaExterna} "
            . "será en: {$cita->direccion_cita}. "
            . "Google Maps: {$googleMapsUrl} "
            . "Waze: {$wazeUrl}"
            );
            @endphp

            <section
                class="mb-6 rounded-2xl border border-amber-200
               bg-amber-50 p-6 shadow-sm">
                <p
                    class="text-xs font-semibold uppercase
                   tracking-wide text-amber-700">
                    Cita fuera de las instalaciones
                </p>

                <h3 class="mt-2 text-lg font-bold text-amber-950">
                    Dirección de la cita
                </h3>

                <p
                    class="mt-2 whitespace-pre-line
                   text-sm text-amber-950">{{ $cita->direccion_cita }}</p>

                <div class="mt-5 flex flex-wrap gap-3">
                    <a
                        href="{{ $googleMapsUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center
                       rounded-xl bg-[#0D3B7F] px-4 py-2.5
                       text-sm font-semibold text-white transition
                       hover:bg-[#082a5d]">
                        Abrir en Google Maps
                    </a>

                    <a
                        href="{{ $wazeUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center
                       rounded-xl border border-sky-300 bg-white
                       px-4 py-2.5 text-sm font-semibold text-sky-700
                       transition hover:bg-sky-50">
                        Abrir en Waze
                    </a>

                    @if (
                    in_array(
                    auth()->user()->role,
                    ['admin', 'recepcionista'],
                    true
                    )
                    && $telefonoMedicoDireccion !== ''
                    )
                    <a
                        href="https://wa.me/{{ $telefonoMedicoDireccion }}?text={{ $mensajeDireccionMedico }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center
                           rounded-xl bg-green-600 px-4 py-2.5
                           text-sm font-semibold text-white transition
                           hover:bg-green-700">
                        Enviar dirección al médico
                    </a>
                    @endif
                </div>

                @if (
                in_array(
                auth()->user()->role,
                ['admin', 'recepcionista'],
                true
                )
                && $telefonoMedicoDireccion === ''
                )
                <p class="mt-4 text-sm font-medium text-amber-800">
                    El médico no tiene un teléfono registrado para WhatsApp.
                </p>
                @endif
            </section>
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
                            <p
                                class="text-xs font-semibold uppercase
               tracking-wide text-gray-400">
                                Horario
                            </p>

                            <p class="mt-2 font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse(
            $cita->hora
        )->format('h:i A') }}

                                <span class="mx-1 text-gray-400">
                                    –
                                </span>

                                {{ $cita->hora_fin->format('h:i A') }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase
               tracking-wide text-gray-400">
                                Duración
                            </p>

                            <p class="mt-2 font-semibold text-gray-900">
                                {{ $cita->duracion_minutos ?? 15 }}
                                {{ ($cita->duracion_minutos ?? 15) === 1
                                ? 'minuto'
                                : 'minutos' }}
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Motivo
                            </p>

                            <div class="mt-3">
                                @php
                                $motivoClases = match ($cita->motivo) {
                                'consulta_inicial' =>
                                'border-blue-200 bg-blue-50 text-blue-700',

                                'consulta_subsecuente' =>
                                'border-emerald-200 bg-emerald-50 text-emerald-700',

                                'consulta_emergencia' =>
                                'border-red-200 bg-red-50 text-red-700',

                                default =>
                                'border-gray-200 bg-gray-50 text-gray-700',
                                };
                                @endphp

                                <span
                                    class="inline-flex items-center rounded-full border px-3 py-1.5
               text-sm font-semibold {{ $motivoClases }}">
                                    {{ $cita->motivo_texto }}
                                </span>
                            </div>
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

                    <div class="border-t border-gray-200 px-6 py-6">
                       <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h3 class="text-lg font-bold text-gray-900">
            Signos vitales
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Datos registrados por el profesional clínico responsable.
        </p>
    </div>

    @if (
        $cita->signoVital
        && in_array(
            auth()->user()->role,
            ['admin', 'medico'],
            true
        )
    )
        <a
            href="{{ route(
                'signos-vitales.show',
                $cita->signoVital
            ) }}"
            class="inline-flex items-center justify-center
                   rounded-xl border border-blue-200
                   bg-white px-4 py-2.5 text-sm
                   font-semibold text-blue-700
                   transition hover:bg-blue-50"
        >
            Ver valoración
        </a>
    @elseif (
        auth()->user()->isMedico()
        && $cita->estado !== 'cancelada'
    )
        <a
            href="{{ route(
                'signos-vitales.create',
                $cita
            ) }}"
            class="inline-flex items-center justify-center
                   rounded-xl bg-blue-600 px-4 py-2.5
                   text-sm font-semibold text-white
                   shadow-sm transition hover:bg-blue-700"
        >
            Registrar signos vitales
        </a>
    @endif
</div>

                        @if ($cita->signoVital)
                        @php
                        $signoVital = $cita->signoVital;

                        $pastillasSignosVitales = [
                        ['Peso', number_format((float) $signoVital->peso, 2) . ' kg'],
                        ['Estatura', number_format((float) $signoVital->estatura, 2) . ' cm'],
                        ['IMC', $signoVital->imc !== null ? number_format($signoVital->imc, 2) : 'No disponible'],
                        ['Temperatura', $signoVital->temperatura !== null ? number_format((float) $signoVital->temperatura, 1) . ' °C' : 'No disponible'],
                        ['Presión arterial', $signoVital->presion_sistolica !== null && $signoVital->presion_diastolica !== null ? $signoVital->presion_sistolica . '/' . $signoVital->presion_diastolica . ' mmHg' : 'No disponible'],
                        ['Frecuencia cardiaca', $signoVital->frecuencia_cardiaca !== null ? $signoVital->frecuencia_cardiaca . ' lpm' : 'No disponible'],
                        ['Frecuencia respiratoria', $signoVital->frecuencia_respiratoria !== null ? $signoVital->frecuencia_respiratoria . ' rpm' : 'No disponible'],
                        ['Saturación de oxígeno', $signoVital->saturacion_oxigeno !== null ? $signoVital->saturacion_oxigeno . '%' : 'No disponible'],
                        ['Glucosa', $signoVital->glucosa !== null ? number_format((float) $signoVital->glucosa, 2) . ' mg/dL' : 'No disponible'],
                        ];
                        @endphp

                        <div class="mt-5 flex flex-wrap gap-3">
                            @foreach ($pastillasSignosVitales as [$etiqueta, $valor])
                            <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-sm text-blue-900">
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
                        <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                Observaciones clínicasObservaciones de enfermería
                            </p>

                            <p class="mt-2 whitespace-pre-line text-sm text-blue-950">
                                {{ $signoVital->observaciones }}
                            </p>
                        </div>
                        @endif
                        @else
                        <div class="mt-5 rounded-xl border border-dashed border-blue-200 bg-blue-50 px-4 py-4 text-sm font-medium text-blue-700">
                            Todavía no se han registrado signos vitales para esta cita.
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Paciente y médico --}}
                <div class="space-y-6">
                    <div>
                        @if ($cita->paciente)
                        @if ($cita->paciente)
                        <div class="rounded-2xl border border-gray-200 bg-white p-5">

                            <div class="flex items-center gap-4">
                                {{-- Foto: visible para médico y administrador --}}
                                <img
                                    src="{{ $cita->paciente->fotoUrl() }}"
                                    alt="Foto de {{ $cita->paciente->nombre }}"
                                    class="h-16 w-16 shrink-0 rounded-full border-2 border-blue-100 object-cover shadow-sm">

                                <div class="min-w-0">
                                    {{-- Nombre: visible para médico y administrador --}}
                                    <h3 class="text-lg font-bold text-gray-900">
                                        {{ $cita->paciente->nombre }}
                                        {{ $cita->paciente->apellido }}
                                    </h3>

                                    {{-- Edad: visible para médico y administrador --}}
                                    <p class="mt-1 text-sm text-gray-600">
                                        <span class="font-semibold">Edad:</span>
                                        {{ $cita->paciente->edad }}
                                    </p>
                                </div>

                            </div>
                            @if (
                            auth()->user()->role === 'medico'
                            && $cita->paciente
                            )
                            <a
                                href="{{ route('pacientes.estudios.index', $cita->paciente) }}"
                                class="mt-4 inline-flex w-full items-center justify-center
               gap-2 rounded-xl border border-[#0D3B7F]
               bg-white px-4 py-2.5 text-sm font-semibold
               text-[#0D3B7F] transition
               hover:bg-[#0D3B7F] hover:text-white">
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
                   a2 2 0 0 1-2-2V5
                   a2 2 0 0 1 2-2h5
                   l5 5v11a2 2 0 0 1-2 2z" />
                                </svg>

                                Ver estudios
                            </a>
                            @endif

                            {{-- Información completa: solamente administrador --}}
                            @if (auth()->user()->role === 'admin')
                            <div class="mt-5 grid gap-4 border-t border-gray-100 pt-5 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Teléfono
                                    </p>
                                    <p class="mt-1 text-sm font-medium text-gray-700">
                                        {{ $cita->paciente->telefono ?: 'No registrado' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Correo electrónico
                                    </p>
                                    <p class="mt-1 break-all text-sm font-medium text-gray-700">
                                        {{ $cita->paciente->email ?: 'No registrado' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Fecha de nacimiento
                                    </p>
                                    <p class="mt-1 text-sm font-medium text-gray-700">
                                        {{ $cita->paciente->fecha_nacimiento?->format('d/m/Y') ?? 'No registrada' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
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
                        <div class="rounded-xl bg-gray-50 p-4">
                            <p class="text-sm text-gray-500">Paciente no disponible.</p>
                        </div>
                        @endif
                        @else
                        <p class="text-sm text-gray-500">
                            Paciente no disponible
                        </p>
                        @endif
                    </div>

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

            @include(
            'citas.sections.panel-clinico'
            )
        </div>
    </div>

    @include(
    'citas.modals.estudios'
    )

    @include(
    'citas.modals.crear-evolucion'
    )

    @include(
    'citas.modals.evolucion-clinica'
    )

    @include(
    'citas.modals.cerrar-caso-clinico'
    )

    @include(
    'citas.modals.aparatos-evolucion'
    )

    @include(
    'citas.modals.historial-caso-clinico'
    )

    @include(
    'citas.modals.graficas-evolucion'
    )
</x-app-layout>

@include(
'citas.scripts.estudios'
)

@include(
'citas.scripts.clinicos'
)

@include(
'citas.scripts.graficas-evolucion'
)