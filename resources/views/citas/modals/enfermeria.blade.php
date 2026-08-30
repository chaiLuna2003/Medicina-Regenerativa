@if ($puedeConsultarInformacionClinica)
    @php
        $valoracionEnfermeria =
            $cita->signoVital;

        $parametrosEnfermeria =
            $valoracionEnfermeria
                ? [
                    [
                        'nombre' => 'Peso',
                        'valor' => number_format(
                            (float) $valoracionEnfermeria->peso,
                            2
                        ) . ' kg',
                    ],
                    [
                        'nombre' => 'Estatura',
                        'valor' => number_format(
                            (float) $valoracionEnfermeria->estatura,
                            2
                        ) . ' cm',
                    ],
                    [
                        'nombre' => 'IMC',
                        'valor' => $valoracionEnfermeria->imc
                            !== null
                                ? number_format(
                                    $valoracionEnfermeria->imc,
                                    2
                                )
                                : 'No disponible',
                    ],
                    [
                        'nombre' => 'Temperatura',
                        'valor' => $valoracionEnfermeria->temperatura
                            !== null
                                ? number_format(
                                    (float) $valoracionEnfermeria
                                        ->temperatura,
                                    1
                                ) . ' °C'
                                : 'No disponible',
                    ],
                    [
                        'nombre' => 'Presión arterial',
                        'valor' =>
                            $valoracionEnfermeria
                                ->presion_sistolica !== null
                            && $valoracionEnfermeria
                                ->presion_diastolica !== null
                                ? $valoracionEnfermeria
                                    ->presion_sistolica
                                    . '/'
                                    . $valoracionEnfermeria
                                        ->presion_diastolica
                                    . ' mmHg'
                                : 'No disponible',
                    ],
                    [
                        'nombre' => 'Frecuencia cardiaca',
                        'valor' =>
                            $valoracionEnfermeria
                                ->frecuencia_cardiaca !== null
                                ? $valoracionEnfermeria
                                    ->frecuencia_cardiaca
                                    . ' lpm'
                                : 'No disponible',
                    ],
                    [
                        'nombre' => 'Frecuencia respiratoria',
                        'valor' =>
                            $valoracionEnfermeria
                                ->frecuencia_respiratoria !== null
                                ? $valoracionEnfermeria
                                    ->frecuencia_respiratoria
                                    . ' rpm'
                                : 'No disponible',
                    ],
                    [
                        'nombre' => 'Saturación de oxígeno',
                        'valor' =>
                            $valoracionEnfermeria
                                ->saturacion_oxigeno !== null
                                ? $valoracionEnfermeria
                                    ->saturacion_oxigeno
                                    . '%'
                                : 'No disponible',
                    ],
                    [
                        'nombre' => 'Glucosa',
                        'valor' => $valoracionEnfermeria->glucosa
                            !== null
                                ? number_format(
                                    (float) $valoracionEnfermeria
                                        ->glucosa,
                                    2
                                ) . ' mg/dL'
                                : 'No disponible',
                    ],
                ]
                : [];
    @endphp

    <div
        id="modal-clinico-enfermeria"
        data-modal-clinico-panel="enfermeria"
        class="fixed inset-0 z-50 hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-enfermeria">
        <div
            class="absolute inset-0 bg-slate-950/60
                   backdrop-blur-sm"
            data-cerrar-modal-clinico></div>

        <div
            class="relative flex min-h-full items-center
                   justify-center p-3 sm:p-6">
            <div
                class="relative flex max-h-[92vh] w-full
                       max-w-3xl flex-col overflow-hidden
                       rounded-2xl bg-white shadow-2xl">

                <header
                    class="flex shrink-0 items-start
                           justify-between gap-5 border-b
                           border-slate-200 px-5 py-4 sm:px-6">
                    <div>
                        <p
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-cyan-700">
                            Registro de la cita
                        </p>

                        <h2
                            id="titulo-modal-enfermeria"
                            class="mt-1 text-xl font-bold
                                   text-slate-900">
                            Valoración de enfermería
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $cita->fecha->format('d/m/Y') }}
                            ·
                            {{ \Carbon\Carbon::parse(
                                $cita->hora
                            )->format('h:i A') }}
                        </p>
                    </div>

                    <button
                        type="button"
                        data-cerrar-modal-clinico
                        aria-label="Cerrar valoración"
                        class="rounded-lg p-2 text-slate-400
                               transition hover:bg-slate-100
                               hover:text-slate-700">
                        ✕
                    </button>
                </header>

                <div
                    class="min-h-0 flex-1 overflow-y-auto
                           bg-slate-50/70 p-5 sm:p-6">
                    @if (! $valoracionEnfermeria)
                        <div
                            class="rounded-2xl border border-dashed
                                   border-cyan-300 bg-white
                                   px-6 py-12 text-center">
                            <p
                                class="font-semibold
                                       text-cyan-950">
                                Sin valoración de enfermería
                            </p>

                            <p
                                class="mt-1 text-sm
                                       text-cyan-700">
                                Enfermería todavía no ha registrado
                                signos vitales para esta cita.
                            </p>
                        </div>
                    @else
                        <div
                            class="grid gap-3
                                   sm:grid-cols-2 lg:grid-cols-3">
                            @foreach (
                                $parametrosEnfermeria
                                as $parametro
                            )
                                <div
                                    class="rounded-xl border
                                           border-cyan-100
                                           bg-white p-4">
                                    <p
                                        class="text-xs font-semibold
                                               uppercase tracking-wide
                                               text-cyan-600">
                                        {{ $parametro['nombre'] }}
                                    </p>

                                    <p
                                        class="mt-2 text-lg font-bold
                                               text-slate-900">
                                        {{ $parametro['valor'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <div
                            class="mt-4 grid gap-4
                                   sm:grid-cols-2">
                            <section
                                class="rounded-xl border
                                       border-slate-200 bg-white p-4">
                                <p
                                    class="text-xs font-semibold
                                           uppercase tracking-wide
                                           text-slate-400">
                                    Registrado por
                                </p>

                                <p
                                    class="mt-2 font-semibold
                                           text-slate-900">
                                    {{ $valoracionEnfermeria
                                        ->enfermero
                                        ?->name
                                        ?? 'Usuario no disponible' }}
                                </p>

                                <p
                                    class="mt-1 text-sm
                                           text-slate-500">
                                    {{ $valoracionEnfermeria
                                        ->created_at
                                        ->format('d/m/Y, h:i A') }}
                                </p>
                            </section>

                            <section
                                class="rounded-xl border
                                       border-slate-200 bg-white p-4">
                                <p
                                    class="text-xs font-semibold
                                           uppercase tracking-wide
                                           text-slate-400">
                                    Observaciones
                                </p>

                                <p
                                    class="mt-2 whitespace-pre-line
                                           text-sm text-slate-700">
                                    {{ $valoracionEnfermeria
                                        ->observaciones
                                        ?: 'Sin observaciones.' }}
                                </p>
                            </section>
                        </div>
                    @endif
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