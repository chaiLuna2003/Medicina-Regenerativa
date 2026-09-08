@if ($puedeEditarCita)
<div
    id="modal-edicion-cita"
    data-modal-edicion-cita
    data-abrir-al-cargar="{{ $errors->any() && old('_origen') === 'modal_edicion_cita' ? 'true' : 'false' }}"
    class="fixed inset-0 z-50 hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="titulo-modal-edicion-cita"
    aria-hidden="true">

    <div
        data-cerrar-modal-edicion-cita
        class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm">
    </div>

    <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
        <section
            class="relative flex max-h-[92vh] w-full max-w-5xl
                   flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">

            <header
                class="flex items-start justify-between gap-4
                       border-b border-gray-200 px-6 py-5 sm:px-8">
                <div>
                    <p class="text-sm font-semibold text-[#0D3B7F]">
                        Actualizar información
                    </p>

                    <h2
                        id="titulo-modal-edicion-cita"
                        class="mt-1 text-xl font-bold text-gray-900">
                        Editar cita
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Modifica los datos administrativos de la cita.
                    </p>
                </div>

                <button
                    type="button"
                    data-cerrar-modal-edicion-cita
                    class="inline-flex h-10 w-10 items-center justify-center
                           rounded-full text-gray-500 transition
                           hover:bg-gray-100 hover:text-gray-900"
                    aria-label="Cerrar modal">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <form
                method="POST"
                action="{{ route('citas.update', $cita) }}"
                data-formulario-edicion-cita
                class="flex min-h-0 flex-1 flex-col">
                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="_origen"
                    value="modal_edicion_cita">

                <input
                    type="hidden"
                    name="estado"
                    value="{{ old('estado', $cita->estado) }}">

                <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6 sm:px-8">
                    @if ($errors->any() && old('_origen') === 'modal_edicion_cita')
                    <div
                        class="mb-6 rounded-2xl border border-red-200
                               bg-red-50 p-4 text-sm text-red-700">
                        <p class="font-semibold">
                            No se pudo actualizar la cita.
                        </p>

                        <ul class="mt-2 list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label
                                for="edicion_paciente_id"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Paciente
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="edicion_paciente_id"
                                name="paciente_id"
                                required
                                class="block w-full rounded-xl border-gray-300
                                       bg-white text-gray-900 shadow-sm
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">
                                @foreach ($pacientes as $paciente)
                                <option
                                    value="{{ $paciente->id }}"
                                    @selected(
                                        (int) old(
                                            'paciente_id',
                                            $cita->paciente_id
                                        ) === $paciente->id
                                    )>
                                    {{ trim(
                                        $paciente->nombre
                                        .' '
                                        .$paciente->apellido
                                    ) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                for="edicion_medico_id"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Médico
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="edicion_medico_id"
                                name="medico_id"
                                required
                                class="block w-full rounded-xl border-gray-300
                                       bg-white text-gray-900 shadow-sm
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">
                                @foreach ($medicos as $medico)
                                <option
                                    value="{{ $medico->id }}"
                                    @selected(
                                        (int) old(
                                            'medico_id',
                                            $cita->medico_id
                                        ) === $medico->id
                                    )>
                                    Dr. {{ trim(
                                        $medico->nombre
                                        .' '
                                        .$medico->apellido_paterno
                                        .' '
                                        .$medico->apellido_materno
                                    ) }}

                                    @if ($medico->especialidad)
                                    — {{ $medico->especialidad }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                for="edicion_fecha"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Fecha
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="edicion_fecha"
                                type="date"
                                name="fecha"
                                required
                                value="{{ old(
                                    'fecha',
                                    $cita->fecha->format('Y-m-d')
                                ) }}"
                                class="block w-full rounded-xl border-gray-300
                                       text-gray-900 shadow-sm
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">
                        </div>

                        <div>
                            <label
                                for="edicion_hora"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Hora de inicio
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="edicion_hora"
                                name="hora"
                                required
                                data-valor-anterior="{{ old(
                                    'hora',
                                    \Carbon\Carbon::parse(
                                        $cita->hora
                                    )->format('H:i')
                                ) }}"
                                class="block w-full rounded-xl border-gray-300
                                       bg-white text-gray-900 shadow-sm
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">
                                <option
                                    value="{{ old(
                                        'hora',
                                        \Carbon\Carbon::parse(
                                            $cita->hora
                                        )->format('H:i')
                                    ) }}"
                                    selected>
                                    {{ \Carbon\Carbon::parse(
                                        old('hora', $cita->hora)
                                    )->format('h:i A') }}
                                </option>
                            </select>

                            <p
                                data-mensaje-horarios-edicion
                                class="mt-2 text-xs text-gray-500">
                                Se validará contra la agenda del médico.
                            </p>
                        </div>

                        <div>
                            <label
                                for="edicion_duracion_minutos"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Duración
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="edicion_duracion_minutos"
                                name="duracion_minutos"
                                required
                                data-valor-anterior="{{ old(
                                    'duracion_minutos',
                                    $cita->duracion_minutos ?? 15
                                ) }}"
                                class="block w-full rounded-xl border-gray-300
                                       bg-white text-gray-900 shadow-sm
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">
                                <option
                                    value="{{ old(
                                        'duracion_minutos',
                                        $cita->duracion_minutos ?? 15
                                    ) }}"
                                    selected>
                                    {{ old(
                                        'duracion_minutos',
                                        $cita->duracion_minutos ?? 15
                                    ) }}
                                    minutos
                                </option>
                            </select>

                            <p
                                data-mensaje-duracion-edicion
                                class="mt-2 text-xs text-gray-500">
                                Intervalos de 15 minutos hasta las 09:00 PM.
                            </p>
                        </div>

                        <div>
                            <label
                                for="edicion_modalidad"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Modalidad
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="edicion_modalidad"
                                name="modalidad"
                                required
                                class="block w-full rounded-xl border-gray-300
                                       bg-white text-gray-900 shadow-sm
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">
                                <option
                                    value="presencial"
                                    @selected(
                                        old(
                                            'modalidad',
                                            $cita->modalidad
                                        ) === 'presencial'
                                    )>
                                    Presencial
                                </option>

                                <option
                                    value="telefonica"
                                    @selected(
                                        old(
                                            'modalidad',
                                            $cita->modalidad
                                        ) === 'telefonica'
                                    )>
                                    Telefónica
                                </option>

                                <option
                                    value="videoconsulta"
                                    @selected(
                                        old(
                                            'modalidad',
                                            $cita->modalidad
                                        ) === 'videoconsulta'
                                    )>
                                    Videoconsulta
                                </option>

                                <option
                                    value="fuera_instalaciones"
                                    @selected(
                                        old(
                                            'modalidad',
                                            $cita->modalidad
                                        ) === 'fuera_instalaciones'
                                    )>
                                    Fuera de las instalaciones
                                </option>
                            </select>
                        </div>

                        <div
                            data-contenedor-direccion-edicion
                            class="md:col-span-2
                                   {{ old(
                                       'modalidad',
                                       $cita->modalidad
                                   ) === 'fuera_instalaciones'
                                       ? ''
                                       : 'hidden' }}">
                            <label
                                for="edicion_direccion_cita"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Dirección de la cita
                                <span class="text-red-500">*</span>
                            </label>

                            <textarea
                                id="edicion_direccion_cita"
                                name="direccion_cita"
                                rows="3"
                                maxlength="500"
                                class="block w-full rounded-xl border-gray-300
                                       text-gray-900 shadow-sm
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">{{ old(
                                    'direccion_cita',
                                    $cita->direccion_cita
                                ) }}</textarea>
                        </div>

                        <div>
                            <label
                                for="edicion_motivo"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Motivo
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="edicion_motivo"
                                name="motivo"
                                required
                                class="block w-full rounded-xl border-gray-300
                                       bg-white text-gray-900 shadow-sm
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">
                                @php
                                $motivosEdicion = [
                                    'consulta_inicial' =>
                                        'Consulta inicial',

                                    'consulta_subsecuente' =>
                                        'Consulta subsecuente',

                                    'consulta_emergencia' =>
                                        'Consulta de emergencia',
                                ];

                                if (
                                    filled($cita->motivo)
                                    && ! array_key_exists(
                                        $cita->motivo,
                                        $motivosEdicion
                                    )
                                ) {
                                    $motivosEdicion[$cita->motivo] =
                                        ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $cita->motivo
                                            )
                                        );
                                }

                                $motivoSeleccionado = old(
                                    'motivo',
                                    $cita->motivo
                                );
                                @endphp

                                @foreach ($motivosEdicion as $valor => $texto)
                                <option
                                    value="{{ $valor }}"
                                    @selected(
                                        $motivoSeleccionado === $valor
                                    )>
                                    {{ $texto }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label
                                for="edicion_notas"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Notas adicionales
                            </label>

                            <textarea
                                id="edicion_notas"
                                name="notas"
                                rows="4"
                                maxlength="2000"
                                class="block w-full resize-y rounded-xl
                                       border-gray-300 text-gray-900 shadow-sm
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">{{ old(
                                    'notas',
                                    $cita->notas
                                ) }}</textarea>
                        </div>
                    </div>
                </div>

                <footer
                    class="flex flex-col-reverse gap-3 border-t
                           border-gray-200 bg-gray-50 px-6 py-4
                           sm:flex-row sm:justify-end sm:px-8">
                    <button
                        type="button"
                        data-cerrar-modal-edicion-cita
                        class="inline-flex items-center justify-center
                               rounded-xl border border-gray-300 bg-white
                               px-5 py-2.5 text-sm font-semibold text-gray-700
                               transition hover:bg-gray-100">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center
                               rounded-xl bg-[#0D3B7F] px-6 py-2.5
                               text-sm font-semibold text-white shadow-sm
                               transition hover:bg-[#082a5d]">
                        Guardar cambios
                    </button>
                </footer>
            </form>
        </section>
    </div>
</div>
@endif