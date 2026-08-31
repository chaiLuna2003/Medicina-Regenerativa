@php
    $casoParaCerrar =
        $cita->evolucionClinica?->casoClinico;

    $erroresCierreCaso =
        $errors->getBag('cierreCasoClinico');
@endphp

@if (
    $casoParaCerrar
    && $casoParaCerrar->estaActivo()
)
    @can('cerrar', $casoParaCerrar)
        <div
            id="modal-clinico-cerrar-caso"
            data-modal-clinico-panel="cerrar-caso"
            class="fixed inset-0 z-50 hidden"
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-modal-cerrar-caso">

            <div
                class="absolute inset-0
                       bg-slate-950/60 backdrop-blur-sm"
                data-cerrar-modal-clinico>
            </div>

            <div
                class="relative flex min-h-full
                       items-center justify-center
                       p-3 sm:p-5">

                <div
                    class="relative flex max-h-[96vh]
                           w-full max-w-xl flex-col
                           overflow-hidden rounded-2xl
                           bg-white shadow-2xl">

                    <header
                        class="flex items-start
                               justify-between gap-5
                               border-b border-slate-200
                               px-5 py-4 sm:px-6">

                        <div>
                            <p
                                class="text-xs font-semibold
                                       uppercase tracking-wide
                                       text-red-600">
                                Finalizar seguimiento
                            </p>

                            <h2
                                id="titulo-modal-cerrar-caso"
                                class="mt-1 text-xl font-bold
                                       text-slate-900">
                                Cerrar caso clínico
                            </h2>
                        </div>

                        <button
                            type="button"
                            data-cerrar-modal-clinico
                            aria-label="Cerrar ventana"
                            class="rounded-lg p-2
                                   text-slate-400 transition
                                   hover:bg-slate-100
                                   hover:text-slate-700">
                            ✕
                        </button>
                    </header>

                    <form
                        method="POST"
                        action="{{ route(
                            'casos-clinicos.cerrar',
                            $casoParaCerrar
                        ) }}"
                        class="min-h-0 overflow-y-auto">

                        @csrf
                        @method('PATCH')

                        <div
                            class="space-y-5
                                   px-5 py-5 sm:px-6">

                            <div
                                class="rounded-xl border
                                       border-red-200
                                       bg-red-50 p-4">

                                <p
                                    class="font-semibold
                                           text-red-900">
                                    {{ $casoParaCerrar->nombre }}
                                </p>

                                <p
                                    class="mt-1 text-sm
                                           leading-6 text-red-700">
                                    El caso seguirá disponible
                                    para consulta, pero no aceptará
                                    nuevas evoluciones. No podrá
                                    reabrirse desde la aplicación.
                                </p>
                            </div>

                            <div>
                                <label
                                    for="motivo-cierre-caso"
                                    class="text-sm font-semibold
                                           text-slate-800">
                                    Motivo de cierre
                                    <span class="text-red-600">
                                        *
                                    </span>
                                </label>

                                <textarea
                                    id="motivo-cierre-caso"
                                    name="motivo_cierre"
                                    rows="5"
                                    required
                                    minlength="10"
                                    maxlength="5000"
                                    placeholder="Ejemplo: Alta médica por cumplimiento de objetivos clínicos."
                                    class="mt-2 block w-full
                                           rounded-xl
                                           border-slate-300
                                           text-sm shadow-sm
                                           focus:border-[#0D3B7F]
                                           focus:ring-[#0D3B7F]">{{ old('motivo_cierre') }}</textarea>

                                @error(
                                    'motivo_cierre',
                                    'cierreCasoClinico'
                                )
                                    <p
                                        class="mt-2 text-sm
                                               text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <label
                                class="flex cursor-pointer
                                       items-start gap-3
                                       rounded-xl border
                                       border-slate-200
                                       bg-slate-50 p-4">

                                <input
                                    type="checkbox"
                                    name="confirmacion_cierre"
                                    value="1"
                                    required
                                    @checked(
                                        old('confirmacion_cierre')
                                    )
                                    class="mt-0.5 rounded
                                           border-slate-300
                                           text-[#0D3B7F]
                                           focus:ring-[#0D3B7F]">

                                <span
                                    class="text-sm leading-6
                                           text-slate-700">
                                    Confirmo que el seguimiento
                                    debe cerrarse y que revisé el
                                    motivo registrado.
                                </span>
                            </label>

                            @error(
                                'confirmacion_cierre',
                                'cierreCasoClinico'
                            )
                                <p class="text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <footer
                            class="flex flex-col-reverse gap-3
                                   border-t border-slate-200
                                   bg-white px-5 py-4
                                   sm:flex-row sm:justify-end
                                   sm:px-6">

                            <button
                                type="button"
                                data-cerrar-modal-clinico
                                class="rounded-xl border
                                       border-slate-300
                                       px-5 py-2.5 text-sm
                                       font-semibold
                                       text-slate-700
                                       transition
                                       hover:bg-slate-50">
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                class="rounded-xl bg-red-600
                                       px-5 py-2.5 text-sm
                                       font-semibold text-white
                                       transition
                                       hover:bg-red-700">
                                Confirmar cierre
                            </button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>

        @if ($erroresCierreCaso->any())
            <script>
                document.addEventListener(
                    'DOMContentLoaded',
                    function() {
                        abrirModalClinico(
                            'cerrar-caso'
                        );
                    }
                );
            </script>
        @endif
    @endcan
@endif