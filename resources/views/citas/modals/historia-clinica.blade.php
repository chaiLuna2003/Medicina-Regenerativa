@if ($puedeConsultarInformacionClinica)
    @php
        $historia =
            $cita->paciente?->historiaClinica;

        $heredofamiliares =
            collect(
                $historia
                    ?->antecedentesHeredofamiliares
                    ?->antecedentes
                    ?? []
            )->filter(
                fn($valor) => filled($valor)
            );

        $personalesPatologicos =
            collect(
                $historia
                    ?->antecedentesPersonalesPatologicos
                    ?->antecedentes
                    ?? []
            )->filter(
                fn($valor) => filled($valor)
            );

        $personalesNoPatologicos =
            collect(
                $historia
                    ?->antecedentesPersonalesNoPatologicos
                    ?->antecedentes
                    ?? []
            )->filter(
                fn($valor) => filled($valor)
            );

        $formatearValorClinico =
            function ($valor): string {
                if (is_bool($valor)) {
                    return $valor ? 'Sí' : 'No';
                }

                if (is_array($valor)) {
                    return implode(
                        ', ',
                        array_filter($valor)
                    );
                }

                return (string) $valor;
            };
    @endphp

    <div
        id="modal-clinico-historia-clinica"
        data-modal-clinico-panel="historia-clinica"
        class="fixed inset-0 z-50 hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-historia">
        <div
            class="absolute inset-0 bg-slate-950/60
                   backdrop-blur-sm"
            data-cerrar-modal-clinico></div>

        <div
            class="relative flex min-h-full items-center
                   justify-center p-3 sm:p-6">
            <div
                class="relative flex max-h-[92vh] w-full
                       max-w-5xl flex-col overflow-hidden
                       rounded-2xl bg-white shadow-2xl">

                {{-- Encabezado --}}
                <header
                    class="flex shrink-0 items-start
                           justify-between gap-5 border-b
                           border-slate-200 px-5 py-4
                           sm:px-6">
                    <div>
                        <p
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-[#0D3B7F]">
                            Expediente del paciente
                        </p>

                        <h2
                            id="titulo-modal-historia"
                            class="mt-1 text-xl font-bold
                                   text-slate-900">
                            Historia clínica
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $cita->paciente?->nombre }}
                            {{ $cita->paciente?->apellido }}
                        </p>
                    </div>

                    <button
                        type="button"
                        data-cerrar-modal-clinico
                        aria-label="Cerrar Historia clínica"
                        class="rounded-lg p-2 text-slate-400
                               transition hover:bg-slate-100
                               hover:text-slate-700">
                        ✕
                    </button>
                </header>

                {{-- Contenido --}}
                <div
                    class="min-h-0 flex-1 overflow-y-auto
                           bg-slate-50/70 p-5 sm:p-6">

                    @if (
                        ! $historia
                        && blank($cita->paciente?->alergias)
                    )
                        <div
                            class="rounded-2xl border border-dashed
                                   border-slate-300 bg-white
                                   px-6 py-12 text-center">
                            <p
                                class="font-semibold
                                       text-slate-700">
                                No existe Historia clínica registrada.
                            </p>

                            <p
                                class="mt-1 text-sm
                                       text-slate-500">
                                El expediente aparecerá aquí cuando
                                sea capturado.
                            </p>
                        </div>
                    @else
                        <div
                            class="grid gap-4
                                   md:grid-cols-2">

                            {{-- Resumen principal --}}
                            <section
                                class="rounded-2xl border
                                       border-slate-200 bg-white p-5">
                                <h3
                                    class="font-bold
                                           text-slate-900">
                                    Resumen clínico
                                </h3>

                                <dl class="mt-4 space-y-4">
                                    <div>
                                        <dt
                                            class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-slate-400">
                                            Patología base
                                        </dt>

                                        <dd
                                            class="mt-1 whitespace-pre-line
                                                   text-sm text-slate-700">
                                            {{ $historia?->patologia_base
                                                ?: 'No registrada' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt
                                            class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-slate-400">
                                            Padecimiento actual
                                        </dt>

                                        <dd
                                            class="mt-1 whitespace-pre-line
                                                   text-sm text-slate-700">
                                            {{ $historia?->padecimiento_actual
                                                ?: 'No registrado' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt
                                            class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-slate-400">
                                            Tratamientos actuales
                                        </dt>

                                        <dd
                                            class="mt-1 whitespace-pre-line
                                                   text-sm text-slate-700">
                                            {{ $historia?->tratamientos_actuales
                                                ?: 'No registrados' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt
                                            class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-slate-400">
                                            Prioridad de análisis médico
                                        </dt>

                                        <dd
                                            class="mt-1 whitespace-pre-line
                                                   text-sm text-slate-700">
                                            {{ $historia
                                                ?->prioridad_analisis_medico
                                                ?: 'No registrada' }}
                                        </dd>
                                    </div>
                                </dl>
                            </section>

                            {{-- Alertas --}}
                            <section
                                class="rounded-2xl border
                                       border-rose-200 bg-rose-50 p-5">
                                <h3
                                    class="font-bold
                                           text-rose-950">
                                    Alertas clínicas
                                </h3>

                                <p
                                    class="mt-4 text-xs font-semibold
                                           uppercase tracking-wide
                                           text-rose-500">
                                    Alergias
                                </p>

                                <p
                                    class="mt-2 whitespace-pre-line
                                           text-sm text-rose-900">
                                    {{ $cita->paciente?->alergias
                                        ?: 'No se registraron alergias.' }}
                                </p>
                            </section>
                        </div>

                        {{-- Antecedentes --}}
                        <div class="mt-4 grid gap-4 lg:grid-cols-3">
                            @foreach ([
                                [
                                    'titulo' => 'Heredofamiliares',
                                    'datos' => $heredofamiliares,
                                    'catalogo' =>
                                        \App\Models\AntecedenteHeredofamiliar::CAMPOS,
                                ],
                                [
                                    'titulo' => 'Personales patológicos',
                                    'datos' => $personalesPatologicos,
                                    'catalogo' =>
                                        \App\Models\AntecedentePersonalPatologico::CAMPOS,
                                ],
                                [
                                    'titulo' => 'Personales no patológicos',
                                    'datos' => $personalesNoPatologicos,
                                    'catalogo' =>
                                        \App\Models\AntecedentePersonalNoPatologico::CAMPOS,
                                ],
                            ] as $grupo)
                                <section
                                    class="rounded-2xl border
                                           border-slate-200 bg-white p-5">
                                    <h3
                                        class="font-bold
                                               text-slate-900">
                                        {{ $grupo['titulo'] }}
                                    </h3>

                                    @if ($grupo['datos']->isEmpty())
                                        <p
                                            class="mt-4 text-sm
                                                   text-slate-500">
                                            Sin antecedentes registrados.
                                        </p>
                                    @else
                                        <dl class="mt-4 space-y-3">
                                            @foreach (
                                                $grupo['datos']
                                                as $clave => $valor
                                            )
                                                <div>
                                                    <dt
                                                        class="text-xs
                                                               font-semibold
                                                               text-slate-500">
                                                        {{ $grupo['catalogo'][$clave]
                                                            ?? ucfirst(
                                                                str_replace(
                                                                    '_',
                                                                    ' ',
                                                                    $clave
                                                                )
                                                            ) }}
                                                    </dt>

                                                    <dd
                                                        class="mt-0.5 text-sm
                                                               text-slate-800">
                                                        {{ $formatearValorClinico(
                                                            $valor
                                                        ) }}
                                                    </dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    @endif
                                </section>
                            @endforeach
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
                        class="rounded-xl bg-slate-900
                               px-5 py-2.5 text-sm font-semibold
                               text-white transition
                               hover:bg-slate-700">
                        Cerrar
                    </button>
                </footer>
            </div>
        </div>
    </div>
@endif