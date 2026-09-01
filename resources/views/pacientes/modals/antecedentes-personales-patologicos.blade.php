  @php
    $personalesPatologicosModal = $pacientes
    ->historiaClinica
    ?->antecedentesPersonalesPatologicos;

    $valoresPersonalesPatologicosModal =
    $personalesPatologicosModal?->antecedentes
    ?? [];
    @endphp

    @if (request()->user()->isMedico())

    <div
        id="modal-personales-patologicos"
        class="fixed inset-0 z-50 hidden
           items-center justify-center
           bg-slate-950/50 p-4"
        aria-hidden="true"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-personales-patologicos">

        <div
            class="max-h-[90vh] w-full max-w-5xl
               overflow-y-auto rounded-2xl
               bg-white shadow-2xl">

            <div
                class="sticky top-0 z-10
                   flex items-start justify-between
                   border-b border-slate-100
                   bg-white px-6 py-5">

                <div>
                    <h3
                        id="titulo-modal-personales-patologicos"
                        class="text-lg font-semibold text-slate-900">
                        Antecedentes personales patológicos
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Escribe “negado” o especifica el antecedente.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalPersonalesPatologicos()"
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
                'pacientes.historia-clinica.'
                . 'personales-patologicos.update',
                $pacientes
            ) }}">

                @csrf
                @method('PUT')

                <div
                    class="grid grid-cols-1 gap-4 p-6
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4">

                    @foreach (
                    $camposPersonalesPatologicos
                    as $clave => $etiqueta
                    )

                    <div>
                        <label
                            for="personal_patologico_{{ $clave }}"
                            class="mb-1.5 block
                                   text-sm font-medium
                                   text-slate-700">
                            {{ $etiqueta }}
                        </label>

                        <input
                            id="personal_patologico_{{ $clave }}"
                            name="antecedentes[{{ $clave }}]"
                            type="text"
                            maxlength="1000"
                            value="{{ old(
    "antecedentes.{$clave}",
    data_get(
        $valoresPersonalesPatologicosModal,
        $clave
    ),
    'personalesPatologicos'
) }}"
                            placeholder="Negado o especifique"
                            class="block w-full rounded-xl
                                   border-slate-300 text-sm
                                   shadow-sm
                                   focus:border-amber-500
                                   focus:ring-amber-500">

                        @error(
                        "antecedentes.{$clave}",
                        'personalesPatologicos'
                        )
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    @endforeach
                </div>

                <div
                    class="sticky bottom-0
                       flex items-center justify-end gap-3
                       border-t border-slate-100
                       bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="cerrarModalPersonalesPatologicos()"
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
                        class="rounded-xl bg-amber-600
                           px-5 py-2.5
                           text-sm font-semibold
                           text-white shadow-sm
                           transition hover:bg-amber-700">
                        Guardar antecedentes
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endif