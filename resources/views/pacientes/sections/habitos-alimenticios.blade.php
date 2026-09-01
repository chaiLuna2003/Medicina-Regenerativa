@php
    $habitoAlimenticio = $pacientes
        ->historiaClinica
        ?->habitoAlimenticio;

    $comidasRegistradas =
        $habitoAlimenticio?->comidas ?? [];

    $alimentosRegistrados =
        $habitoAlimenticio?->alimentos ?? [];
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
                Hábitos alimenticios
            </h3>

            <p class="mt-1 text-xs text-slate-400">
                Comidas habituales, alimentos,
                frecuencia y cantidad de consumo
            </p>
        </div>

        <div class="flex items-center gap-3">

            @if (request()->user()->isMedico())
                <button
                    type="button"
                    onclick="
                        event.preventDefault();
                        event.stopPropagation();
                        abrirModalHabitosAlimenticios();
                    "
                    class="inline-flex items-center
                           justify-center rounded-xl
                           bg-indigo-600 px-4 py-2
                           text-xs font-semibold
                           text-white shadow-sm
                           transition hover:bg-indigo-700">

                    {{ $habitoAlimenticio
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

        @if ($habitoAlimenticio)

            {{-- Comidas realizadas --}}

            <div class="border-b border-slate-100 p-5">

                <p
                    class="mb-3 text-xs font-semibold
                           uppercase tracking-wide
                           text-slate-400">
                    Comidas realizadas habitualmente
                </p>

                <div class="flex flex-wrap gap-2">

                    @foreach (
                        $comidasHabitosAlimenticios
                        as $clave => $etiqueta
                    )

                        @php
                            $realizaComida = (bool) data_get(
                                $comidasRegistradas,
                                $clave,
                                false
                            );
                        @endphp

                        <span
                            @class([
                                'inline-flex items-center rounded-full px-3 py-1',
                                'text-xs font-semibold',

                                'bg-emerald-50 text-emerald-700' =>
                                    $realizaComida,

                                'bg-slate-100 text-slate-400' =>
                                    ! $realizaComida,
                            ])>

                            {{ $etiqueta }}:
                            {{ $realizaComida ? 'Sí' : 'No' }}
                        </span>

                    @endforeach
                </div>
            </div>

            {{-- Frecuencia de alimentos --}}

            <div
                class="grid grid-cols-1 gap-0
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4">

                @foreach (
                    $camposHabitosAlimenticios
                    as $clave => $etiqueta
                )

                    @php
                        $valor = data_get(
                            $alimentosRegistrados,
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
                    Sin hábitos alimenticios registrados
                </p>

                <p class="mt-1 text-sm text-slate-400">
                    Registra las comidas habituales y la frecuencia
                    de consumo de alimentos.
                </p>
            </div>

        @endif
    </div>
</details>