<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Registrar nueva cita
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Selecciona al paciente, médico, fecha y horario.
                </p>
            </div>

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            >
                Regresar al dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                    <h3 class="font-semibold text-red-800">
                        Revisa los siguientes campos:
                    </h3>

                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('citas.store') }}"
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
            >
                @csrf

                <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                    {{-- Paciente --}}
                    {{-- Buscador de paciente --}}
<div class="relative md:col-span-2">
    <label
        for="buscar_paciente"
        class="mb-2 block text-sm font-semibold text-gray-700"
    >
        Paciente
        <span class="text-red-500">*</span>
    </label>

    {{-- Este es el dato que Laravel guardará --}}
    <input
        type="hidden"
        id="paciente_id"
        name="paciente_id"
        value="{{ old('paciente_id') }}"
    >

    <div class="relative">
        <input
            type="search"
            id="buscar_paciente"
            placeholder="Escribe el nombre o apellido del paciente..."
            autocomplete="off"
            class="block w-full rounded-xl border-gray-300 bg-white
                   pr-11 text-gray-900 shadow-sm
                   focus:border-blue-500 focus:ring-blue-500"
        >

        <div
            id="indicador_busqueda"
            class="pointer-events-none absolute inset-y-0 right-4
                   hidden items-center"
        >
            <svg
                class="h-5 w-5 animate-spin text-blue-600"
                viewBox="0 0 24 24"
                fill="none"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                ></path>
            </svg>
        </div>
    </div>

    {{-- Resultados --}}
    <div
        id="resultados_pacientes"
        class="absolute z-30 mt-2 hidden max-h-72 w-full
               overflow-y-auto rounded-xl border border-gray-200
               bg-white shadow-xl"
    ></div>

    {{-- Paciente seleccionado --}}
    <div
        id="paciente_seleccionado"
        class="mt-3 hidden items-center justify-between gap-4
               rounded-xl border border-blue-200 bg-blue-50 px-4 py-3"
    >
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                Paciente seleccionado
            </p>

            <p
                id="nombre_paciente_seleccionado"
                class="mt-1 font-semibold text-gray-900"
            ></p>
        </div>

        <button
            type="button"
            id="quitar_paciente"
            class="shrink-0 text-sm font-semibold text-red-600
                   hover:text-red-700 hover:underline"
        >
            Cambiar
        </button>
    </div>

    <p
        id="mensaje_busqueda"
        class="mt-2 text-sm text-gray-500"
    >
        Escribe al menos 2 caracteres para buscar.
    </p>

    @error('paciente_id')
        <p class="mt-2 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>

                    {{-- Médico --}}
                    <div class="md:col-span-2">
                        <label
                            for="medico_id"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Médico
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="medico_id"
                            name="medico_id"
                            required
                            class="block w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Selecciona un médico
                            </option>

                            @foreach ($medicos as $medico)
                                <option
                                    value="{{ $medico->id }}"
                                    @selected(old('medico_id') == $medico->id)
                                >
                                    {{ $medico->nombre }}

                                    @if (!empty($medico->apellido_paterno))
                                        {{ $medico->apellido_paterno }}
                                    @endif

                                    @if (!empty($medico->especialidad))
                                        — {{ $medico->especialidad }}
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

                    {{-- Fecha --}}
                    <div>
                        <label
                            for="fecha"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Fecha
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="fecha"
                            name="fecha"
                            type="date"
                            min="{{ now()->format('Y-m-d') }}"
                            value="{{ old('fecha', now()->format('Y-m-d')) }}"
                            required
                            class="block w-full rounded-xl border-gray-300 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

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
        class="mb-2 block text-sm font-semibold text-gray-700"
    >
        Hora
        <span class="text-red-500">*</span>
    </label>

    <select
        id="hora"
        name="hora"
        required
        disabled
        data-valor-anterior="{{ old('hora') }}"
        class="block w-full rounded-xl border-gray-300 bg-white
               text-gray-900 shadow-sm
               focus:border-blue-500 focus:ring-blue-500
               disabled:cursor-not-allowed disabled:bg-gray-100"
    >
        <option value="">
            Selecciona primero médico y fecha
        </option>
    </select>

    <p id="mensaje_horarios" class="mt-2 text-sm text-gray-500">
        El sistema mostrará los bloques disponibles de 15 minutos.
    </p>

    @error('hora')
        <p class="mt-2 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>

                    {{-- Motivo --}}
                    <div class="md:col-span-2">
                        <label
                            for="motivo"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Motivo de la cita
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="motivo"
                            name="motivo"
                            type="text"
                            value="{{ old('motivo') }}"
                            placeholder="Ejemplo: Consulta inicial"
                            required
                            maxlength="255"
                            class="block w-full rounded-xl border-gray-300 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        @error('motivo')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div class="md:col-span-2">
                        <label
                            for="estado"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Estado inicial
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="estado"
                            name="estado"
                            required
                            class="block w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option
                                value="programada"
                                @selected(old('estado', 'programada') === 'programada')
                            >
                                Programada
                            </option>

                            <option
                                value="confirmada"
                                @selected(old('estado') === 'confirmada')
                            >
                                Confirmada
                            </option>
                        </select>

                        @error('estado')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Notas --}}
                    <div class="md:col-span-2">
                        <label
                            for="notas"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Notas adicionales
                        </label>

                        <textarea
                            id="notas"
                            name="notas"
                            rows="4"
                            maxlength="2000"
                            placeholder="Información adicional sobre la cita..."
                            class="block w-full resize-none rounded-xl border-gray-300 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('notas') }}</textarea>

                        @error('notas')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-6 py-5 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-100"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-[#0D3B7F] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#082a5d] focus:outline-none focus:ring-2 focus:ring-[#0D3B7F] focus:ring-offset-2"
                    >
                        Guardar cita
                    </button>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const buscador = document.getElementById('buscar_paciente');
    const pacienteId = document.getElementById('paciente_id');
    const resultados = document.getElementById('resultados_pacientes');
    const indicador = document.getElementById('indicador_busqueda');
    const mensaje = document.getElementById('mensaje_busqueda');
    const seleccionado = document.getElementById('paciente_seleccionado');
    const nombreSeleccionado = document.getElementById(
        'nombre_paciente_seleccionado'
    );
    const quitarPaciente = document.getElementById('quitar_paciente');
    const medico = document.getElementById('medico_id');
const fecha = document.getElementById('fecha');
const hora = document.getElementById('hora');
const mensajeHorarios = document.getElementById('mensaje_horarios');

let solicitudHorarios;

    let temporizador;
    let solicitudActual;

    function mostrarMensaje(texto) {
        resultados.innerHTML = '';

        const elemento = document.createElement('p');
        elemento.className = 'px-4 py-4 text-sm text-gray-500';
        elemento.textContent = texto;

        resultados.appendChild(elemento);
        resultados.classList.remove('hidden');
    }

    function seleccionarPaciente(paciente) {
        pacienteId.value = paciente.id;
        nombreSeleccionado.textContent = paciente.nombre_completo;

        seleccionado.classList.remove('hidden');
        seleccionado.classList.add('flex');

        buscador.value = '';
        buscador.disabled = true;
        resultados.classList.add('hidden');

        mensaje.textContent = 'El paciente quedó asignado a la cita.';
    }

    async function buscarPacientes(termino) {
        solicitudActual?.abort();
        solicitudActual = new AbortController();

        indicador.classList.remove('hidden');
        indicador.classList.add('flex');

        try {
  const url = new URL(
    "{{ route('pacientes.buscar', [], false) }}",
    window.location.origin
);

            url.searchParams.set('q', termino);

            const respuesta = await fetch(url.toString(), {
    method: 'GET',
    credentials: 'same-origin',
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    signal: solicitudActual.signal,
});

            if (!respuesta.ok) {
                throw new Error('No se pudo realizar la búsqueda.');
            }

            const pacientes = await respuesta.json();

            resultados.innerHTML = '';

            if (pacientes.length === 0) {
                mostrarMensaje('No se encontraron pacientes.');
                return;
            }

            pacientes.forEach((paciente) => {
                const boton = document.createElement('button');
                boton.type = 'button';
                boton.className =
                    'block w-full border-b border-gray-100 px-4 py-3 ' +
                    'text-left transition last:border-b-0 hover:bg-blue-50';

                const nombre = document.createElement('span');
                nombre.className = 'block font-semibold text-gray-900';
                nombre.textContent = paciente.nombre_completo;

                boton.appendChild(nombre);

                if (paciente.telefono) {
                    const telefono = document.createElement('span');
                    telefono.className = 'mt-1 block text-sm text-gray-500';
                    telefono.textContent = paciente.telefono;
                    boton.appendChild(telefono);
                }

                boton.addEventListener('click', () => {
                    seleccionarPaciente(paciente);
                });

                resultados.appendChild(boton);
            });

            resultados.classList.remove('hidden');
        } catch (error) {
            if (error.name !== 'AbortError') {
                mostrarMensaje(
                    'Ocurrió un error al buscar. Intenta nuevamente.'
                );
            }
        } finally {
            indicador.classList.add('hidden');
            indicador.classList.remove('flex');
        }
    }

    buscador.addEventListener('input', () => {
        clearTimeout(temporizador);

        const termino = buscador.value.trim();

        pacienteId.value = '';

        if (termino.length < 2) {
            resultados.classList.add('hidden');
            mensaje.textContent =
                'Escribe al menos 2 caracteres para buscar.';
            return;
        }

        mensaje.textContent = 'Buscando coincidencias...';

        temporizador = setTimeout(() => {
            buscarPacientes(termino);
        }, 350);
    });

    quitarPaciente.addEventListener('click', () => {
        pacienteId.value = '';
        nombreSeleccionado.textContent = '';

        seleccionado.classList.add('hidden');
        seleccionado.classList.remove('flex');

        buscador.disabled = false;
        mensaje.textContent =
            'Escribe al menos 2 caracteres para buscar.';

        buscador.focus();
    });

    document.addEventListener('click', (event) => {
        if (
            !resultados.contains(event.target) &&
            event.target !== buscador
        ) {
            resultados.classList.add('hidden');
        }
    });

    buscador.addEventListener('focus', () => {
        if (resultados.children.length > 0 && !pacienteId.value) {
            resultados.classList.remove('hidden');
        }
    });

    async function cargarHorarios() {
    if (!medico.value || !fecha.value) {
        hora.disabled = true;
        hora.innerHTML =
            '<option value="">Selecciona primero médico y fecha</option>';

        mensajeHorarios.textContent =
            'El sistema mostrará los bloques disponibles de 15 minutos.';

        return;
    }

    solicitudHorarios?.abort();
    solicitudHorarios = new AbortController();

    hora.disabled = true;
    hora.innerHTML =
        '<option value="">Consultando horarios...</option>';

    mensajeHorarios.textContent =
        'Consultando la agenda del médico...';

    try {
        const url = new URL(
            "{{ route('citas.horarios-disponibles', [], false) }}",
            window.location.origin
        );

        url.searchParams.set('medico_id', medico.value);
        url.searchParams.set('fecha', fecha.value);

        const respuesta = await fetch(url.toString(), {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            signal: solicitudHorarios.signal,
        });

        if (!respuesta.ok) {
            throw new Error('No se pudieron consultar los horarios.');
        }

        const datos = await respuesta.json();

        const horariosDisponibles = datos.horarios.filter(
            (horarioDisponible) => horarioDisponible.disponible
        );

        hora.innerHTML = '';

        if (horariosDisponibles.length === 0) {
            hora.innerHTML =
                '<option value="">No hay horarios disponibles</option>';

            mensajeHorarios.textContent =
                'La agenda del médico está llena para esta fecha.';

            return;
        }

        const valorAnterior = hora.dataset.valorAnterior;

        const opcionInicial = document.createElement('option');
        opcionInicial.value = '';
        opcionInicial.textContent = 'Selecciona un horario';
        hora.appendChild(opcionInicial);

        horariosDisponibles.forEach((horarioDisponible) => {
            const opcion = document.createElement('option');

            opcion.value = horarioDisponible.hora;
            opcion.textContent = horarioDisponible.texto;

            if (
                valorAnterior === horarioDisponible.hora ||
                (
                    !valorAnterior &&
                    datos.primer_disponible === horarioDisponible.hora
                )
            ) {
                opcion.selected = true;
            }

            hora.appendChild(opcion);
        });

        hora.disabled = false;

        const opcionSeleccionada =
            hora.options[hora.selectedIndex]?.text;

        mensajeHorarios.textContent = opcionSeleccionada
            ? `Próximo espacio disponible: ${opcionSeleccionada}.`
            : 'Selecciona uno de los horarios disponibles.';
    } catch (error) {
        if (error.name === 'AbortError') {
            return;
        }

        hora.innerHTML =
            '<option value="">No se pudieron cargar los horarios</option>';

        mensajeHorarios.textContent =
            'Ocurrió un error al consultar la disponibilidad.';
    }
}

medico.addEventListener('change', () => {
    hora.dataset.valorAnterior = '';
    cargarHorarios();
});

fecha.addEventListener('change', () => {
    hora.dataset.valorAnterior = '';
    cargarHorarios();
});

cargarHorarios();
    
});


</script>