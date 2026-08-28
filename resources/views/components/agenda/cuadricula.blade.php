@props([
'medicosAgenda',
'horasAgenda',
'citasAgenda',
'fechaSeleccionada',
'permitirCreacion' => false,
'mostrarNotas' => false,
])

<div
    x-data
    x-init="
        $nextTick(() => {
            $refs.espaciador.style.width =
                `${$refs.contenido.scrollWidth}px`;
        })
    "
    @resize.window="
        $refs.espaciador.style.width =
            `${$refs.contenido.scrollWidth}px`
    ">
    {{-- Barra horizontal superior --}}
    <div
        x-ref="superior"
        @scroll.passive="
            $refs.contenido.scrollLeft =
                $el.scrollLeft
        "
        class="overflow-x-auto overflow-y-hidden
               border-b border-gray-100 bg-slate-50"
        role="region"
        aria-label="Desplazamiento horizontal de la agenda"
        tabindex="0">
        <div
            x-ref="espaciador"
            class="h-1"></div>
    </div>

    {{-- Contenido de la agenda --}}
    <div
        x-ref="contenido"
        @scroll.passive="
            $refs.superior.scrollLeft =
                $el.scrollLeft
        "
        class="max-h-[690px] overflow-x-hidden overflow-y-auto">
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
            class="grid w-full"
            style="
        min-width:
            {{ 76 + ($medicosAgenda->count() * 210) }}px;

        grid-template-columns:
            76px repeat(
                {{ $medicosAgenda->count() }},
                minmax(210px, 1fr)
            );
    ">
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
                    <p
                        class="truncate text-sm font-bold
                                       text-gray-800">
                        Dr. {{ $medico->nombre }}
                        {{ $medico->apellido_paterno }}
                    </p>

                    <p
                        class="mt-0.5 truncate text-[11px]
                                       text-gray-500">
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

            {{-- Celdas de médicos --}}
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

            $espaciadoCeldaAgenda =
            $esInicioAgenda
            && $esFinalAgenda
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
            @endphp

            <div
                class="h-12 border-b border-r
                                   border-gray-100 bg-white
                                   {{ $espaciadoCeldaAgenda }}">
                @if ($citaAgenda)
                <x-agenda.tarjeta-cita
                    :cita="$citaAgenda"
                    :es-inicio="$esInicioAgenda"
                    :es-final="$esFinalAgenda"
                    :mostrar-notas="$mostrarNotas" />
                @else
                @php
                $fechaHoraBloque =
                \Carbon\Carbon::parse(
                $fechaSeleccionada
                ->format('Y-m-d')
                . ' '
                . $horaAgenda
                );

                $puedeCrearCita =
                $fechaHoraBloque
                ->gt(now());
                @endphp

                @if (
                $permitirCreacion
                && $puedeCrearCita
                )
                <button
                    type="button"
                    class="abrir-modal-cita group
                                               flex h-full w-full
                                               items-center justify-center
                                               rounded-md border
                                               border-transparent
                                               text-gray-300 transition
                                               hover:border-blue-200
                                               hover:bg-blue-50
                                               hover:text-[#0D3B7F]"
                    data-medico-id="{{ $medico->id }}"
                    data-medico-nombre="Dr. {{ $medico->nombre }} {{ $medico->apellido_paterno }}"
                    data-fecha="{{ $fechaSeleccionada->format('Y-m-d') }}"
                    data-hora="{{ $horaAgenda }}"
                    title="Crear cita en este horario">
                    <span
                        class="text-lg opacity-0
                                                   transition
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