@if ($puedeConsultarInformacionClinica)
    @php
        $valoracionEnfermeria = $cita->signoVital;

        $parametrosEnfermeria = $valoracionEnfermeria
            ? [
                [
                    'nombre' => 'Peso',
                    'valor' => $valoracionEnfermeria->peso !== null
                        ? number_format(
                            (float) $valoracionEnfermeria->peso,
                            2
                        ) . ' kg'
                        : 'No disponible',
                ],
                [
                    'nombre' => 'Estatura',
                    'valor' => $valoracionEnfermeria->estatura !== null
                        ? number_format(
                            (float) $valoracionEnfermeria->estatura,
                            2
                        ) . ' cm'
                        : 'No disponible',
                ],
                [
                    'nombre' => 'IMC',
                    'valor' => $valoracionEnfermeria->imc !== null
                        ? number_format(
                            (float) $valoracionEnfermeria->imc,
                            2
                        )
                        : 'No disponible',
                ],
                [
                    'nombre' => 'Temperatura',
                    'valor' => $valoracionEnfermeria->temperatura !== null
                        ? number_format(
                            (float) $valoracionEnfermeria->temperatura,
                            1
                        ) . ' °C'
                        : 'No disponible',
                ],
                [
                    'nombre' => 'Presión arterial',
                    'valor' =>
                        $valoracionEnfermeria->presion_sistolica !== null
                        && $valoracionEnfermeria->presion_diastolica !== null
                            ? $valoracionEnfermeria->presion_sistolica
                                . '/'
                                . $valoracionEnfermeria->presion_diastolica
                                . ' mmHg'
                            : 'No disponible',
                ],
                [
                    'nombre' => 'Frecuencia cardiaca',
                    'valor' => $valoracionEnfermeria->frecuencia_cardiaca !== null
                        ? $valoracionEnfermeria->frecuencia_cardiaca . ' lpm'
                        : 'No disponible',
                ],
                [
                    'nombre' => 'Frecuencia respiratoria',
                    'valor' => $valoracionEnfermeria->frecuencia_respiratoria !== null
                        ? $valoracionEnfermeria->frecuencia_respiratoria . ' rpm'
                        : 'No disponible',
                ],
                [
                    'nombre' => 'Saturación de oxígeno',
                    'valor' => $valoracionEnfermeria->saturacion_oxigeno !== null
                        ? $valoracionEnfermeria->saturacion_oxigeno . '%'
                        : 'No disponible',
                ],
                [
                    'nombre' => 'Glucosa',
                    'valor' => $valoracionEnfermeria->glucosa !== null
                        ? number_format(
                            (float) $valoracionEnfermeria->glucosa,
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
            class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
            data-cerrar-modal-clinico>
        </div>

        <div
            class="relative flex min-h-full items-center
                   justify-center p-3 sm:p-6">

            <div
                class="relative flex max-h-[92vh] w-full min-w-0
                       max-w-3xl flex-col overflow-hidden
                       rounded-2xl bg-white shadow-2xl
                       [overflow-wrap:anywhere]">

                <header
                    class="flex shrink-0 items-start justify-between
                           gap-5 border-b border-slate-200
                           px-5 py-4 sm:px-6">

                    <div class="min-w-0 flex-1">
                        <p
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-cyan-700">
                            Registro de la cita
                        </p>

                        <h2
                            id="titulo-modal-enfermeria"
                            class="mt-1 text-xl font-bold text-slate-900">
                            Valoración de enfermería
                        </h2>

                        <p class="mt-1 text-sm text-slate-600">
                            {{ $cita->fecha->format('d/m/Y') }}
                            ·
                            {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                        </p>
                    </div>

                    <button
                        type="button"
                        data-cerrar-modal-clinico
                        aria-label="Cerrar valoración"
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

                <div
                    class="min-h-0 min-w-0 flex-1 overflow-y-auto
                           bg-slate-50/70 p-5 sm:p-6">

                    @if (! $valoracionEnfermeria)
                        <div
                            class="rounded-2xl border border-dashed
                                   border-cyan-300 bg-white
                                   px-6 py-12 text-center">

                            <p class="font-semibold text-cyan-950">
                                Sin valoración de enfermería
                            </p>

                            <p class="mt-1 text-sm text-cyan-700">
                                Enfermería todavía no ha registrado
                                signos vitales para esta cita.
                            </p>
                        </div>
                    @else
                        <dl
                            class="grid gap-3
                                   sm:grid-cols-2 lg:grid-cols-3">

                            @foreach ($parametrosEnfermeria as $parametro)
                                <div
                                    class="min-w-0 rounded-xl border
                                           border-cyan-100 bg-white p-4">

                                    <dt
                                        class="text-xs font-semibold uppercase
                                               tracking-wide text-cyan-700">
                                        {{ $parametro['nombre'] }}
                                    </dt>

                                    <dd
                                        class="mt-2 text-lg font-bold
                                               text-slate-900">
                                        {{ $parametro['valor'] }}
                                    </dd>
                                </div>
                            @endforeach
                        </dl>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <section
                                class="min-w-0 rounded-xl border
                                       border-slate-200 bg-white p-4">

                                <h3
                                    class="text-xs font-semibold uppercase
                                           tracking-wide text-slate-600">
                                    Registrado por
                                </h3>

                                <p
                                    class="mt-2 font-semibold text-slate-900">
                                    {{ $valoracionEnfermeria->enfermero?->name
                                        ?? 'Usuario no disponible' }}
                                </p>

                                <p class="mt-1 text-sm text-slate-600">
                                    {{ $valoracionEnfermeria->created_at
                                        ?->format('d/m/Y, h:i A')
                                        ?? 'Fecha no disponible' }}
                                </p>
                            </section>

                            <section
                                class="min-w-0 rounded-xl border
                                       border-slate-200 bg-white p-4">

                                <h3
                                    class="text-xs font-semibold uppercase
                                           tracking-wide text-slate-600">
                                    Observaciones
                                </h3>

                                <p
                                    class="mt-2 whitespace-pre-line
                                           text-sm text-slate-700">{{ $valoracionEnfermeria->observaciones ?: 'Sin observaciones.' }}</p>
                            </section>
                        </div>
                    @endif
                </div>

                <footer
                    class="flex shrink-0 justify-end border-t
                           border-slate-200 bg-white
                           px-5 py-4 sm:px-6">

                    <button
                        type="button"
                        data-cerrar-modal-clinico
                        class="w-full rounded-xl bg-slate-900
                               px-5 py-2.5 text-sm font-semibold
                               text-white transition hover:bg-slate-700
                               focus-visible:outline-none
                               focus-visible:ring-2
                               focus-visible:ring-slate-900
                               focus-visible:ring-offset-2 sm:w-auto">
                        Cerrar
                    </button>
                </footer>
            </div>
        </div>
    </div>
@endif