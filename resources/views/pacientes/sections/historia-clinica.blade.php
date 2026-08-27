<details
    class="group overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">

    <summary
        class="flex cursor-pointer list-none
               items-center justify-between
               gap-4 px-6 py-5">

        <div class="flex items-center gap-3">

            <div
                class="flex h-10 w-10 items-center
                       justify-center rounded-xl
                       bg-cyan-50 text-cyan-700">

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
                        d="M9 12h6m-6 4h6M7 3h7l4 4v14H7z" />
                </svg>
            </div>

            <div>
                <h3 class="font-semibold text-slate-900">
                    Historia clínica
                </h3>

                <p class="text-xs text-slate-400">
                    Resumen clínico principal del paciente
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">

            @if (
                request()->user()->isAdmin()
                || request()->user()->isMedico()
            )
                <button
                    type="button"
                    onclick="
                        event.preventDefault();
                        event.stopPropagation();
                        abrirModalHistoriaClinica();
                    "
                    class="inline-flex items-center
                           justify-center rounded-xl
                           bg-cyan-600 px-4 py-2
                           text-xs font-semibold
                           text-white shadow-sm
                           transition hover:bg-cyan-700">

                    {{ $pacientes->historiaClinica
                        ? 'Editar'
                        : 'Registrar' }}
                </button>
            @endif

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-400
                       transition duration-200
                       group-open:rotate-180"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </summary>

    <div class="border-t border-slate-100">

        @if ($pacientes->historiaClinica)

            <div class="grid grid-cols-1 gap-0 lg:grid-cols-2">

                {{-- Patología base --}}

                <article
                    class="border-b border-slate-100
                           p-6 lg:border-r">

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-cyan-700">
                        Patología base
                    </p>

                    <div
                        class="mt-3 whitespace-pre-line
                               text-sm leading-6
                               text-slate-700">{{ $pacientes
                            ->historiaClinica
                            ->patologia_base
                            ?: 'Sin información registrada.' }}</div>
                </article>

                {{-- Padecimiento actual --}}

                <article class="border-b border-slate-100 p-6">

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-cyan-700">
                        Padecimiento actual
                    </p>

                    <div
                        class="mt-3 whitespace-pre-line
                               text-sm leading-6
                               text-slate-700">{{ $pacientes
                            ->historiaClinica
                            ->padecimiento_actual
                            ?: 'Sin información registrada.' }}</div>
                </article>

                {{-- Tratamientos actuales --}}

                <article
                    class="border-b border-slate-100
                           p-6 lg:border-b-0 lg:border-r">

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-cyan-700">
                        Tratamientos actuales
                    </p>

                    <div
                        class="mt-3 whitespace-pre-line
                               text-sm leading-6
                               text-slate-700">{{ $pacientes
                            ->historiaClinica
                            ->tratamientos_actuales
                            ?: 'Sin información registrada.' }}</div>
                </article>

                {{-- Prioridad y análisis --}}

                <article class="p-6">

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-cyan-700">
                        Prioridad y análisis médico
                    </p>

                    <div
                        class="mt-3 whitespace-pre-line
                               text-sm leading-6
                               text-slate-700">{{ $pacientes
                            ->historiaClinica
                            ->prioridad_analisis_medico
                            ?: 'Sin información registrada.' }}</div>
                </article>
            </div>

        @else

            <div class="px-6 py-12 text-center">

                <div
                    class="mx-auto flex h-12 w-12
                           items-center justify-center
                           rounded-2xl bg-slate-100
                           text-slate-400">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h6m-6 4h6M7 3h7l4 4v14H7z" />
                    </svg>
                </div>

                <p class="mt-4 text-sm font-semibold text-slate-700">
                    Sin historia clínica registrada
                </p>

                <p class="mt-1 text-sm text-slate-400">
                    Registra el primer resumen clínico del paciente.
                </p>
            </div>

        @endif
    </div>
</details>