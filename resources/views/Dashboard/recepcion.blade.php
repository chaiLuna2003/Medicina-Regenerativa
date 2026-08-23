<x-app-layout>
    @php
    $medicoSeleccionado = $medicoSeleccionadoId
    ? $medicosFiltro->firstWhere('id', $medicoSeleccionadoId)
    : null;
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-600">
                    Panel de recepción
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Buenos días, {{ auth()->user()->name }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a
                    href="{{ route('citas.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl
                           bg-[#0D3B7F] px-5 py-3 text-sm font-semibold
                           text-white shadow-sm transition hover:bg-[#082a5d]">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4" />
                    </svg>

                    Nueva cita
                </a>

                <a
                    href="{{ route('pacientes.create') }}"
                    class="inline-flex items-center justify-center rounded-xl
                           border border-gray-300 bg-white px-5 py-3
                           text-sm font-semibold text-gray-700 shadow-sm
                           transition hover:bg-gray-50">
                    Nuevo paciente
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- ================================================= --}}
            {{-- CUMPLEAÑOS DE PACIENTES --}}
            {{-- ================================================= --}}
            <section
                class="overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">
                <div
                    class="flex items-center justify-between
               border-b border-slate-100
               px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center
                       justify-center rounded-xl
                       bg-pink-50 text-pink-600">
                            🎂
                        </div>

                        <div>
                            <h3 class="font-semibold text-slate-900">
                                Cumpleaños de pacientes
                            </h3>

                            <p class="text-xs text-slate-400">
                                Hoy y próximos 7 días
                            </p>
                        </div>
                    </div>

                    <span
                        class="rounded-full bg-pink-50
                   px-2.5 py-1 text-xs
                   font-semibold text-pink-700">
                        {{ $cumpleanosPacientes->count() }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100">

                    @forelse ($cumpleanosPacientes as $paciente)

                    @php
                    $esHoy =
                    $paciente->dias_para_cumpleanos === 0;

                    $telefono =
                    preg_replace(
                    '/\D+/',
                    '',
                    (string) $paciente->telefono
                    );

                    if (strlen($telefono) === 10) {
                    $telefono = '52' . $telefono;
                    }

                    $mensaje = rawurlencode(
                    "¡Hola {$paciente->nombre}! 🎉 "
                    . "De parte de todo el equipo de la clínica "
                    . "te deseamos un feliz cumpleaños. "
                    . "Esperamos que tengas un excelente día."
                    );
                    @endphp

                    <div
                        class="flex flex-col gap-4
                       px-6 py-5
                       sm:flex-row
                       sm:items-center
                       sm:justify-between">
                        <div class="flex items-center gap-4">

                            <img
                                src="{{ $paciente->fotoUrl() }}"
                                alt="Foto de {{ $paciente->nombre }}"
                                class="h-12 w-12 rounded-xl
                               border border-slate-200
                               object-cover">

                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p
                                        class="font-semibold text-slate-900">
                                        {{ $paciente->nombre }}
                                        {{ $paciente->apellido }}
                                    </p>

                                    @if ($esHoy)
                                    <span
                                        class="rounded-full
                                           bg-pink-100
                                           px-2 py-0.5
                                           text-[11px]
                                           font-bold
                                           text-pink-700">
                                        Hoy
                                    </span>
                                    @endif
                                </div>

                                <p
                                    class="mt-1 text-sm
                                   text-slate-500">
                                    {{ $paciente
                                ->proximo_cumpleanos
                                ->format('d/m/Y') }}
                                </p>

                                <p
                                    class="mt-1 text-xs
                                   text-slate-400">
                                    Cumple
                                    {{ $paciente->edad_cumpleanos }}
                                    años

                                    @unless ($esHoy)
                                    · En
                                    {{ $paciente->dias_para_cumpleanos }}
                                    {{ $paciente->dias_para_cumpleanos === 1
                                    ? 'día'
                                    : 'días' }}
                                    @endunless
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex flex-wrap
                           items-center gap-2">
                            <a
                                href="{{ route(
                            'pacientes.show',
                            $paciente
                        ) }}"
                                class="inline-flex items-center
                               rounded-lg border
                               border-slate-200
                               bg-white px-3 py-2
                               text-xs font-semibold
                               text-slate-700
                               transition
                               hover:bg-slate-50">
                                Ver paciente
                            </a>

                            @if ($telefono)
                            <a
                                href="https://wa.me/{{ $telefono }}?text={{ $mensaje }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center
                                   rounded-lg
                                   bg-green-600
                                   px-3 py-2
                                   text-xs font-semibold
                                   text-white transition
                                   hover:bg-green-700">
                                Felicitar por WhatsApp
                            </a>
                            @endif
                        </div>
                    </div>

                    @empty

                    <div
                        class="px-6 py-10
                       text-center">
                        <p
                            class="text-sm font-medium
                           text-slate-600">
                            No hay cumpleaños próximos.
                        </p>

                        <p
                            class="mt-1 text-xs
                           text-slate-400">
                            Aquí aparecerán los pacientes
                            que cumplan años en los próximos 7 días.
                        </p>
                    </div>

                    @endforelse

                </div>
            </section>

            {{-- Indicadores generales de hoy --}}
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @php
                $indicadores = [
                [
                'titulo' => 'Citas de hoy',
                'valor' => $totalCitasHoy,
                'descripcion' => 'Total de consultas registradas',
                'barra' => 'bg-blue-500',
                'fondo' => 'bg-blue-50',
                'texto' => 'text-blue-600',
                ],
                [
                'titulo' => 'En espera',
                'valor' => $citasEnEspera,
                'descripcion' => 'Pendientes de atención',
                'barra' => 'bg-amber-500',
                'fondo' => 'bg-amber-50',
                'texto' => 'text-amber-600',
                ],
                [
                'titulo' => 'Confirmadas',
                'valor' => $citasConfirmadas,
                'descripcion' => 'Consultas confirmadas',
                'barra' => 'bg-emerald-500',
                'fondo' => 'bg-emerald-50',
                'texto' => 'text-emerald-600',
                ],
                [
                'titulo' => 'Canceladas',
                'valor' => $citasCanceladas,
                'descripcion' => 'Cancelaciones registradas hoy',
                'barra' => 'bg-red-500',
                'fondo' => 'bg-red-50',
                'texto' => 'text-red-600',
                ],
                ];
                @endphp

                @foreach ($indicadores as $indicador)
                <article class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="absolute inset-x-0 top-0 h-1 {{ $indicador['barra'] }}"></div>

                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                {{ $indicador['titulo'] }}
                            </p>

                            <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                {{ $indicador['valor'] }}
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl {{ $indicador['fondo'] }} {{ $indicador['texto'] }}">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3M5 11h14M5
                                           5h14a2 2 0 012 2v12a2 2
                                           0 01-2 2H5a2 2 0
                                           01-2-2V7a2 2 0 012-2z" />
                            </svg>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-gray-400">
                        {{ $indicador['descripcion'] }}
                    </p>
                </article>
                @endforeach
            </section>

            {{-- Calendario, agenda visual y listado detallado --}}
            <section class="grid gap-6 xl:grid-cols-[390px_minmax(0,1fr)]">

                {{-- Listado detallado de citas --}}
                <div
                    class="order-3 overflow-hidden rounded-2xl
                   border border-gray-200 bg-white shadow-sm
                   xl:col-span-2">

                    {{-- Encabezado --}}
                    <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-[#0D3B7F]/10 text-[#0D3B7F]">
                                <span class="text-xs font-bold uppercase">
                                    {{ $fechaSeleccionada->locale('es')->translatedFormat('M') }}
                                </span>

                                <span class="text-lg font-bold leading-none">
                                    {{ $fechaSeleccionada->format('d') }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    @if ($fechaSeleccionada->isToday())
                                    Agenda de hoy
                                    @else
                                    Agenda del día
                                    @endif
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $fechaSeleccionada->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ $citasSeleccionadas->count() }}
                                {{ $citasSeleccionadas->count() === 1 ? 'cita' : 'citas' }}
                            </span>

                            <a
                                href="{{ route('citas.index', [
                                    'medico_id' => $medicoSeleccionadoId,
                                ]) }}"
                                class="text-sm font-semibold text-[#0D3B7F] hover:text-[#082a5d]">
                                Ver todas
                            </a>
                        </div>
                    </div>

                    {{-- Filtro por médico --}}
                    <div class="border-b border-gray-100 bg-slate-50/70 px-6 py-5">
                        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900">
                                    Filtrar agenda por médico
                                </h4>

                                <p class="mt-1 text-xs text-gray-500">
                                    Las cantidades corresponden a la fecha seleccionada.
                                </p>
                            </div>

                            @if ($medicoSeleccionadoId)
                            <a
                                href="{{ route('dashboard', [
                                        'fecha' => $fechaSeleccionada->format('Y-m-d'),
                                        'mes' => $mesCalendario->format('Y-m'),
                                    ]) }}"
                                class="text-xs font-semibold text-[#0D3B7F] hover:text-[#082a5d]">
                                Quitar filtro
                            </a>
                            @endif
                        </div>

                        {{-- Médico seleccionado --}}
                        @if ($medicoSeleccionado)
                        <div class="mb-4 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-blue-500">
                                Médico seleccionado
                            </p>

                            <p class="mt-1 font-semibold text-[#0D3B7F]">
                                Dr. {{ $medicoSeleccionado->nombre }}
                                {{ $medicoSeleccionado->apellido_paterno }}
                            </p>

                            <p class="mt-1 text-xs text-blue-600">
                                {{ $medicoSeleccionado->citas_fecha_count }}
                                {{ $medicoSeleccionado->citas_fecha_count === 1 ? 'cita programada' : 'citas programadas' }}
                            </p>
                        </div>
                        @endif

                        <div class="flex gap-3 overflow-x-auto pb-2">
                            {{-- Todos --}}
                            <a
                                href="{{ route('dashboard', [
                                    'fecha' => $fechaSeleccionada->format('Y-m-d'),
                                    'mes' => $mesCalendario->format('Y-m'),
                                ]) }}"
                                @class([ 'flex min-w-[155px] shrink-0 items-center justify-between gap-3 rounded-xl border px-4 py-3 transition' , 'border-[#0D3B7F] bg-[#0D3B7F] text-white shadow-sm'=> !$medicoSeleccionadoId,

                                'border-gray-200 bg-white text-gray-700 hover:border-gray-300'
                                => $medicoSeleccionadoId,
                                ])
                                >
                                <div>
                                    <p class="text-sm font-semibold">
                                        Todos
                                    </p>

                                    <p
                                        @class([ 'mt-1 text-xs' , 'text-blue-100'=> !$medicoSeleccionadoId,
                                        'text-gray-400' => $medicoSeleccionadoId,
                                        ])
                                        >
                                        Equipo médico
                                    </p>
                                </div>

                                <span
                                    @class([ 'flex h-8 min-w-8 items-center justify-center rounded-full px-2 text-xs font-bold' , 'bg-white/20 text-white'=> !$medicoSeleccionadoId,

                                    'bg-slate-100 text-slate-600'
                                    => $medicoSeleccionadoId,
                                    ])
                                    >
                                    {{ $medicosFiltro->sum('citas_fecha_count') }}
                                </span>
                            </a>

                            {{-- Médicos --}}
                            @foreach ($medicosFiltro as $medico)
                            @php
                            $estaSeleccionado =
                            (int) $medicoSeleccionadoId ===
                            (int) $medico->id;
                            @endphp

                            <a
                                href="{{ route('dashboard', [
                                        'fecha' => $fechaSeleccionada->format('Y-m-d'),
                                        'mes' => $mesCalendario->format('Y-m'),
                                        'medico_id' => $medico->id,
                                    ]) }}"
                                @class([ 'flex min-w-[220px] shrink-0 items-center justify-between gap-3 rounded-xl border px-4 py-3 transition' , 'border-[#0D3B7F] bg-[#0D3B7F] text-white shadow-sm'=> $estaSeleccionado,

                                'border-gray-200 bg-white text-gray-700 hover:border-[#0D3B7F]/40 hover:shadow-sm'
                                => !$estaSeleccionado,
                                ])
                                >
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold">
                                        Dr. {{ $medico->nombre }}
                                        {{ $medico->apellido_paterno }}
                                    </p>

                                    <p
                                        @class([ 'mt-1 truncate text-xs' , 'text-blue-100'=> $estaSeleccionado,
                                        'text-gray-400' => !$estaSeleccionado,
                                        ])
                                        >
                                        {{ $medico->especialidad ?: 'Sin especialidad' }}
                                    </p>
                                </div>

                                <span
                                    @class([ 'flex h-8 min-w-8 shrink-0 items-center justify-center rounded-full px-2 text-xs font-bold' , 'bg-white/20 text-white'=> $estaSeleccionado,

                                    'bg-slate-100 text-slate-600'
                                    => !$estaSeleccionado &&
                                    $medico->citas_fecha_count > 0,

                                    'bg-gray-50 text-gray-300'
                                    => !$estaSeleccionado &&
                                    $medico->citas_fecha_count === 0,
                                    ])
                                    >
                                    {{ $medico->citas_fecha_count }}
                                </span>
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Listado de citas --}}
                    <div class="divide-y divide-gray-100">
                        @forelse ($citasSeleccionadas as $cita)
                        @php
                        $estadoActual = $cita->estado_actual;

                        [$estadoClases, $puntoClases, $estadoTexto] = match ($estadoActual) {
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
                        'en_curso', 'en_consulta' => [
                        'bg-blue-50 text-blue-700',
                        'bg-blue-500',
                        'En consulta',
                        ],
                        'finalizada' => [
                        'bg-gray-100 text-gray-600',
                        'bg-gray-500',
                        'Finalizada',
                        ],
                        'cancelada' => [
                        'bg-red-50 text-red-700',
                        'bg-red-500',
                        'Cancelada',
                        ],
                        default => [
                        'bg-violet-50 text-violet-700',
                        'bg-violet-500',
                        'Programada',
                        ],
                        };

                        $nombrePaciente = trim(
                        ($cita->paciente?->nombre ?? '') . ' ' .
                        ($cita->paciente?->apellido ?? '')
                        );

                        $nombreMedico = trim(
                        ($cita->medico?->nombre ?? '') . ' ' .
                        ($cita->medico?->apellido_paterno ?? '')
                        );

                        $inicialPaciente = mb_strtoupper(
                        mb_substr(
                        $cita->paciente?->nombre ?? 'P',
                        0,
                        1
                        )
                        );
                        @endphp

                        <article class="group px-6 py-5 transition hover:bg-slate-50">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                {{-- Hora --}}
                                <div class="flex w-24 shrink-0 items-center gap-2 sm:block">
                                    <p class="text-base font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse(
                                            $cita->hora
                                        )->format('h:i A') }}
                                    </p>

                                    <p class="mt-1 text-xs font-semibold text-gray-500">
                                        hasta
                                        {{ $cita->hora_fin->format('h:i A') }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $cita->duracion_minutos ?? 15 }}
                                        minutos
                                    </p>
                                </div>

                                {{-- Información --}}
                                <div class="flex min-w-0 flex-1 items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                                        {{ $inicialPaciente }}
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="truncate font-semibold text-gray-900">
                                                {{ $nombrePaciente ?: 'Paciente no disponible' }}
                                            </h4>

                                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $estadoClases }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $puntoClases }}"></span>
                                                {{ $estadoTexto }}
                                            </span>
                                        </div>

                                        <p class="mt-1 truncate text-sm text-gray-500">
                                            {{ $cita->motivo ?: 'Sin motivo registrado' }}
                                        </p>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Dr. {{ $nombreMedico ?: 'No asignado' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Modalidad --}}
                                <div class="shrink-0">
                                    @if ($cita->modalidad === 'videoconsulta')
                                    <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700">
                                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                        Videoconsulta
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        Presencial
                                    </span>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('citas.show', $cita) }}"
                                    class="inline-flex shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-[#0D3B7F] hover:text-[#0D3B7F]">
                                    Ver
                                </a>
                            </div>
                        </article>
                        @empty
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <svg
                                    class="h-7 w-7"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M8 7V3m8 4V3M5 11h14M5
                                               5h14a2 2 0 012 2v12a2
                                               2 0 01-2 2H5a2 2 0
                                               01-2-2V7a2 2 0 012-2z" />
                                </svg>
                            </div>

                            <h4 class="mt-4 font-semibold text-gray-900">
                                No hay citas para esta selección
                            </h4>

                            <p class="mt-1 text-sm text-gray-500">
                                Selecciona otro día o cambia el filtro de médico.
                            </p>

                            @if ($medicoSeleccionadoId)
                            <a
                                href="{{ route('dashboard', [
                                            'fecha' => $fechaSeleccionada->format('Y-m-d'),
                                            'mes' => $mesCalendario->format('Y-m'),
                                        ]) }}"
                                class="mt-5 inline-flex rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
                                Mostrar todos
                            </a>
                            @endif
                        </div>
                        @endforelse
                    </div>
                </div>


                {{-- Agenda visual por horario y médico --}}
                <div
                    class="order-2 overflow-hidden rounded-2xl
           border border-gray-200 bg-white shadow-sm">
                    {{-- Encabezado --}}
                    <div
                        class="flex flex-col gap-3 border-b border-gray-100
               px-5 py-5 sm:flex-row sm:items-center
               sm:justify-between">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase
                       tracking-wider text-emerald-600">
                                Agenda por horario
                            </p>

                            <h3 class="mt-1 text-lg font-bold text-gray-900">
                                {{ $fechaSeleccionada
                    ->locale('es')
                    ->translatedFormat('l, d \d\e F') }}
                            </h3>
                        </div>

                        <span
                            class="w-fit rounded-full bg-slate-100
                   px-3 py-1 text-xs font-semibold
                   text-slate-600">
                            Bloques de 15 minutos
                        </span>
                    </div>

                    {{-- Cuadrícula de médicos y horarios --}}
                    <div class="max-h-[690px] overflow-auto">
                        @if ($medicosAgenda->isEmpty())
                        <div class="px-6 py-16 text-center">
                            <p class="font-semibold text-gray-900">
                                No hay médicos activos
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Registra o activa un médico para mostrar la agenda.
                            </p>
                        </div>
                        @else
                        <div
                            class="grid min-w-max"
                            style="grid-template-columns:
                       76px repeat(
                           {{ $medicosAgenda->count() }},
                           minmax(210px, 1fr)
                       );">
                            {{-- Encabezado de hora --}}
                            <div
                                class="sticky left-0 top-0 z-30 flex h-14
                           items-center justify-center border-b
                           border-r border-gray-200 bg-slate-100
                           text-xs font-bold text-gray-500">
                                Hora
                            </div>


                            {{-- Encabezados de médicos --}}
                            @foreach ($medicosAgenda as $medico)
                            <div
                                class="sticky top-0 z-20 flex h-14
               items-center justify-center border-b
               border-r border-gray-200 bg-slate-100
               px-3 text-center">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-gray-800">
                                        Dr. {{ $medico->nombre }}
                                        {{ $medico->apellido_paterno }}
                                    </p>

                                    <p class="mt-0.5 truncate text-[11px] text-gray-500">
                                        {{ $medico->especialidad
                    ?: 'Medicina general' }}
                                    </p>
                                </div>
                            </div>
                            @endforeach

                            {{-- Filas de horarios --}}
                            @foreach ($horasAgenda as $horaAgenda)
                            {{-- Columna de hora --}}
                            <div
                                class="sticky left-0 z-10 flex h-12
               items-center justify-center border-b
               border-r border-gray-100 bg-white
               text-xs font-semibold text-gray-500">
                                {{ \Carbon\Carbon::createFromFormat(
            'H:i',
            $horaAgenda
        )->format('h:i A') }}
                            </div>

                            {{-- Celdas de médicos para este horario --}}
                            @foreach ($medicosAgenda as $medico)
                            @php
                            $llaveAgenda =
                            $medico->id
                            . '|'
                            . $horaAgenda;

                            $bloqueAgenda =
                            $citasAgenda->get(
                            $llaveAgenda
                            );

                            $citaAgenda =
                            $bloqueAgenda['cita']
                            ?? null;

                            $esInicioAgenda =
                            $bloqueAgenda['es_inicio']
                            ?? false;

                            $esFinalAgenda =
                            $bloqueAgenda['es_final']
                            ?? false;

                            $colorCitaAgenda = match (
                            $citaAgenda?->estado_actual
                            ) {
                            'confirmada' =>
                            'border-emerald-500 bg-emerald-500 text-white',

                            'en_espera' =>
                            'border-amber-400 bg-amber-400 text-white',

                            'en_curso', 'en_consulta' =>
                            'border-blue-500 bg-blue-500 text-white',

                            'finalizada' =>
                            'border-slate-400 bg-slate-400 text-white',

                            'cancelada' =>
                            'border-red-200 bg-red-50 text-red-600',

                            default =>
                            'border-indigo-500 bg-indigo-500 text-white',
                            };

                            $bordesCitaAgenda =
                            $esInicioAgenda && $esFinalAgenda
                            ? 'rounded-md'
                            : (
                            $esInicioAgenda
                            ? 'rounded-t-md border-b-0'
                            : (
                            $esFinalAgenda
                            ? 'rounded-b-md border-t-0'
                            : 'rounded-none border-y-0'
                            )
                            );

                            $espaciadoCeldaAgenda =
                            $esInicioAgenda && $esFinalAgenda
                            ? 'p-1'
                            : (
                            $esInicioAgenda
                            ? 'px-1 pt-1'
                            : (
                            $esFinalAgenda
                            ? 'px-1 pb-1'
                            : 'px-1'
                            )
                            );

                            $pacienteAgenda = $citaAgenda
                            ? trim(
                            ($citaAgenda->paciente?->nombre ?? '')
                            . ' '
                            . ($citaAgenda->paciente?->apellido ?? '')
                            )
                            : null;
                            @endphp

                            <div
                                class="h-12 border-b border-r
                                  border-gray-100 bg-white
                                  {{ $espaciadoCeldaAgenda }}">
                                @if ($citaAgenda)
                                <a
                                    href="{{ route(
            'citas.show',
            $citaAgenda
        ) }}"
                                    title="{{ $pacienteAgenda
            ?: 'Paciente no disponible' }}
            · {{ $citaAgenda->duracion_minutos ?? 15 }}
            minutos"
                                    class="flex h-full items-center gap-2
               overflow-hidden border px-2
               text-xs font-semibold shadow-sm
               transition hover:brightness-95
               {{ $colorCitaAgenda }}
               {{ $bordesCitaAgenda }}">
                                    @if ($esInicioAgenda)
                                    <span
                                        class="shrink-0 rounded bg-white/20
                       px-1.5 py-0.5 text-[9px]
                       font-bold uppercase">
                                        {{ $citaAgenda->modalidad ===
                    'videoconsulta'
                    ? 'Video'
                    : 'Cita' }}
                                    </span>

                                    <span class="truncate">
                                        {{ $pacienteAgenda
                    ?: 'Paciente no disponible' }}

                                        ·

                                        {{ $citaAgenda->duracion_minutos
                    ?? 15 }} min
                                    </span>
                                    @else
                                    <span
                                        class="h-1.5 w-full rounded-full
                       bg-white/25"></span>
                                    @endif
                                </a>
                                @else
                                @php
                                $fechaHoraBloque =
                                \Carbon\Carbon::parse(
                                $fechaSeleccionada->format('Y-m-d')
                                . ' '
                                . $horaAgenda
                                );

                                $puedeCrearCita =
                                $fechaHoraBloque->gt(now());
                                @endphp

                                @if ($puedeCrearCita)
                                <button
                                    type="button"
                                    class="abrir-modal-cita group flex h-full
                   w-full items-center justify-center
                   rounded-md border border-transparent
                   text-gray-300 transition
                   hover:border-blue-200 hover:bg-blue-50
                   hover:text-[#0D3B7F]"
                                    data-medico-id="{{ $medico->id }}"
                                    data-medico-nombre="Dr. {{ $medico->nombre }}
                {{ $medico->apellido_paterno }}"
                                    data-fecha="{{ $fechaSeleccionada->format('Y-m-d') }}"
                                    data-hora="{{ $horaAgenda }}"
                                    title="Crear cita en este horario">
                                    <span
                                        class="text-lg opacity-0 transition
                       group-hover:opacity-100">
                                        +
                                    </span>
                                </button>
                                @endif
                                @endif
                            </div>
                            @endforeach
                            @endforeach

                        </div>
                        @endif
                    </div>
                </div>

                {{-- Calendario --}}
                <aside
                    class="order-1 h-fit rounded-2xl border
           border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">
                                Seleccionar fecha
                            </p>

                            <h3 class="mt-1 text-lg font-bold capitalize text-gray-900">
                                {{ $mesCalendario->locale('es')->translatedFormat('F Y') }}
                            </h3>
                        </div>

                        <div class="flex items-center gap-2">
                            <a
                                href="{{ route('dashboard', [
                                    'mes' => $mesAnterior->format('Y-m'),
                                    'fecha' => $mesAnterior->format('Y-m-d'),
                                    'medico_id' => $medicoSeleccionadoId,
                                ]) }}"
                                title="Mes anterior"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-900">
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>

                            <a
                                href="{{ route('dashboard', [
                                    'mes' => $mesSiguiente->format('Y-m'),
                                    'fecha' => $mesSiguiente->format('Y-m-d'),
                                    'medico_id' => $medicoSeleccionadoId,
                                ]) }}"
                                title="Mes siguiente"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-900">
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    @if (!$fechaSeleccionada->isToday())
                    <a
                        href="{{ route('dashboard', [
                                'medico_id' => $medicoSeleccionadoId,
                            ]) }}"
                        class="mt-4 inline-flex text-sm font-semibold text-[#0D3B7F] hover:text-[#082a5d]">
                        Regresar a hoy
                    </a>
                    @endif

                    <div class="mt-5 grid grid-cols-7 gap-1 text-center">
                        @foreach (['L', 'M', 'M', 'J', 'V', 'S', 'D'] as $nombreDia)
                        <div class="py-2 text-xs font-semibold text-gray-400">
                            {{ $nombreDia }}
                        </div>
                        @endforeach

                        @foreach ($diasCalendario as $dia)
                        @php
                        $fechaDia = $dia->format('Y-m-d');
                        $informacionDia = $citasPorDia->get($fechaDia);
                        $totalDia = $informacionDia['total'] ?? 0;

                        $esMesActual =
                        $dia->month === $mesCalendario->month &&
                        $dia->year === $mesCalendario->year;

                        $esSeleccionado = $dia->isSameDay($fechaSeleccionada);
                        $esHoy = $dia->isToday();
                        @endphp

                        <a
                            href="{{ route('dashboard', [
                                    'fecha' => $fechaDia,
                                    'mes' => $dia->format('Y-m'),
                                    'medico_id' => $medicoSeleccionadoId,
                                ]) }}"
                            title="{{ $totalDia }} {{ $totalDia === 1 ? 'cita' : 'citas' }}"
                            @class([ 'relative flex aspect-square flex-col items-center justify-center rounded-xl text-sm font-semibold transition' , 'bg-[#0D3B7F] text-white shadow-sm'=> $esSeleccionado,

                            'bg-blue-50 text-[#0D3B7F] ring-1 ring-inset ring-blue-200'
                            => $esHoy && !$esSeleccionado,

                            'text-gray-700 hover:bg-gray-100'
                            => $esMesActual &&
                            !$esSeleccionado &&
                            !$esHoy,

                            'text-gray-300 hover:bg-gray-50'
                            => !$esMesActual &&
                            !$esSeleccionado,
                            ])
                            >
                            <span>
                                {{ $dia->day }}
                            </span>

                            @if ($totalDia > 0)
                            <span
                                @class([ 'absolute bottom-1 h-1.5 w-1.5 rounded-full' , 'bg-white'=> $esSeleccionado,
                                'bg-emerald-500' => !$esSeleccionado,
                                ])
                                ></span>
                            @endif
                        </a>
                        @endforeach
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-4 border-t border-gray-100 pt-4 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Día con citas
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-[#0D3B7F]"></span>
                            Seleccionado
                        </span>
                    </div>

                    {{-- Próxima cita --}}
                    <div class="mt-5 rounded-xl bg-slate-50 p-4">
                        @if ($proximaCita)
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            @if ($fechaSeleccionada->isToday())
                            Próxima cita
                            @else
                            Primera cita activa
                            @endif
                        </p>

                        <div class="mt-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($proximaCita->hora)->format('h:i A') }}
                                </p>

                                <p class="mt-1 truncate text-sm text-gray-500">
                                    {{ trim(
                                            ($proximaCita->paciente?->nombre ?? '') . ' ' .
                                            ($proximaCita->paciente?->apellido ?? '')
                                        ) ?: 'Paciente no disponible' }}
                                </p>
                            </div>

                            <a
                                href="{{ route('citas.show', $proximaCita) }}"
                                class="shrink-0 rounded-lg bg-white px-3 py-2 text-xs font-semibold text-[#0D3B7F] shadow-sm transition hover:bg-[#0D3B7F] hover:text-white">
                                Ver
                            </a>
                        </div>
                        @else
                        <p class="text-sm font-semibold text-gray-900">
                            Sin citas activas
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            No hay consultas pendientes para esta selección.
                        </p>
                        @endif
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
                        class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-xl text-violet-600">
                            +
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
                        class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-xl text-emerald-600">
                            +
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900">
                                Programar cita
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Agendar una nueva consulta
                            </p>
                        </div>
                    </a>

                    <a
                        href="{{ route('pacientes.index') }}"
                        class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-xl text-blue-600">
                            ≡
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
    {{-- Modal para crear cita desde la agenda --}}
    <div
        id="modal-crear-cita"
        class="fixed inset-0 z-50 hidden
           items-center justify-center
           bg-gray-950/60 p-4 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-cita">
        <div
            class="flex max-h-[90vh] w-full max-w-4xl
               flex-col overflow-hidden rounded-2xl
               bg-white shadow-2xl">
            {{-- Encabezado --}}
            <div
                class="flex items-start justify-between gap-4
                   border-b border-gray-200 px-6 py-5">
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                           tracking-wider text-emerald-600">
                        Agenda de recepción
                    </p>

                    <h3
                        id="titulo-modal-cita"
                        class="mt-1 text-xl font-bold text-gray-900">
                        Crear nueva cita
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        <span id="modal-medico-texto"></span>
                        <span class="mx-2">·</span>
                        <span id="modal-fecha-texto"></span>
                        <span class="mx-2">·</span>
                        <span id="modal-hora-texto"></span>
                    </p>
                </div>

                <button
                    id="cerrar-modal-cita"
                    type="button"
                    class="flex h-10 w-10 shrink-0
                       items-center justify-center rounded-xl
                       border border-gray-200 text-xl
                       text-gray-500 transition
                       hover:bg-gray-100"
                    aria-label="Cerrar">
                    &times;
                </button>
            </div>

            {{-- Aquí colocaremos el formulario --}}
            {{-- Formulario reutilizable --}}
            <div class="overflow-y-auto p-6">
                @include('citas._form', [
                'medicos' => $medicosFiltro,

                'datosPrecargados' => [],
                ])
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal =
                document.getElementById(
                    'modal-crear-cita'
                );

            const botonCerrar =
                document.getElementById(
                    'cerrar-modal-cita'
                );

            const medicoTexto =
                document.getElementById(
                    'modal-medico-texto'
                );

            const fechaTexto =
                document.getElementById(
                    'modal-fecha-texto'
                );

            const horaTexto =
                document.getElementById(
                    'modal-hora-texto'
                );

            const botonesAbrir =
                document.querySelectorAll(
                    '.abrir-modal-cita'
                );


            const medicoFormulario =
                document.getElementById(
                    'medico_id'
                );

            const fechaFormulario =
                document.getElementById(
                    'fecha'
                );

            const horaFormulario =
                document.getElementById(
                    'hora'
                );

            const duracionFormulario =
                document.getElementById(
                    'duracion_minutos'
                );

            /**
             * Formatea la fecha sin modificarla
             * por diferencias de zona horaria.
             */
            function formatearFecha(fecha) {
                const fechaLocal =
                    new Date(
                        `${fecha}T00:00:00`
                    );

                return fechaLocal.toLocaleDateString(
                    'es-MX', {
                        weekday: 'long',
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                    }
                );
            }

            /**
             * Formatea una hora de 24 horas.
             */
            function formatearHora(hora) {
                const [horas, minutos] =
                hora
                    .split(':')
                    .map(Number);

                const fechaHora = new Date();

                fechaHora.setHours(
                    horas,
                    minutos,
                    0,
                    0
                );

                return fechaHora.toLocaleTimeString(
                    'es-MX', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true,
                    }
                );
            }

            function abrirModal(boton) {
                const medicoId =
                    boton.dataset.medicoId;

                const fechaSeleccionada =
                    boton.dataset.fecha;

                const horaSeleccionada =
                    boton.dataset.hora;

                /*
                 * Encabezado del modal.
                 */
                medicoTexto.textContent =
                    boton.dataset.medicoNombre;

                fechaTexto.textContent =
                    formatearFecha(
                        fechaSeleccionada
                    );

                horaTexto.textContent =
                    formatearHora(
                        horaSeleccionada
                    );

                /*
                 * Precargamos médico y fecha.
                 */
                medicoFormulario.value =
                    medicoId;

                fechaFormulario.value =
                    fechaSeleccionada;

                /*
                 * Al disparar change, el script reutilizable
                 * consulta los horarios disponibles.
                 */
                medicoFormulario.dispatchEvent(
                    new Event(
                        'change', {
                            bubbles: true,
                        }
                    )
                );

                /*
                 * El listener del formulario limpia primero
                 * el valor anterior. Por eso asignamos la hora
                 * después de disparar el evento.
                 */
                horaFormulario.dataset.valorAnterior =
                    horaSeleccionada;

                duracionFormulario.dataset.valorAnterior =
                    '15';

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                document.body.classList.add(
                    'overflow-hidden'
                );
            }

            function cerrarModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');

                document.body.classList.remove(
                    'overflow-hidden'
                );
            }

            botonesAbrir.forEach(boton => {
                boton.addEventListener(
                    'click',
                    () => abrirModal(boton)
                );
            });

            botonCerrar.addEventListener(
                'click',
                cerrarModal
            );

            /*
             * Cerrar al presionar el fondo oscuro.
             */
            modal.addEventListener(
                'click',
                event => {
                    if (event.target === modal) {
                        cerrarModal();
                    }
                }
            );

            /*
             * Cerrar con Escape.
             */
            document.addEventListener(
                'keydown',
                event => {
                    if (
                        event.key === 'Escape' &&
                        !modal.classList
                        .contains('hidden')
                    ) {
                        cerrarModal();
                    }
                }
            );
        });
    </script>
</x-app-layout>