    {{-- ========================================================= --}}
    {{-- MODAL PARA BLOQUEAR LA AGENDA DE UN MÉDICO --}}
    {{-- ========================================================= --}}

    @php
    $debeReabrirModalBloqueo =
    $errors->getBag('bloquearAgenda')->any();
    @endphp

    <div
        id="modal-bloquear-agenda"
        data-url-horarios="{{ route('citas.horarios-disponibles') }}"
        class="fixed inset-0 z-50 hidden items-center justify-center
            bg-slate-950/60 p-4 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-bloqueo">

        <div
            class="flex max-h-[92vh] w-full max-w-2xl flex-col
                overflow-hidden rounded-2xl border border-slate-200
                bg-white shadow-2xl">

            {{-- Encabezado --}}
            <div
                class="flex items-start justify-between gap-4
                    border-b border-slate-200 px-6 py-5">
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                            tracking-wider text-amber-600">
                        Agenda de recepción
                    </p>

                    <h3
                        id="titulo-modal-bloqueo"
                        class="mt-1 text-xl font-bold text-slate-900">
                        Bloquear agenda
                    </h3>

                    <p
                        id="descripcion-modal-bloqueo"
                        class="mt-2 text-sm text-slate-500">
                        Reserva un periodo en el que el médico
                        no podrá recibir citas.
                    </p>
                </div>

                <button
                    type="button"
                    data-cerrar-modal-bloqueo
                    class="flex h-10 w-10 shrink-0 items-center
                        justify-center rounded-xl border border-slate-200
                        text-xl text-slate-500 transition
                        hover:bg-slate-100"
                    aria-label="Cerrar">
                    &times;
                </button>
            </div>

            {{-- Contenido desplazable --}}
            <div class="overflow-y-auto">
                <form
                    id="formulario-bloqueo-agenda"
                    method="POST"
                    action="{{ route('agenda-bloqueos.store') }}"
                    data-store-url="{{ route('agenda-bloqueos.store') }}">
                    @csrf

                    <input
                        id="metodo-formulario-bloqueo"
                        type="hidden"
                        name="_method"
                        value="PUT"
                        disabled>

                    <input
                        id="bloqueo-editando-id"
                        type="hidden"
                        name="bloqueo_editando_id"
                        value="{{ old('bloqueo_editando_id') }}"
                        disabled>

                    <div class="space-y-6 p-6">

                        {{-- Errores del formulario --}}
                        @if ($errors->getBag('bloquearAgenda')->any())
                        <div
                            class="rounded-xl border border-red-200
                                    bg-red-50 p-4">
                            <p class="text-sm font-semibold text-red-800">
                                No se pudo bloquear la agenda.
                            </p>

                            <ul
                                class="mt-2 list-inside list-disc
                                        space-y-1 text-sm text-red-700">
                                @foreach (
                                $errors
                                ->getBag('bloquearAgenda')
                                ->all() as $error
                                )
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Médico --}}
                        <div>
                            <label
                                for="bloqueo-medico-id"
                                class="mb-2 block text-sm font-semibold
                                    text-slate-700">
                                Médico
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="bloqueo-medico-id"
                                name="medico_id"
                                required
                                class="block w-full rounded-xl border-slate-300
                                    bg-white text-slate-900 shadow-sm
                                    focus:border-[#0D3B7F]
                                    focus:ring-[#0D3B7F]">
                                <option value="">
                                    Selecciona un médico
                                </option>

                                @foreach ($medicosFiltro as $medico)
                                <option
                                    value="{{ $medico->id }}"
                                    @selected(
                                    old( 'medico_id' ,
                                    $medicoSeleccionadoId
                                    )==$medico->id
                                    )>
                                    Dr. {{ trim(
                                            $medico->nombre.' '.
                                            $medico->apellido_paterno.' '.
                                            $medico->apellido_materno
                                        ) }}

                                    @if ($medico->especialidad)
                                    — {{ $medico->especialidad }}
                                    @endif
                                </option>
                                @endforeach
                            </select>

                            @error('medico_id', 'bloquearAgenda')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Fecha --}}
                        <div>
                            <label
                                for="bloqueo-fecha"
                                class="mb-2 block text-sm font-semibold
                                    text-slate-700">
                                Fecha
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="bloqueo-fecha"
                                type="date"
                                name="fecha"
                                required
                                min="{{ now()->toDateString() }}"
                                value="{{ old(
                                    'fecha',
                                    $fechaSeleccionada->toDateString()
                                ) }}"
                                class="block w-full rounded-xl border-slate-300
                                    text-slate-900 shadow-sm
                                    focus:border-[#0D3B7F]
                                    focus:ring-[#0D3B7F]">

                            @error('fecha', 'bloquearAgenda')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Hora inicial y final --}}
                        <div class="grid gap-5 sm:grid-cols-2">

                            {{-- Hora inicial --}}
                            <div>
                                <label
                                    for="bloqueo-hora-inicio"
                                    class="mb-2 block text-sm font-semibold
                                        text-slate-700">
                                    Comienza
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="bloqueo-hora-inicio"
                                    name="hora_inicio"
                                    required
                                    disabled
                                    data-valor-anterior="{{ old('hora_inicio') }}"
                                    class="block w-full rounded-xl
                                        border-slate-300 bg-white
                                        text-slate-900 shadow-sm
                                        focus:border-[#0D3B7F]
                                        focus:ring-[#0D3B7F]">
                                    <option value="">
                                        Selecciona una hora
                                    </option>
                                </select>
                                <p
                                    id="mensaje-disponibilidad-bloqueo"
                                    class="mt-2 text-xs text-slate-500">
                                    Selecciona un médico y una fecha para consultar
                                    sus espacios disponibles.
                                </p>

                                @error('hora_inicio', 'bloquearAgenda')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Hora final --}}
                            <div>
                                <label
                                    for="bloqueo-hora-fin"
                                    class="mb-2 block text-sm font-semibold
                                        text-slate-700">
                                    Finaliza
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="bloqueo-hora-fin"
                                    name="hora_fin"
                                    required
                                    disabled
                                    data-valor-anterior="{{ old('hora_fin') }}"
                                    class="block w-full rounded-xl
                                        border-slate-300 bg-white
                                        text-slate-900 shadow-sm
                                        focus:border-[#0D3B7F]
                                        focus:ring-[#0D3B7F]">
                                    <option value="">
                                        Selecciona una hora
                                    </option>


                                </select>

                                @error('hora_fin', 'bloquearAgenda')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>

                        <p
                            id="mensaje-periodo-bloqueo"
                            class="rounded-xl bg-amber-50 px-4 py-3
                                text-sm text-amber-800">
                            Selecciona el periodo que deseas bloquear.
                        </p>

                        {{-- Motivo --}}
                        <div>
                            <label
                                for="bloqueo-motivo"
                                class="mb-2 block text-sm font-semibold
                                    text-slate-700">
                                Motivo del bloqueo
                                <span class="text-red-500">*</span>
                            </label>

                            <textarea
                                id="bloqueo-motivo"
                                name="motivo"
                                rows="4"
                                maxlength="500"
                                required
                                placeholder="Ej. Procedimiento externo, reunión o ausencia médica."
                                class="block w-full resize-y rounded-xl
                                    border-slate-300 text-slate-900
                                    shadow-sm placeholder:text-slate-400
                                    focus:border-[#0D3B7F]
                                    focus:ring-[#0D3B7F]">{{ old('motivo') }}</textarea>

                            <div
                                class="mt-2 flex items-center
                                    justify-between gap-4">
                                @error('motivo', 'bloquearAgenda')
                                <p class="text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @else
                                <p class="text-xs text-slate-500">
                                    Este motivo será visible para recepción.
                                </p>
                                @enderror

                                <p class="text-xs text-slate-400">
                                    Máximo 500 caracteres
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div
                        class="flex flex-col-reverse gap-3 border-t
                            border-slate-200 bg-slate-50 px-6 py-5
                            sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            data-cerrar-modal-bloqueo
                            class="inline-flex items-center justify-center
                                rounded-xl border border-slate-300
                                bg-white px-5 py-2.5 text-sm
                                font-semibold text-slate-700
                                transition hover:bg-slate-100">
                            Cancelar
                        </button>

                        <button
                            id="guardar-bloqueo-agenda"
                            type="submit"
                            class="inline-flex items-center justify-center
                                rounded-xl bg-amber-500 px-6 py-2.5
                                text-sm font-semibold text-white
                                shadow-sm transition hover:bg-amber-600
                                focus:outline-none focus:ring-2
                                focus:ring-amber-500 focus:ring-offset-2">
                            Bloquear agenda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- COMPORTAMIENTO DEL MODAL --}}
    {{-- ========================================================= --}}

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById(
                'modal-bloquear-agenda'
            );

            const formulario = document.getElementById(
                'formulario-bloqueo-agenda'
            );

            const metodoFormulario = document.getElementById(
                'metodo-formulario-bloqueo'
            );

            const bloqueoEditandoCampo =
    document.getElementById(
        'bloqueo-editando-id'
    );

            const tituloModal = document.getElementById(
                'titulo-modal-bloqueo'
            );

            const descripcionModal = document.getElementById(
                'descripcion-modal-bloqueo'
            );

            const botonGuardar = document.getElementById(
                'guardar-bloqueo-agenda'
            );

            const motivo = document.getElementById(
                'bloqueo-motivo'
            );

            const botonesEditar = document.querySelectorAll(
                '.editar-bloqueo-agenda'
            );

            let bloqueoEditandoId = null;

            const botonAbrir = document.getElementById(
                'abrir-modal-bloqueo'
            );

            const medico = document.getElementById(
                'bloqueo-medico-id'
            );

            const fecha = document.getElementById(
                'bloqueo-fecha'
            );

            const mensajeDisponibilidad =
                document.getElementById(
                    'mensaje-disponibilidad-bloqueo'
                );

            const botonesCerrar = document.querySelectorAll(
                '[data-cerrar-modal-bloqueo]'
            );

            const horaInicio = document.getElementById(
                'bloqueo-hora-inicio'
            );

            const horaFin = document.getElementById(
                'bloqueo-hora-fin'
            );

            const mensajePeriodo = document.getElementById(
                'mensaje-periodo-bloqueo'
            );

            let bloquesHorarios = [];
            let solicitudHorarios = null;

            function convertirAMinutos(hora) {
                if (!hora) {
                    return null;
                }

                const [horas, minutos] = hora
                    .split(':')
                    .map(Number);

                return (horas * 60) + minutos;
            }

            function formatearHoraDesdeMinutos(
                minutosTotales
            ) {
                const horas24 = Math.floor(
                    minutosTotales / 60
                );

                const minutos = minutosTotales % 60;
                const periodo = horas24 >= 12 ?
                    'PM' :
                    'AM';

                const horas12 = horas24 % 12 || 12;

                return (
                    String(horas12).padStart(2, '0') +
                    ':' +
                    String(minutos).padStart(2, '0') +
                    ' ' +
                    periodo
                );
            }

            function convertirMinutosAHora(
                minutosTotales
            ) {
                const horas = Math.floor(
                    minutosTotales / 60
                );

                const minutos = minutosTotales % 60;

                return (
                    String(horas).padStart(2, '0') +
                    ':' +
                    String(minutos).padStart(2, '0')
                );
            }

            /**
             * Genera las horas finales consecutivas.
             * Se detiene al encontrar una cita o bloqueo.
             */
            function actualizarHorasFinales() {
                horaFin.innerHTML =
                    '<option value="">' +
                    'Selecciona primero la hora inicial' +
                    '</option>';

                if (
                    !horaInicio.value ||
                    bloquesHorarios.length === 0
                ) {
                    horaFin.disabled = true;

                    mensajePeriodo.textContent =
                        'Selecciona primero la hora de inicio.';

                    return;
                }

                const indiceInicio =
                    bloquesHorarios.findIndex(
                        bloque =>
                        bloque.hora === horaInicio.value
                    );

                if (indiceInicio === -1) {
                    horaFin.disabled = true;

                    mensajePeriodo.textContent =
                        'El horario inicial ya no está disponible.';

                    return;
                }

                const valorAnterior =
                    horaFin.dataset.valorAnterior || '';

                for (
                    let indice = indiceInicio; indice < bloquesHorarios.length; indice++
                ) {
                    const bloque = bloquesHorarios[indice];

                    /*
                     * No permitimos extender el bloqueo
                     * después del primer espacio ocupado.
                     */
                    if (!bloque.disponible) {
                        break;
                    }

                    const minutosFinales =
                        convertirAMinutos(bloque.hora) + 15;

                    const opcion =
                        document.createElement('option');

                    opcion.value =
                        convertirMinutosAHora(
                            minutosFinales
                        );

                    opcion.textContent =
                        formatearHoraDesdeMinutos(
                            minutosFinales
                        );

                    if (
                        opcion.value === valorAnterior
                    ) {
                        opcion.selected = true;
                    }

                    horaFin.appendChild(opcion);
                }

                if (horaFin.options.length === 1) {
                    horaFin.disabled = true;

                    mensajePeriodo.textContent =
                        'No existe un periodo disponible ' +
                        'desde esta hora.';

                    return;
                }

                if (!horaFin.value) {
                    horaFin.options[1].selected = true;
                }

                horaFin.disabled = false;

                actualizarMensajePeriodo();
            }

            function actualizarMensajePeriodo() {
                if (
                    !horaInicio.value ||
                    !horaFin.value
                ) {
                    mensajePeriodo.textContent =
                        'Selecciona el periodo que deseas bloquear.';

                    return;
                }

                const duracion =
                    convertirAMinutos(horaFin.value) -
                    convertirAMinutos(horaInicio.value);

                mensajePeriodo.textContent =
                    `La agenda quedará bloqueada durante ` +
                    `${duracion} minutos, desde ` +
                    `${horaInicio.options[
                        horaInicio.selectedIndex
                    ].text.trim()} hasta ` +
                    `${horaFin.options[
                        horaFin.selectedIndex
                    ].text.trim()}.`;
            }

            /**
             * Consulta las citas y bloqueos existentes
             * del médico en la fecha seleccionada.
             */
            async function cargarHorariosDisponibles() {
                if (
                    !medico.value ||
                    !fecha.value
                ) {
                    bloquesHorarios = [];

                    horaInicio.disabled = true;
                    horaFin.disabled = true;

                    horaInicio.innerHTML =
                        '<option value="">' +
                        'Selecciona médico y fecha' +
                        '</option>';

                    horaFin.innerHTML =
                        '<option value="">' +
                        'Selecciona primero la hora inicial' +
                        '</option>';

                    mensajeDisponibilidad.textContent =
                        'Selecciona un médico y una fecha ' +
                        'para consultar sus espacios disponibles.';

                    return;
                }

                solicitudHorarios?.abort();

                solicitudHorarios =
                    new AbortController();

                horaInicio.disabled = true;
                horaFin.disabled = true;

                horaInicio.innerHTML =
                    '<option value="">' +
                    'Consultando horarios...' +
                    '</option>';

                mensajeDisponibilidad.textContent =
                    'Consultando la agenda del médico...';

                try {
                    const url = new URL(
                        modal.dataset.urlHorarios,
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

                    if (bloqueoEditandoId) {
                        url.searchParams.set(
                            'ignorar_bloqueo',
                            bloqueoEditandoId
                        );
                    }

                    const respuesta = await fetch(
                        url, {
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            signal: solicitudHorarios.signal,
                        }
                    );

                    if (!respuesta.ok) {
                        throw new Error(
                            'No se pudieron consultar los horarios.'
                        );
                    }

                    const datos = await respuesta.json();

                    bloquesHorarios =
                        datos.horarios || [];

                    const horariosDisponibles =
                        bloquesHorarios.filter(
                            bloque => bloque.disponible
                        );

                    horaInicio.innerHTML =
                        '<option value="">' +
                        'Selecciona una hora' +
                        '</option>';

                    if (
                        horariosDisponibles.length === 0
                    ) {
                        horaInicio.disabled = true;

                        horaInicio.innerHTML =
                            '<option value="">' +
                            'No hay horarios disponibles' +
                            '</option>';

                        mensajeDisponibilidad.textContent =
                            'La agenda del médico no tiene ' +
                            'espacios libres en esta fecha.';

                        actualizarHorasFinales();

                        return;
                    }

                    const valorAnterior =
                        horaInicio.dataset.valorAnterior || '';

                    horariosDisponibles.forEach(
                        bloque => {
                            const opcion =
                                document.createElement(
                                    'option'
                                );

                            opcion.value = bloque.hora;
                            opcion.textContent =
                                bloque.texto;

                            if (
                                bloque.hora === valorAnterior
                            ) {
                                opcion.selected = true;
                            }

                            horaInicio.appendChild(opcion);
                        }
                    );

                    horaInicio.disabled = false;

                    mensajeDisponibilidad.textContent =
                        `${horariosDisponibles.length} ` +
                        'intervalos de 15 minutos disponibles.';

                    actualizarHorasFinales();
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    bloquesHorarios = [];

                    horaInicio.disabled = true;
                    horaFin.disabled = true;

                    horaInicio.innerHTML =
                        '<option value="">' +
                        'No se pudieron cargar los horarios' +
                        '</option>';

                    mensajeDisponibilidad.textContent =
                        'Ocurrió un error al consultar la agenda.';

                    actualizarHorasFinales();
                }
            }

           /**
 * Muestra el modal y consulta disponibilidad.
 */
function abrirModal() {
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.body.classList.add(
        'overflow-hidden'
    );

    cargarHorariosDisponibles();
}

/**
 * Configura el formulario para crear.
 */
function abrirModalCreacion() {
    bloqueoEditandoId = null;

    formulario.action =
        formulario.dataset.storeUrl;

    metodoFormulario.disabled = true;

    bloqueoEditandoCampo.disabled = true;
    bloqueoEditandoCampo.value = '';

    tituloModal.textContent =
        'Bloquear agenda';

    descripcionModal.textContent =
        'Reserva un periodo en el que el médico ' +
        'no podrá recibir citas.';

    botonGuardar.textContent =
        'Bloquear agenda';

    horaInicio.dataset.valorAnterior = '';
    horaFin.dataset.valorAnterior = '';

    horaInicio.value = '';
    horaFin.value = '';
    motivo.value = '';

    abrirModal();
}

/**
 * Configura el formulario para editar.
 */
function abrirModalEdicion(boton) {
    bloqueoEditandoId =
        boton.dataset.bloqueoId;

    formulario.action =
        boton.dataset.updateUrl;

    metodoFormulario.disabled = false;

    bloqueoEditandoCampo.disabled = false;
    bloqueoEditandoCampo.value =
        bloqueoEditandoId;

    tituloModal.textContent =
        'Editar bloqueo de agenda';

    descripcionModal.textContent =
        'Modifica el periodo respetando las citas ' +
        'y los demás bloqueos existentes.';

    botonGuardar.textContent =
        'Guardar cambios';

    medico.value =
        boton.dataset.medicoId;

    fecha.value =
        boton.dataset.fecha;

    horaInicio.dataset.valorAnterior =
        boton.dataset.horaInicio;

    horaFin.dataset.valorAnterior =
        boton.dataset.horaFin;

    motivo.value =
        boton.dataset.motivo;

    abrirModal();
}

            function cerrarModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');

                document.body.classList.remove(
                    'overflow-hidden'
                );
            }

           botonAbrir?.addEventListener(
    'click',
    abrirModalCreacion
);

botonesEditar.forEach(boton => {
    boton.addEventListener(
        'click',
        () => abrirModalEdicion(boton)
    );
});

            botonesCerrar.forEach(boton => {
                boton.addEventListener(
                    'click',
                    cerrarModal
                );
            });

            horaInicio.addEventListener(
                'change',
                () => {
                    horaInicio.dataset.valorAnterior =
                        horaInicio.value;

                    horaFin.dataset.valorAnterior = '';

                    actualizarHorasFinales();
                }
            );

            horaFin.addEventListener(
                'change',
                () => {
                    horaFin.dataset.valorAnterior =
                        horaFin.value;

                    actualizarMensajePeriodo();
                }
            );

            medico.addEventListener(
                'change',
                () => {
                    horaInicio.dataset.valorAnterior = '';
                    horaFin.dataset.valorAnterior = '';

                    cargarHorariosDisponibles();
                }
            );

            fecha.addEventListener(
                'change',
                () => {
                    horaInicio.dataset.valorAnterior = '';
                    horaFin.dataset.valorAnterior = '';

                    cargarHorariosDisponibles();
                }
            );

            modal.addEventListener('click', event => {
                if (event.target === modal) {
                    cerrarModal();
                }
            });

            document.addEventListener(
                'keydown',
                event => {
                    if (
                        event.key === 'Escape' &&
                        !modal.classList.contains('hidden')
                    ) {
                        cerrarModal();
                    }
                }
            );

           const debeReabrirModal =
    @json($debeReabrirModalBloqueo);

if (debeReabrirModal) {
    const bloqueoAnteriorId =
        bloqueoEditandoCampo.value;

    /*
     * Si existe un identificador, Laravel regresó
     * desde una edición con errores.
     */
    if (bloqueoAnteriorId) {
        const botonBloqueo =
            Array.from(botonesEditar).find(
                boton =>
                    boton.dataset.bloqueoId ===
                    bloqueoAnteriorId
            );

        if (botonBloqueo) {
            bloqueoEditandoId =
                bloqueoAnteriorId;

            formulario.action =
                botonBloqueo.dataset.updateUrl;

            metodoFormulario.disabled = false;
            bloqueoEditandoCampo.disabled = false;

            tituloModal.textContent =
                'Editar bloqueo de agenda';

            descripcionModal.textContent =
                'Corrige los datos del periodo seleccionado.';

            botonGuardar.textContent =
                'Guardar cambios';

            /*
             * Los campos ya contienen old() y no deben
             * reemplazarse con los valores originales.
             */
            abrirModal();
        }
    } else {
        bloqueoEditandoId = null;

        formulario.action =
            formulario.dataset.storeUrl;

        metodoFormulario.disabled = true;
        bloqueoEditandoCampo.disabled = true;

        tituloModal.textContent =
            'Bloquear agenda';

        descripcionModal.textContent =
            'Corrige los datos para registrar el bloqueo.';

        botonGuardar.textContent =
            'Bloquear agenda';

        abrirModal();
    }
} else {
    actualizarHorasFinales();
}
        });
    </script>