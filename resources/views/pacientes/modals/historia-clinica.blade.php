 @if (
    request()->user()->isAdmin()
    || request()->user()->isMedico()
    )

    <div
        id="modal-historia-clinica"
        class="fixed inset-0 z-50 hidden
           items-center justify-center
           bg-slate-950/50 p-4"
        aria-hidden="true"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-historia-clinica">

        <div
            class="max-h-[90vh] w-full max-w-4xl
               overflow-y-auto rounded-2xl
               bg-white shadow-2xl">

            {{-- Encabezado --}}
            <div
                class="sticky top-0 z-10
                   flex items-start justify-between
                   border-b border-slate-100
                   bg-white px-6 py-5">

                <div>
                    <h3
                        id="titulo-modal-historia-clinica"
                        class="text-lg font-semibold text-slate-900">

                        {{ $pacientes->historiaClinica
                        ? 'Editar historia clínica'
                        : 'Registrar historia clínica' }}
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Registra el resumen clínico principal del paciente.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalHistoriaClinica()"
                    class="flex h-9 w-9 items-center
                       justify-center rounded-lg
                       text-slate-400 transition
                       hover:bg-slate-100
                       hover:text-slate-700"
                    aria-label="Cerrar modal">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form
                method="POST"
                action="{{ route(
                'pacientes.historia-clinica.update',
                $pacientes
            ) }}">

                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2">

                    {{-- Patología base --}}
                    <div>
                        <label
                            for="patologia_base"
                            class="mb-1.5 block
                               text-sm font-semibold
                               text-slate-700">
                            Patología base
                        </label>

                        <textarea
                            id="patologia_base"
                            name="patologia_base"
                            rows="6"
                            maxlength="20000"
                            placeholder="Describe las enfermedades o condiciones principales..."
                            class="block w-full resize-y
                               rounded-xl border-slate-300
                               text-sm shadow-sm
                               focus:border-cyan-500
                               focus:ring-cyan-500">{{ old(
                            'patologia_base',
                            $pacientes->historiaClinica?->patologia_base
                        ) }}</textarea>

                        @error('patologia_base')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Padecimiento actual --}}
                    <div>
                        <label
                            for="padecimiento_actual"
                            class="mb-1.5 block
                               text-sm font-semibold
                               text-slate-700">
                            Padecimiento actual
                        </label>

                        <textarea
                            id="padecimiento_actual"
                            name="padecimiento_actual"
                            rows="6"
                            maxlength="20000"
                            placeholder="Describe síntomas, evolución y motivo de atención..."
                            class="block w-full resize-y
                               rounded-xl border-slate-300
                               text-sm shadow-sm
                               focus:border-cyan-500
                               focus:ring-cyan-500">{{ old(
                            'padecimiento_actual',
                            $pacientes->historiaClinica?->padecimiento_actual
                        ) }}</textarea>

                        @error('padecimiento_actual')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Tratamientos actuales --}}
                    <div>
                        <label
                            for="tratamientos_actuales"
                            class="mb-1.5 block
                               text-sm font-semibold
                               text-slate-700">
                            Tratamientos actuales
                        </label>

                        <textarea
                            id="tratamientos_actuales"
                            name="tratamientos_actuales"
                            rows="6"
                            maxlength="20000"
                            placeholder="Medicamentos, terapias, dosis y frecuencia..."
                            class="block w-full resize-y
                               rounded-xl border-slate-300
                               text-sm shadow-sm
                               focus:border-cyan-500
                               focus:ring-cyan-500">{{ old(
                            'tratamientos_actuales',
                            $pacientes->historiaClinica?->tratamientos_actuales
                        ) }}</textarea>

                        @error('tratamientos_actuales')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Prioridad y análisis --}}
                    <div>
                        <label
                            for="prioridad_analisis_medico"
                            class="mb-1.5 block
                               text-sm font-semibold
                               text-slate-700">
                            Prioridad y análisis médico
                        </label>

                        <textarea
                            id="prioridad_analisis_medico"
                            name="prioridad_analisis_medico"
                            rows="6"
                            maxlength="20000"
                            placeholder="Registra prioridades, valoración y análisis clínico..."
                            class="block w-full resize-y
                               rounded-xl border-slate-300
                               text-sm shadow-sm
                               focus:border-cyan-500
                               focus:ring-cyan-500">{{ old(
                            'prioridad_analisis_medico',
                            $pacientes->historiaClinica?->prioridad_analisis_medico
                        ) }}</textarea>

                        @error('prioridad_analisis_medico')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                </div>

                {{-- Acciones --}}
                <div
                    class="sticky bottom-0
                       flex items-center justify-end gap-3
                       border-t border-slate-100
                       bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="cerrarModalHistoriaClinica()"
                        class="rounded-xl border
                           border-slate-300 bg-white
                           px-4 py-2.5
                           text-sm font-semibold
                           text-slate-700 transition
                           hover:bg-slate-50">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-cyan-600
                           px-5 py-2.5
                           text-sm font-semibold
                           text-white shadow-sm
                           transition hover:bg-cyan-700">
                        Guardar historia clínica
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endif