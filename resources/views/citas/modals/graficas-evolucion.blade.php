@if (
    $puedeConsultarInformacionClinica
    && $cita->evolucionClinica?->casoClinico
)
    @php
        $graficasDisponibles = [
            [
                'clave' => 'peso_imc',
                'titulo' => 'Peso e IMC',
                'descripcion' =>
                    'Cambios registrados durante el seguimiento.',
            ],
            [
                'clave' => 'presion',
                'titulo' => 'Presión arterial',
                'descripcion' =>
                    'Comparación sistólica y diastólica.',
            ],
            [
                'clave' => 'frecuencia_cardiaca',
                'titulo' => 'Frecuencia cardiaca',
                'descripcion' =>
                    'Latidos por minuto registrados.',
            ],
            [
                'clave' => 'frecuencia_respiratoria',
                'titulo' => 'Frecuencia respiratoria',
                'descripcion' =>
                    'Respiraciones por minuto.',
            ],
            [
                'clave' => 'saturacion_oxigeno',
                'titulo' => 'Saturación de oxígeno',
                'descripcion' =>
                    'Porcentaje de oxígeno registrado.',
            ],
            [
                'clave' => 'temperatura',
                'titulo' => 'Temperatura',
                'descripcion' =>
                    'Evolución de la temperatura corporal.',
            ],
            [
                'clave' => 'glucosa',
                'titulo' => 'Glucosa',
                'descripcion' =>
                    'Registros de glucosa disponibles.',
            ],
            [
                'clave' => 'estatura',
                'titulo' => 'Estatura',
                'descripcion' =>
                    'Histórico de estatura registrado.',
            ],
        ];
    @endphp

    <div
        id="modal-clinico-graficas-evolucion"
        data-modal-clinico-panel="graficas-evolucion"
        class="fixed inset-0 z-50 hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-graficas">
        <div
            class="absolute inset-0 bg-slate-950/60
                   backdrop-blur-sm"
            data-cerrar-modal-clinico></div>

        <div
            class="relative flex min-h-full items-center
                   justify-center p-2 sm:p-5">
            <div
                class="relative flex max-h-[96vh] w-full
                       max-w-7xl flex-col overflow-hidden
                       rounded-2xl bg-white shadow-2xl">

                <header
                    class="flex shrink-0 items-start
                           justify-between gap-5 border-b
                           border-slate-200 px-5 py-4 sm:px-6">
                    <div>
                        <p
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-[#0D3B7F]">
                            Evolución histórica
                        </p>

                        <h2
                            id="titulo-modal-graficas"
                            class="mt-1 text-xl font-bold
                                   text-slate-900">
                            Gráficas de enfermería
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $cita->evolucionClinica
                                ->casoClinico
                                ->nombre }}
                            · únicamente citas de este caso
                        </p>
                    </div>

                    <button
                        type="button"
                        data-cerrar-modal-clinico
                        aria-label="Cerrar gráficas"
                        class="rounded-lg p-2 text-slate-400
                               transition hover:bg-slate-100
                               hover:text-slate-700">
                        ✕
                    </button>
                </header>

                <div
                    class="min-h-0 flex-1 overflow-y-auto
                           bg-slate-50/70 p-4 sm:p-6">

                    <div
                        class="mb-5 rounded-xl border
                               border-blue-200 bg-blue-50
                               px-4 py-3 text-sm text-blue-900">
                        Cada punto corresponde a una cita asociada
                        con este seguimiento. Las consultas ajenas
                        al caso no se incluyen.
                    </div>

                    <div
                        class="grid gap-4 xl:grid-cols-2">
                        @foreach (
                            $graficasDisponibles
                            as $grafica
                        )
                            <article
                                class="rounded-2xl border
                                       border-slate-200 bg-white
                                       p-4 shadow-sm">
                                <div>
                                    <h3
                                        class="font-bold
                                               text-slate-900">
                                        {{ $grafica['titulo'] }}
                                    </h3>

                                    <p
                                        class="mt-1 text-xs
                                               text-slate-500">
                                        {{ $grafica['descripcion'] }}
                                    </p>
                                </div>

                                <div
                                    id="grafica-evolucion-{{ $grafica['clave'] }}"
                                    data-grafica-evolucion="{{ $grafica['clave'] }}"
                                    class="mt-3 min-h-[280px]">
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <footer
                    class="flex shrink-0 justify-end border-t
                           border-slate-200 bg-white px-5 py-4
                           sm:px-6">
                    <button
                        type="button"
                        data-cerrar-modal-clinico
                        class="rounded-xl bg-slate-900 px-5
                               py-2.5 text-sm font-semibold
                               text-white transition
                               hover:bg-slate-700">
                        Cerrar
                    </button>
                </footer>
            </div>
        </div>
    </div>
@endif