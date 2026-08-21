<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Editar cita
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Modifica la información y el estado de la cita.
                </p>
            </div>

            <a
                href="{{ route('citas.index') }}"
                class="inline-flex items-center justify-center rounded-xl border
                       border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold
                       text-gray-700 transition hover:bg-gray-50">
                Volver a la agenda
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- Errores generales --}}
            @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex gap-3">
                    <svg
                        class="mt-0.5 h-5 w-5 flex-none text-red-500"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16ZM8.28
                                   7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72
                                   1.72a.75.75 0 101.06 1.06L10 11.06l1.72
                                   1.72a.75.75 0 101.06-1.06L11.06 10l1.72
                                   -1.72a.75.75 0 00-1.06-1.06L10 8.94
                                   8.28 7.22Z"
                            clip-rule="evenodd" />
                    </svg>

                    <div>
                        <p class="text-sm font-semibold text-red-800">
                            No se pudo actualizar la cita.
                        </p>

                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <form
                method="POST"
                action="{{ route('citas.update', $cita) }}"
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                @csrf
                @method('PUT')

                <div class="border-b border-gray-200 px-6 py-5 sm:px-8">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Información de la cita
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Los campos marcados con
                        <span class="text-red-500">*</span>
                        son obligatorios.
                    </p>
                </div>

                <div class="space-y-8 p-6 sm:p-8">

                    {{-- Paciente y médico --}}
                    <div class="grid gap-6 md:grid-cols-2">

                        {{-- Paciente --}}
                        <div>
                            <label
                                for="paciente_id"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Paciente <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="paciente_id"
                                name="paciente_id"
                                required
                                class="block w-full rounded-xl border-gray-300 bg-white
                                       text-gray-900 shadow-sm transition
                                       focus:border-[#0D3B7F] focus:ring-[#0D3B7F]
                                       @error('paciente_id') border-red-400 @enderror">
                                <option value="">Selecciona un paciente</option>

                                @foreach ($pacientes as $paciente)
                                <option
                                    value="{{ $paciente->id }}"
                                    @selected(
                                    old('paciente_id', $cita->paciente_id)
                                    == $paciente->id
                                    )
                                    >
                                    {{ trim($paciente->nombre . ' ' . $paciente->apellido) }}
                                </option>
                                @endforeach
                            </select>

                            @error('paciente_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Médico --}}
                        <div>
                            <label
                                for="medico_id"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Médico <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="medico_id"
                                name="medico_id"
                                required
                                class="block w-full rounded-xl border-gray-300 bg-white
                                       text-gray-900 shadow-sm transition
                                       focus:border-[#0D3B7F] focus:ring-[#0D3B7F]
                                       @error('medico_id') border-red-400 @enderror">
                                <option value="">Selecciona un médico</option>

                                @foreach ($medicos as $medico)
                                <option
                                    value="{{ $medico->id }}"
                                    @selected(
                                    old('medico_id', $cita->medico_id)
                                    == $medico->id
                                    )
                                    >
                                    Dr. {{ trim(
                                            $medico->nombre . ' ' .
                                            $medico->apellido_paterno . ' ' .
                                            $medico->apellido_materno
                                        ) }}

                                    @if ($medico->especialidad)
                                    — {{ $medico->especialidad }}
                                    @endif

                                    @if (! $medico->status)
                                    — Inactivo
                                    @endif
                                </option>
                                @endforeach
                            </select>

                            @error('medico_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Modalidad de consulta --}}
                    {{-- Tipo de atención --}}
                    <div class="md:col-span-2">
                        <fieldset>
                            <legend
                                class="mb-3 text-sm font-semibold text-gray-700">
                                Tipo de atención
                                <span class="text-red-500">*</span>
                            </legend>

                            @php
                            $modalidades = [
                            'presencial' => [
                            'titulo' =>
                            'Consultorio u oficina',

                            'descripcion' =>
                            'Atención presencial dentro de las instalaciones.',

                            'icono' => '🏥',
                            ],

                            'telefonica' => [
                            'titulo' =>
                            'Telefónica',

                            'descripcion' =>
                            'Consulta realizada mediante llamada telefónica.',

                            'icono' => '📞',
                            ],

                            'videoconsulta' => [
                            'titulo' =>
                            'Videollamada',

                            'descripcion' =>
                            'Se utilizará una sala de Google Meet.',

                            'icono' => '💻',
                            ],

                            'fuera_instalaciones' => [
                            'titulo' =>
                            'Fuera de las instalaciones',

                            'descripcion' =>
                            'Atención presencial fuera del consultorio.',

                            'icono' => '📍',
                            ],
                            ];

                            $modalidadSeleccionada = old(
                            'modalidad',
                            $cita->modalidad
                            );
                            @endphp

                            <div class="grid gap-4 sm:grid-cols-2">
                                @foreach ($modalidades as $valor => $modalidad)
                                <label
                                    class="cursor-pointer rounded-2xl
                           border border-gray-300 bg-white
                           p-4 transition
                           hover:border-[#0D3B7F]
                           hover:bg-blue-50">
                                    <div class="flex items-start gap-3">
                                        <input
                                            type="radio"
                                            name="modalidad"
                                            value="{{ $valor }}"
                                            required
                                            class="mt-1 text-[#0D3B7F]
                                   focus:ring-[#0D3B7F]"
                                            @checked(
                                            $modalidadSeleccionada===$valor
                                            )>

                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span>
                                                    {{ $modalidad['icono'] }}
                                                </span>

                                                <p class="font-semibold text-gray-900">
                                                    {{ $modalidad['titulo'] }}
                                                </p>
                                            </div>

                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $modalidad['descripcion'] }}
                                            </p>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>

                            @error('modalidad')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </fieldset>
                    </div>

                    {{-- Fecha, hora y estado --}}
                    <div class="grid gap-6 md:grid-cols-3">

                        {{-- Fecha --}}
                        <div>
                            <label
                                for="fecha"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Fecha <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="fecha"
                                type="date"
                                name="fecha"
                                required
                                value="{{ old(
                                    'fecha',
                                    \Carbon\Carbon::parse($cita->fecha)->format('Y-m-d')
                                ) }}"
                                class="block w-full rounded-xl border-gray-300
                                       text-gray-900 shadow-sm transition
                                       focus:border-[#0D3B7F] focus:ring-[#0D3B7F]
                                       @error('fecha') border-red-400 @enderror">

                            @error('fecha')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Hora --}}

                        <div>
                            <label
                                for="hora"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Hora
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="hora"
                                name="hora"
                                required
                                disabled
                                data-valor-anterior="{{ old(
            'hora',
            \Carbon\Carbon::parse(
                $cita->hora
            )->format('H:i')
        ) }}"
                                class="block w-full rounded-xl
               border-gray-300 bg-white
               text-gray-900 shadow-sm transition
               focus:border-[#0D3B7F]
               focus:ring-[#0D3B7F]
               disabled:cursor-not-allowed
               disabled:bg-gray-100
               @error('hora') border-red-400 @enderror">
                                <option value="">
                                    Consultando horarios disponibles...
                                </option>
                            </select>

                            <p
                                id="mensaje_horarios"
                                class="mt-2 text-sm text-gray-500">
                                El sistema mostrará bloques disponibles de 15 minutos.
                            </p>

                            @error('hora')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Hora de finalización --}}
                        {{-- Hora de finalización --}}
                        <div>
                            <label
                                for="duracion_minutos"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Finaliza la cita
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="duracion_minutos"
                                name="duracion_minutos"
                                required
                                disabled
                                data-valor-anterior="{{ old(
            'duracion_minutos',
            $cita->duracion_minutos ?? 15
        ) }}"
                                class="block w-full rounded-xl
               border-gray-300 bg-white
               text-gray-900 shadow-sm
               focus:border-[#0D3B7F]
               focus:ring-[#0D3B7F]
               disabled:cursor-not-allowed
               disabled:bg-gray-100
               @error('duracion_minutos')
                   border-red-400
               @enderror">
                                <option value="">
                                    Selecciona primero la hora de inicio
                                </option>
                            </select>

                            <p
                                id="mensaje_duracion"
                                class="mt-2 text-sm text-gray-500">
                                La duración puede ser de 15 minutos hasta 2 horas.
                            </p>

                            @error('duracion_minutos')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label
                                for="estado"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Estado <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="estado"
                                name="estado"
                                required
                                class="block w-full rounded-xl border-gray-300 bg-white
                                       text-gray-900 shadow-sm transition
                                       focus:border-[#0D3B7F] focus:ring-[#0D3B7F]
                                       @error('estado') border-red-400 @enderror">
                                @php
                                $estadoActual = old('estado', $cita->estado);
                                @endphp

                                <option
                                    value="programada"
                                    @selected($estadoActual==='programada' )>
                                    Programada
                                </option>

                                <option
                                    value="confirmada"
                                    @selected($estadoActual==='confirmada' )>
                                    Confirmada
                                </option>

                                <option
                                    value="en_espera"
                                    @selected($estadoActual==='en_espera' )>
                                    En espera
                                </option>

                                <option
                                    value="en_consulta"
                                    @selected($estadoActual==='en_consulta' )>
                                    En consulta
                                </option>

                                <option
                                    value="finalizada"
                                    @selected($estadoActual==='finalizada' )>
                                    Finalizada
                                </option>

                                <option
                                    value="cancelada"
                                    @selected($estadoActual==='cancelada' )>
                                    Cancelada
                                </option>
                            </select>

                            @error('estado')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Motivo de la cita --}}
                    @php
                    $motivosCita = [
                    'consulta_inicial' => [
                    'titulo' => 'Consulta inicial',
                    'descripcion' =>
                    'Primera valoración del paciente.',
                    ],

                    'consulta_subsecuente' => [
                    'titulo' => 'Consulta subsecuente',
                    'descripcion' =>
                    'Seguimiento de una consulta anterior.',
                    ],

                    'consulta_emergencia' => [
                    'titulo' => 'Consulta de emergencia',
                    'descripcion' =>
                    'Atención prioritaria o urgente.',
                    ],
                    ];

                    $motivoSeleccionado = old(
                    'motivo',
                    $cita->motivo
                    );

                    $tieneMotivoHistorico =
                    filled($cita->motivo)
                    && !array_key_exists(
                    $cita->motivo,
                    $motivosCita
                    );
                    @endphp

                    <div class="md:col-span-2">
                        <fieldset>
                            <legend
                                class="mb-3 text-sm font-semibold
                   text-gray-700">
                                Motivo de la cita
                                <span class="text-red-500">*</span>
                            </legend>

                            {{-- Motivo histórico --}}
                            @if ($tieneMotivoHistorico)
                            <label
                                class="mb-4 flex cursor-pointer items-start
               gap-3 rounded-2xl border
               border-amber-300 bg-amber-50 p-4
               transition hover:border-amber-400">
                                <input
                                    type="radio"
                                    name="motivo"
                                    value="{{ $cita->motivo }}"
                                    required
                                    class="mt-1 text-amber-600
                   focus:ring-amber-600"
                                    @checked(
                                    $motivoSeleccionado===$cita->motivo
                                )
                                >

                                <div>
                                    <p class="font-semibold text-amber-900">
                                        {{ $cita->motivo }}
                                    </p>

                                    <p class="mt-1 text-sm text-amber-700">
                                        Este motivo pertenece a un registro
                                        histórico. Puedes conservarlo o elegir
                                        una categoría actual.
                                    </p>
                                </div>
                            </label>
                            @endif

                            {{-- Motivos actuales --}}
                            <div class="grid gap-4 sm:grid-cols-3">
                                @foreach ($motivosCita as $valor => $motivoCita)
                                <label
                                    class="cursor-pointer rounded-2xl
                           border border-gray-300 bg-white
                           p-4 transition
                           hover:border-[#0D3B7F]
                           hover:bg-blue-50">
                                    <div class="flex items-start gap-3">
                                        <input
                                            type="radio"
                                            name="motivo"
                                            value="{{ $valor }}"
                                            required
                                            class="mt-1 text-[#0D3B7F]
                                   focus:ring-[#0D3B7F]"
                                            @checked(
                                            $motivoSeleccionado===$valor
                                            )>

                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900">
                                                {{ $motivoCita['titulo'] }}
                                            </p>

                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $motivoCita['descripcion'] }}
                                            </p>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>

                            @error('motivo')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </fieldset>
                    </div>
                    {{-- Notas --}}
                    <div>
                        <label
                            for="notas"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Notas adicionales
                        </label>

                        <textarea
                            id="notas"
                            name="notas"
                            rows="5"
                            maxlength="2000"
                            placeholder="Agrega indicaciones o información relevante..."
                            class="block w-full resize-y rounded-xl border-gray-300
                                   text-gray-900 shadow-sm transition
                                   placeholder:text-gray-400
                                   focus:border-[#0D3B7F] focus:ring-[#0D3B7F]
                                   @error('notas') border-red-400 @enderror">{{ old('notas', $cita->notas) }}</textarea>

                        <div class="mt-2 flex justify-between gap-4">
                            @error('notas')
                            <p class="text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @else
                            <p class="text-sm text-gray-500">
                                Este campo es opcional.
                            </p>
                            @enderror

                            <p class="text-sm text-gray-400">
                                Máximo 2000 caracteres
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex flex-col-reverse gap-3 border-t border-gray-200
                            bg-gray-50 px-6 py-5 sm:flex-row sm:justify-end sm:px-8">
                    <a
                        href="{{ route('citas.index') }}"
                        class="inline-flex items-center justify-center rounded-xl
                               border border-gray-300 bg-white px-5 py-2.5
                               text-sm font-semibold text-gray-700 transition
                               hover:bg-gray-100">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl
                               bg-[#0D3B7F] px-6 py-2.5 text-sm font-semibold
                               text-white shadow-sm transition hover:bg-[#082a5d]
                               focus:outline-none focus:ring-2 focus:ring-[#0D3B7F]
                               focus:ring-offset-2">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const medico =
            document.getElementById('medico_id');

        const fecha =
            document.getElementById('fecha');

        const hora =
            document.getElementById('hora');

        const duracion =
            document.getElementById(
                'duracion_minutos'
            );

        const mensajeHorarios =
            document.getElementById(
                'mensaje_horarios'
            );

        const mensajeDuracion =
            document.getElementById(
                'mensaje_duracion'
            );

        let bloquesHorarios = [];
        let solicitudHorarios;

        /**
         * Calcula y formatea la hora final.
         */
        function formatearHoraFinal(
            horaInicio,
            duracionMinutos
        ) {
            const [horas, minutos] =
                horaInicio
                    .split(':')
                    .map(Number);

            const fechaHora = new Date();

            fechaHora.setHours(
                horas,
                minutos + duracionMinutos,
                0,
                0
            );

            return fechaHora.toLocaleTimeString(
                'es-MX',
                {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true,
                }
            );
        }

        /**
         * Genera las duraciones que caben antes
         * del siguiente bloque ocupado.
         */
        function generarOpcionesDuracion() {
            duracion.innerHTML = '';

            if (
                !hora.value
                || bloquesHorarios.length === 0
            ) {
                duracion.disabled = true;

                duracion.innerHTML =
                    '<option value="">'
                    + 'Selecciona primero la hora de inicio'
                    + '</option>';

                mensajeDuracion.textContent =
                    'La duración puede ser de '
                    + '15 minutos hasta 2 horas.';

                return;
            }

            const indiceInicio =
                bloquesHorarios.findIndex(
                    bloque =>
                        bloque.hora === hora.value
                );

            if (indiceInicio === -1) {
                duracion.disabled = true;

                duracion.innerHTML =
                    '<option value="">'
                    + 'Horario de inicio no disponible'
                    + '</option>';

                return;
            }

            const valorAnterior =
                Number(
                    duracion.dataset.valorAnterior
                    || 15
                );

            const duracionesPermitidas = [
                15,
                30,
                45,
                60,
                75,
                90,
                105,
                120,
            ];

            let primeraOpcion = null;

            duracionesPermitidas.forEach(
                duracionMinutos => {
                    const bloquesNecesarios =
                        duracionMinutos / 15;

                    const bloquesRequeridos =
                        bloquesHorarios.slice(
                            indiceInicio,
                            indiceInicio
                                + bloquesNecesarios
                        );

                    const estaDisponible =
                        bloquesRequeridos.length
                            === bloquesNecesarios
                        && bloquesRequeridos.every(
                            bloque =>
                                bloque.disponible
                        );

                    if (!estaDisponible) {
                        return;
                    }

                    const opcion =
                        document.createElement(
                            'option'
                        );

                    opcion.value =
                        String(duracionMinutos);

                    const horaFinal =
                        formatearHoraFinal(
                            hora.value,
                            duracionMinutos
                        );

                    opcion.textContent =
                        `${horaFinal} — `
                        + `${duracionMinutos} minutos`;

                    if (
                        duracionMinutos
                        === valorAnterior
                    ) {
                        opcion.selected = true;
                    }

                    primeraOpcion ??= opcion;

                    duracion.appendChild(opcion);
                }
            );

            if (duracion.options.length === 0) {
                duracion.disabled = true;

                duracion.innerHTML =
                    '<option value="">'
                    + 'No hay una duración disponible'
                    + '</option>';

                mensajeDuracion.textContent =
                    'El siguiente horario se encuentra ocupado.';

                return;
            }

            /*
             * Si la duración guardada dejó de estar
             * disponible, seleccionamos la primera.
             */
            if (
                !duracion.value
                && primeraOpcion
            ) {
                primeraOpcion.selected = true;
            }

            duracion.disabled = false;

            mensajeDuracion.textContent =
                'La cita terminará a las '
                + formatearHoraFinal(
                    hora.value,
                    Number(duracion.value)
                )
                + '.';
        }

        /**
         * Consulta los bloques disponibles.
         */
        async function cargarHorarios() {
            if (
                !medico.value
                || !fecha.value
            ) {
                hora.disabled = true;

                hora.innerHTML =
                    '<option value="">'
                    + 'Selecciona primero médico y fecha'
                    + '</option>';

                mensajeHorarios.textContent =
                    'Selecciona un médico y una fecha.';

                bloquesHorarios = [];

                generarOpcionesDuracion();

                return;
            }

            solicitudHorarios?.abort();

            solicitudHorarios =
                new AbortController();

            hora.disabled = true;

            hora.innerHTML =
                '<option value="">'
                + 'Consultando horarios...'
                + '</option>';

            mensajeHorarios.textContent =
                'Consultando la agenda del médico...';

            try {
                const url = new URL(
                    "{{ route('citas.horarios-disponibles', [], false) }}",
                    window.location.origin
                );

                url.searchParams.set(
                    'medico_id',
                    medico.value
                );

                url.searchParams.set(
                    'fecha',
                    fecha.value
                );

                /*
                 * Ignoramos la propia cita para que
                 * conserve disponibles sus bloques.
                 */
                url.searchParams.set(
                    'ignorar_cita',
                    "{{ $cita->id }}"
                );

                const respuesta =
                    await fetch(
                        url,
                        {
                            headers: {
                                Accept:
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            signal:
                                solicitudHorarios.signal,
                        }
                    );

                if (!respuesta.ok) {
                    throw new Error(
                        'No se pudieron consultar '
                        + 'los horarios.'
                    );
                }

                const datos =
                    await respuesta.json();

                bloquesHorarios =
                    datos.horarios;

                const horariosDisponibles =
                    bloquesHorarios.filter(
                        bloque =>
                            bloque.disponible
                    );

                hora.innerHTML = '';

                if (
                    horariosDisponibles.length === 0
                ) {
                    hora.innerHTML =
                        '<option value="">'
                        + 'No hay horarios disponibles'
                        + '</option>';

                    mensajeHorarios.textContent =
                        'La agenda del médico está '
                        + 'llena para esta fecha.';

                    generarOpcionesDuracion();

                    return;
                }

                const valorAnterior =
                    hora.dataset.valorAnterior;

                const opcionInicial =
                    document.createElement(
                        'option'
                    );

                opcionInicial.value = '';

                opcionInicial.textContent =
                    'Selecciona un horario';

                hora.appendChild(
                    opcionInicial
                );

                horariosDisponibles.forEach(
                    bloque => {
                        const opcion =
                            document.createElement(
                                'option'
                            );

                        opcion.value =
                            bloque.hora;

                        opcion.textContent =
                            bloque.texto;

                        if (
                            valorAnterior ===
                            bloque.hora
                        ) {
                            opcion.selected = true;
                        }

                        hora.appendChild(
                            opcion
                        );
                    }
                );

                hora.disabled = false;

                const opcionSeleccionada =
                    hora.options[
                        hora.selectedIndex
                    ]?.text;

                mensajeHorarios.textContent =
                    opcionSeleccionada
                        ? 'Horario seleccionado: '
                            + opcionSeleccionada
                            + '.'
                        : 'Selecciona uno de los '
                            + 'horarios disponibles.';

                generarOpcionesDuracion();
            } catch (error) {
                if (
                    error.name === 'AbortError'
                ) {
                    return;
                }

                bloquesHorarios = [];

                hora.disabled = true;

                hora.innerHTML =
                    '<option value="">'
                    + 'No se pudieron cargar los horarios'
                    + '</option>';

                mensajeHorarios.textContent =
                    'Ocurrió un error al consultar '
                    + 'la disponibilidad.';

                generarOpcionesDuracion();
            }
        }

        hora.addEventListener(
            'change',
            () => {
                hora.dataset.valorAnterior =
                    hora.value;

                generarOpcionesDuracion();
            }
        );

        duracion.addEventListener(
            'change',
            () => {
                duracion.dataset.valorAnterior =
                    duracion.value;

                if (
                    !hora.value
                    || !duracion.value
                ) {
                    return;
                }

                mensajeDuracion.textContent =
                    'La cita terminará a las '
                    + formatearHoraFinal(
                        hora.value,
                        Number(duracion.value)
                    )
                    + '.';
            }
        );

        medico.addEventListener(
            'change',
            cargarHorarios
        );

        fecha.addEventListener(
            'change',
            cargarHorarios
        );

        cargarHorarios();
    });
</script>
</x-app-layout>