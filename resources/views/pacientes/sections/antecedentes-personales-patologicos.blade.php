@php
    $personalesPatologicos = $pacientes
        ->historiaClinica
        ?->antecedentesPersonalesPatologicos;

    $valoresPersonalesPatologicos =
        $personalesPatologicos?->antecedentes
        ?? [];
@endphp

<details
    class="group overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">

    <summary
        class="flex cursor-pointer list-none
               items-center justify-between
               gap-4 px-6 py-5">

        <div>
            <h3 class="font-semibold text-slate-900">
                Antecedentes personales patológicos
            </h3>

            <p class="mt-1 text-xs text-slate-400">
                Enfermedades y eventos médicos previos del paciente
            </p>
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
                        abrirModalPersonalesPatologicos();
                    "
                    class="inline-flex items-center
                           justify-center rounded-xl
                           bg-amber-600 px-4 py-2
                           text-xs font-semibold
                           text-white shadow-sm
                           transition hover:bg-amber-700">

                    {{ $personalesPatologicos
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

        @if ($personalesPatologicos)

            <div
                class="grid grid-cols-1 gap-0
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4">

                @foreach (
                    $camposPersonalesPatologicos
                    as $clave => $etiqueta
                )

                    @php
                        $valor = data_get(
                            $valoresPersonalesPatologicos,
                            $clave
                        );
                    @endphp

                    <div
                        class="border-b border-r
                               border-slate-100 p-4">

                        <p class="text-xs font-medium text-slate-400">
                            {{ $etiqueta }}
                        </p>

                        <p
                            class="mt-1 whitespace-pre-line
                                   text-sm font-semibold
                                   text-slate-800">

                            {{ filled($valor)
                                ? $valor
                                : 'No registrado' }}
                        </p>
                    </div>

                @endforeach
            </div>

        @else

            <div class="px-6 py-10 text-center">

                <p class="text-sm font-semibold text-slate-700">
                    Sin antecedentes personales patológicos
                </p>

                <p class="mt-1 text-sm text-slate-400">
                    Registra las enfermedades y eventos médicos previos.
                </p>
            </div>

        @endif
    </div>
</details>