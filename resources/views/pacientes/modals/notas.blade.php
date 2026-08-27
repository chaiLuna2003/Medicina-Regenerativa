@if (request()->user()->isAdmin())

    {{-- ===================================================== --}}
    {{-- MODAL: NOTAS --}}
    {{-- ===================================================== --}}
    <div
        id="modal-notas"
        class="fixed inset-0 z-50 hidden
               items-center justify-center
               bg-slate-950/40 px-4 py-8
               backdrop-blur-[2px]"
        aria-hidden="true">
        <div
            class="w-full max-w-lg overflow-hidden
                   rounded-2xl bg-white shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-modal-notas">
            {{-- Encabezado --}}
            <div
                class="flex items-start justify-between
                       border-b border-slate-100
                       px-6 py-5">
                <div>
                    <h3
                        id="titulo-modal-notas"
                        class="text-lg font-semibold text-slate-900">
                        Editar notas
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Actualiza las observaciones generales
                        del paciente.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalNotas()"
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

            {{-- Formulario --}}
            <form
                method="POST"
                action="{{ route('pacientes.update', $pacientes) }}">
                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="seccion"
                    value="notas">

                <div class="px-6 py-6">
                    <label
                        for="modal_notas"
                        class="mb-1.5 block
                               text-sm font-medium
                               text-slate-700">
                        Notas / observaciones
                    </label>

                    <textarea
                        id="modal_notas"
                        name="notas"
                        rows="7"
                        maxlength="5000"
                        placeholder="Escribe observaciones generales sobre el paciente..."
                        class="block w-full resize-y
                               rounded-xl border-slate-300
                               text-sm shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500">{{ old('notas', $pacientes->notas) }}</textarea>

                    @error('notas')
                    <p class="mt-1.5 text-xs text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                    <p class="mt-2 text-xs text-slate-400">
                        Utiliza este espacio para información general
                        relevante del paciente.
                    </p>
                </div>

                {{-- Acciones --}}
                <div
                    class="flex items-center justify-end gap-3
                           border-t border-slate-100
                           bg-slate-50 px-6 py-4">
                    <button
                        type="button"
                        onclick="cerrarModalNotas()"
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
                        class="rounded-xl bg-blue-600
                               px-5 py-2.5
                               text-sm font-semibold
                               text-white shadow-sm
                               transition
                               hover:bg-blue-700
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500
                               focus:ring-offset-2">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endif