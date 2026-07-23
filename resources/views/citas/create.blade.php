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
                    <div class="md:col-span-2">
                        <label
                            for="paciente_id"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Paciente
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="paciente_id"
                            name="paciente_id"
                            required
                            class="block w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Selecciona un paciente
                            </option>

                            @foreach ($pacientes as $paciente)
                                <option
                                    value="{{ $paciente->id }}"
                                    @selected(old('paciente_id') == $paciente->id)
                                >
                                    {{
                                        trim(
                                            $paciente->nombre . ' ' .
                                            $paciente->apellido_paterno . ' ' .
                                            $paciente->apellido_materno
                                        )
                                    }}
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

                        <input
                            id="hora"
                            name="hora"
                            type="time"
                            value="{{ old('hora') }}"
                            required
                            class="block w-full rounded-xl border-gray-300 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

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