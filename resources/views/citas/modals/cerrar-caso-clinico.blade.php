@php
    $casoParaCerrar = $cita->evolucionClinica?->casoClinico;

    $erroresCierreCaso = $errors->getBag('cierreCasoClinico');
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
            aria-labelledby="titulo-modal-cerrar-caso"
            aria-describedby="advertencia-cierre-caso">

            <div
                class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
                data-cerrar-modal-clinico>
            </div>

            <div
                class="relative flex min-h-full items-center
                       justify-center p-3 sm:p-5">

                <div
                    class="relative flex max-h-[96vh] w-full min-w-0
                           max-w-xl flex-col overflow-hidden
                           rounded-2xl bg-white shadow-2xl
                           [overflow-wrap:anywhere]">

                    <header
                        class="flex shrink-0 items-start justify-between
                               gap-5 border-b border-slate-200
                               px-5 py-4 sm:px-6">

                        <div class="min-w-0 flex-1">
                            <p
                                class="text-xs font-semibold uppercase
                                       tracking-wide text-red-600">
                                Finalizar seguimiento
                            </p>

                            <h2
                                id="titulo-modal-cerrar-caso"
                                class="mt-1 text-xl font-bold text-slate-900">
                                Cerrar caso clínico
                            </h2>
                        </div>

                        <button
                            type="button"
                            data-cerrar-modal-clinico
                            aria-label="Cerrar ventana"
                            class="shrink-0 rounded-lg p-2 text-slate-600
                                   transition hover:bg-slate-100
                                   hover:text-slate-900
                                   focus-visible:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-[#0D3B7F]
                                   focus-visible:ring-offset-2">
                            ✕
                        </button>
                    </header>

                    <form
                        method="POST"
                        action="{{ route(
                            'casos-clinicos.cerrar',
                            $casoParaCerrar
                        ) }}"
                        class="flex min-h-0 min-w-0 flex-1 flex-col">

                        @csrf
                        @method('PATCH')

                        <div
                            class="min-h-0 min-w-0 flex-1 space-y-5
                                   overflow-y-auto px-5 py-5 sm:px-6">

                            <div
                                class="rounded-xl border border-red-200
                                       bg-red-50 p-4">

                                <p class="font-semibold text-red-900">
                                    {{ $casoParaCerrar->nombre }}
                                </p>

                                <p
                                    id="advertencia-cierre-caso"
                                    class="mt-1 text-sm leading-6 text-red-700">
                                    El caso seguirá disponible para consulta,
                                    pero no aceptará nuevas evoluciones.
                                    No podrá reabrirse desde la aplicación.
                                </p>
                            </div>

                            <div>
                                <label
                                    for="motivo-cierre-caso"
                                    class="block text-sm font-semibold
                                           text-slate-800">
                                    Motivo de cierre
                                    <span
                                        aria-hidden="true"
                                        class="text-red-600">
                                        *
                                    </span>
                                </label>

                                <p
                                    id="ayuda-motivo-cierre-caso"
                                    class="mt-1 text-xs text-slate-600">
                                    Obligatorio. Escribe entre 10 y 5000 caracteres.
                                </p>

                                <textarea
                                    id="motivo-cierre-caso"
                                    name="motivo_cierre"
                                    rows="5"
                                    required
                                    minlength="10"
                                    maxlength="5000"
                                    aria-invalid="{{ $erroresCierreCaso->has('motivo_cierre') ? 'true' : 'false' }}"
                                    aria-describedby="ayuda-motivo-cierre-caso{{ $erroresCierreCaso->has('motivo_cierre') ? ' error-motivo-cierre-caso' : '' }}"
                                    placeholder="Ejemplo: Alta médica por cumplimiento de objetivos clínicos."
                                    class="mt-2 block w-full min-w-0
                                           rounded-xl border-slate-300
                                           text-sm shadow-sm
                                           focus:border-[#0D3B7F]
                                           focus:ring-[#0D3B7F]">{{ old('motivo_cierre') }}</textarea>

                                @error('motivo_cierre', 'cierreCasoClinico')
                                    <p
                                        id="error-motivo-cierre-caso"
                                        class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="flex cursor-pointer items-start
                                           gap-3 rounded-xl border
                                           border-slate-200 bg-slate-50 p-4">

                                    <input
                                        type="checkbox"
                                        name="confirmacion_cierre"
                                        value="1"
                                        required
                                        @checked(old('confirmacion_cierre'))
                                        aria-invalid="{{ $erroresCierreCaso->has('confirmacion_cierre') ? 'true' : 'false' }}"
                                        @if ($erroresCierreCaso->has('confirmacion_cierre'))
                                            aria-describedby="error-confirmacion-cierre-caso"
                                        @endif
                                        class="mt-0.5 shrink-0 rounded
                                               border-slate-300 text-[#0D3B7F]
                                               focus:ring-[#0D3B7F]">

                                    <span
                                        class="min-w-0 text-sm leading-6
                                               text-slate-700">
                                        Confirmo que el seguimiento debe cerrarse
                                        y que revisé el motivo registrado.
                                    </span>
                                </label>

                                @error('confirmacion_cierre', 'cierreCasoClinico')
                                    <p
                                        id="error-confirmacion-cierre-caso"
                                        class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <footer
                            class="flex shrink-0 flex-col gap-3
                                   border-t border-slate-200 bg-white
                                   px-5 py-4 sm:flex-row
                                   sm:justify-end sm:px-6">

                            <button
                                type="button"
                                data-cerrar-modal-clinico
                                data-modal-clinico-focus
                                class="w-full rounded-xl border border-slate-300
                                       px-5 py-2.5 text-sm font-semibold
                                       text-slate-700 transition
                                       hover:bg-slate-50
                                       focus-visible:outline-none
                                       focus-visible:ring-2
                                       focus-visible:ring-[#0D3B7F]
                                       focus-visible:ring-offset-2 sm:w-auto">
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                class="w-full rounded-xl bg-red-600
                                       px-5 py-2.5 text-sm font-semibold
                                       text-white transition hover:bg-red-700
                                       focus-visible:outline-none
                                       focus-visible:ring-2
                                       focus-visible:ring-red-700
                                       focus-visible:ring-offset-2 sm:w-auto">
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
                    function () {
                        abrirModalClinico('cerrar-caso');
                    }
                );
            </script>
        @endif
    @endcan
@endif