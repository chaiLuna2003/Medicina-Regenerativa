 @if (
    request()->user()->isAdmin()
    || request()->user()->isRecepcionista()
    )

    {{-- MODAL: CONTACTO --}}
    <div
        id="modal-contacto"
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
            aria-labelledby="titulo-modal-contacto">
            <div
                class="flex items-start justify-between
                       border-b border-slate-100
                       px-6 py-5">
                <div>
                    <h3
                        id="titulo-modal-contacto"
                        class="text-lg font-semibold text-slate-900">
                        Editar información de contacto
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Actualiza el teléfono y correo del paciente.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalContacto()"
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
                action="{{ route('pacientes.update', $pacientes) }}">
                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="seccion"
                    value="contacto">

                <div class="space-y-5 px-6 py-6">

                    <div>
                        <label
                            for="modal_telefono"
                            class="mb-1.5 block text-sm
                                   font-medium text-slate-700">
                            Celular / WhatsApp
                        </label>

                        <input
                            id="modal_telefono"
                            name="telefono"
                            type="text"
                            value="{{ old(
                                'telefono',
                                $pacientes->telefono
                            ) }}"
                            maxlength="20"
                            class="block w-full rounded-xl
                                   border-slate-300 text-sm
                                   shadow-sm
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                        @error('telefono')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Teléfono fijo --}}
                    <div>
                        <label
                            for="modal_telefono_fijo"
                            class="mb-1.5 block text-sm
               font-medium text-slate-700">
                            Teléfono
                        </label>

                        <input
                            id="modal_telefono_fijo"
                            name="telefono_fijo"
                            type="text"
                            value="{{ old(
            'telefono_fijo',
            $pacientes->telefono_fijo
        ) }}"
                            maxlength="20"
                            class="block w-full rounded-xl
               border-slate-300 text-sm
               shadow-sm
               focus:border-blue-500
               focus:ring-blue-500">

                        @error('telefono_fijo')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Teléfono secundario --}}
                    <div>
                        <label
                            for="modal_telefono_secundario"
                            class="mb-1.5 block text-sm
               font-medium text-slate-700">
                            Teléfono secundario
                        </label>

                        <input
                            id="modal_telefono_secundario"
                            name="telefono_secundario"
                            type="text"
                            value="{{ old(
            'telefono_secundario',
            $pacientes->telefono_secundario
        ) }}"
                            maxlength="20"
                            class="block w-full rounded-xl
               border-slate-300 text-sm
               shadow-sm
               focus:border-blue-500
               focus:ring-blue-500">

                        @error('telefono_secundario')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="modal_email"
                            class="mb-1.5 block text-sm
                                   font-medium text-slate-700">
                            Correo electrónico
                        </label>

                        <input
                            id="modal_email"
                            name="email"
                            type="email"
                            value="{{ old(
                                'email',
                                $pacientes->email
                            ) }}"
                            maxlength="255"
                            class="block w-full rounded-xl
                                   border-slate-300 text-sm
                                   shadow-sm
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                        @error('email')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                </div>

                <div
                    class="flex items-center justify-end gap-3
                           border-t border-slate-100
                           bg-slate-50 px-6 py-4">
                    <button
                        type="button"
                        onclick="cerrarModalContacto()"
                        class="rounded-xl border
                               border-slate-300 bg-white
                               px-4 py-2.5 text-sm
                               font-semibold text-slate-700
                               transition hover:bg-slate-50">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600
                               px-5 py-2.5 text-sm
                               font-semibold text-white
                               shadow-sm transition
                               hover:bg-blue-700">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endif