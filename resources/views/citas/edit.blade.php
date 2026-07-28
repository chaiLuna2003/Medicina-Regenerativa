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
                       text-gray-700 transition hover:bg-gray-50"
            >
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
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16ZM8.28
                                   7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72
                                   1.72a.75.75 0 101.06 1.06L10 11.06l1.72
                                   1.72a.75.75 0 101.06-1.06L11.06 10l1.72
                                   -1.72a.75.75 0 00-1.06-1.06L10 8.94
                                   8.28 7.22Z"
                                clip-rule="evenodd"
                            />
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
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
            >
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
                                class="mb-2 block text-sm font-semibold text-gray-700"
                            >
                                Paciente <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="paciente_id"
                                name="paciente_id"
                                required
                                class="block w-full rounded-xl border-gray-300 bg-white
                                       text-gray-900 shadow-sm transition
                                       focus:border-[#0D3B7F] focus:ring-[#0D3B7F]
                                       @error('paciente_id') border-red-400 @enderror"
                            >
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
                                class="mb-2 block text-sm font-semibold text-gray-700"
                            >
                                Médico <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="medico_id"
                                name="medico_id"
                                required
                                class="block w-full rounded-xl border-gray-300 bg-white
                                       text-gray-900 shadow-sm transition
                                       focus:border-[#0D3B7F] focus:ring-[#0D3B7F]
                                       @error('medico_id') border-red-400 @enderror"
                            >
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

                    {{-- Fecha, hora y estado --}}
                    <div class="grid gap-6 md:grid-cols-3">

                        {{-- Fecha --}}
                        <div>
                            <label
                                for="fecha"
                                class="mb-2 block text-sm font-semibold text-gray-700"
                            >
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
                                       @error('fecha') border-red-400 @enderror"
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
                                Hora <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="hora"
                                type="time"
                                name="hora"
                                required
                                value="{{ old(
                                    'hora',
                                    \Carbon\Carbon::parse($cita->hora)->format('H:i')
                                ) }}"
                                class="block w-full rounded-xl border-gray-300
                                       text-gray-900 shadow-sm transition
                                       focus:border-[#0D3B7F] focus:ring-[#0D3B7F]
                                       @error('hora') border-red-400 @enderror"
                            >

                            @error('hora')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label
                                for="estado"
                                class="mb-2 block text-sm font-semibold text-gray-700"
                            >
                                Estado <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="estado"
                                name="estado"
                                required
                                class="block w-full rounded-xl border-gray-300 bg-white
                                       text-gray-900 shadow-sm transition
                                       focus:border-[#0D3B7F] focus:ring-[#0D3B7F]
                                       @error('estado') border-red-400 @enderror"
                            >
                                @php
                                    $estadoActual = old('estado', $cita->estado);
                                @endphp

                                <option
                                    value="programada"
                                    @selected($estadoActual === 'programada')
                                >
                                    Programada
                                </option>

                                <option
                                    value="confirmada"
                                    @selected($estadoActual === 'confirmada')
                                >
                                    Confirmada
                                </option>

                                <option
                                    value="en_espera"
                                    @selected($estadoActual === 'en_espera')
                                >
                                    En espera
                                </option>

                                <option
                                    value="en_consulta"
                                    @selected($estadoActual === 'en_consulta')
                                >
                                    En consulta
                                </option>

                                <option
                                    value="finalizada"
                                    @selected($estadoActual === 'finalizada')
                                >
                                    Finalizada
                                </option>

                                <option
                                    value="cancelada"
                                    @selected($estadoActual === 'cancelada')
                                >
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

                    {{-- Motivo --}}
                    <div>
                        <label
                            for="motivo"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Motivo de la cita
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="motivo"
                            type="text"
                            name="motivo"
                            maxlength="255"
                            required
                            value="{{ old('motivo', $cita->motivo) }}"
                            placeholder="Ej. Consulta de valoración"
                            class="block w-full rounded-xl border-gray-300
                                   text-gray-900 shadow-sm transition
                                   placeholder:text-gray-400
                                   focus:border-[#0D3B7F] focus:ring-[#0D3B7F]
                                   @error('motivo') border-red-400 @enderror"
                        >

                        @error('motivo')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Notas --}}
                    <div>
                        <label
                            for="notas"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
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
                                   @error('notas') border-red-400 @enderror"
                        >{{ old('notas', $cita->notas) }}</textarea>

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
                            bg-gray-50 px-6 py-5 sm:flex-row sm:justify-end sm:px-8"
                >
                    <a
                        href="{{ route('citas.index') }}"
                        class="inline-flex items-center justify-center rounded-xl
                               border border-gray-300 bg-white px-5 py-2.5
                               text-sm font-semibold text-gray-700 transition
                               hover:bg-gray-100"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl
                               bg-[#0D3B7F] px-6 py-2.5 text-sm font-semibold
                               text-white shadow-sm transition hover:bg-[#082a5d]
                               focus:outline-none focus:ring-2 focus:ring-[#0D3B7F]
                               focus:ring-offset-2"
                    >
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>