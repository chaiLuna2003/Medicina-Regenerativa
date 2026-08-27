 @php
    $habitoAlimenticioModal = $pacientes
    ->historiaClinica
    ?->habitoAlimenticio;

    $comidasRegistradasModal =
    $habitoAlimenticioModal?->comidas ?? [];

    $alimentosRegistradosModal =
    $habitoAlimenticioModal?->alimentos ?? [];
    @endphp

    @if (
    request()->user()->isAdmin()
    || request()->user()->isMedico()
    )
    <div
        id="modal-habitos-alimenticios"
        class="fixed inset-0 z-50 hidden
               items-center justify-center
               bg-slate-950/60 p-4"
        aria-hidden="true"
        onclick="
            if (event.target === this) {
                cerrarModalHabitosAlimenticios();
            }
        ">

        <div
            class="flex max-h-[90vh] w-full
                   max-w-6xl flex-col
                   overflow-hidden rounded-2xl
                   bg-white shadow-2xl">

            {{-- Encabezado --}}
            <div
                class="flex items-center justify-between
                       border-b border-slate-200
                       px-6 py-5">

                <div>
                    <h2
                        class="text-lg font-semibold
                               text-slate-900">
                        Hábitos alimenticios
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Registra las comidas habituales y la
                        frecuencia o cantidad de consumo.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalHabitosAlimenticios()"
                    class="rounded-lg p-2 text-slate-400
                           transition hover:bg-slate-100
                           hover:text-slate-700"
                    aria-label="Cerrar">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form
                method="POST"
                action="{{ route(
                    'pacientes.historia-clinica.'
                    . 'habitos-alimenticios.update',
                    $pacientes
                ) }}"
                class="flex min-h-0 flex-1 flex-col">

                @csrf
                @method('PUT')

                <div class="flex-1 overflow-y-auto p-6">

                    {{-- Errores generales --}}
                    @if (
                    $errors->habitosAlimenticios->any()
                    )
                    <div
                        class="mb-6 rounded-xl border
                                   border-red-200 bg-red-50
                                   px-4 py-3 text-sm text-red-700">

                        <p class="font-semibold">
                            Revisa los campos señalados.
                        </p>

                        <ul class="mt-2 list-disc pl-5">
                            @foreach (
                            $errors->habitosAlimenticios->all()
                            as $error
                            )
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Comidas --}}
                    <section>
                        <h3
                            class="text-sm font-semibold
                                   text-slate-900">
                            Comidas realizadas habitualmente
                        </h3>

                        <div
                            class="mt-4 grid grid-cols-2 gap-3
                                   sm:grid-cols-3
                                   lg:grid-cols-5">

                            @foreach (
                            $comidasHabitosAlimenticios
                            as $clave => $etiqueta
                            )
                            @php
                            $seleccionada = old(
                            "comidas.{$clave}",
                            data_get(
                            $comidasRegistradasModal,
                            $clave,
                            false
                            )
                            );
                            @endphp

                            <label
                                class="flex cursor-pointer
                                           items-center gap-3
                                           rounded-xl border
                                           border-slate-200
                                           bg-slate-50 p-4
                                           transition
                                           hover:border-indigo-300
                                           hover:bg-indigo-50">

                                <input
                                    id="habito_comida_{{ $clave }}"
                                    type="checkbox"
                                    name="comidas[{{ $clave }}]"
                                    value="1"
                                    @checked($seleccionada)
                                    class="h-4 w-4 rounded
                                               border-slate-300
                                               text-indigo-600
                                               focus:ring-indigo-500">

                                <span
                                    class="text-sm font-medium
                                               text-slate-700">
                                    {{ $etiqueta }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </section>

                    {{-- Alimentos --}}
                    <section class="mt-8">

                        <div>
                            <h3
                                class="text-sm font-semibold
                                       text-slate-900">
                                Frecuencia o cantidad de alimentos
                            </h3>

                            <p class="mt-1 text-xs text-slate-400">
                                Ejemplos: diario, ocasional,
                                2 veces por semana, 1.5 litros.
                            </p>
                        </div>

                        <div
                            class="mt-4 grid grid-cols-1 gap-4
                                   sm:grid-cols-2
                                   lg:grid-cols-3">

                            @foreach (
                            $camposHabitosAlimenticios
                            as $clave => $etiqueta
                            )
                            <div>
                                <label
                                    for="habito_alimento_{{ $clave }}"
                                    class="mb-1.5 block
                                               text-xs font-semibold
                                               text-slate-600">

                                    {{ $etiqueta }}
                                </label>

                                <input
                                    id="habito_alimento_{{ $clave }}"
                                    type="text"
                                    name="alimentos[{{ $clave }}]"
                                    value="{{ old(
    "alimentos.{$clave}",
    data_get(
        $alimentosRegistradosModal,
        $clave
    )
) }}"
                                    maxlength="500"
                                    placeholder="Frecuencia o cantidad"
                                    class="w-full rounded-xl
                                               border-slate-300
                                               text-sm shadow-sm
                                               focus:border-indigo-500
                                               focus:ring-indigo-500">

                                @error(
                                "alimentos.{$clave}",
                                'habitosAlimenticios'
                                )
                                <p
                                    class="mt-1 text-xs
                                                   text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                    </section>
                </div>

                {{-- Acciones --}}
                <div
                    class="flex justify-end gap-3
                           border-t border-slate-200
                           bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="
                            cerrarModalHabitosAlimenticios()
                        "
                        class="rounded-xl border
                               border-slate-300 bg-white
                               px-5 py-2.5 text-sm
                               font-semibold text-slate-700
                               transition hover:bg-slate-100">

                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-indigo-600
                               px-5 py-2.5 text-sm
                               font-semibold text-white
                               shadow-sm transition
                               hover:bg-indigo-700">

                        Guardar hábitos
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
