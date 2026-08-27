@php
    $ginecoobstetricos = $pacientes
        ->historiaClinica
        ?->antecedenteGinecoobstetrico;

    $resumenGineco = [
        'Menarca' => $ginecoobstetricos?->edad_menarca
            ? $ginecoobstetricos->edad_menarca . ' años'
            : null,

        'Ritmo menstrual' =>
            $ginecoobstetricos?->ritmo_menstrual,

        'Última menstruación' =>
            $ginecoobstetricos
                ?->fecha_ultima_menstruacion
                ?->format('d/m/Y'),

        'Gestas' => $ginecoobstetricos?->gestas,
        'Partos' => $ginecoobstetricos?->partos,
        'Cesáreas' => $ginecoobstetricos?->cesareas,
        'Abortos' => $ginecoobstetricos?->abortos,

        'Embarazos ectópicos' =>
            $ginecoobstetricos?->embarazos_ectopicos,

        'Hijos vivos' =>
            $ginecoobstetricos?->hijos_vivos,

        'Embarazo actual' =>
            is_null($ginecoobstetricos?->embarazo_actual)
                ? null
                : (
                    $ginecoobstetricos->embarazo_actual
                        ? 'Sí'
                        : 'No'
                ),

        'Método anticonceptivo' =>
            $ginecoobstetricos?->metodo_anticonceptivo,

        'Menopausia' =>
            is_null($ginecoobstetricos?->menopausia)
                ? null
                : (
                    $ginecoobstetricos->menopausia
                        ? 'Sí'
                        : 'No'
                ),
    ];

    $camposMenstruales = [
        [
            'campo' => 'edad_menarca',
            'etiqueta' => 'Edad de menarca',
            'tipo' => 'number',
            'min' => 5,
            'max' => 25,
        ],
        [
            'campo' => 'ritmo_menstrual',
            'etiqueta' => 'Ritmo menstrual',
            'placeholder' => 'Ej. 28 x 5, regular',
        ],
        [
            'campo' => 'duracion_menstruacion_dias',
            'etiqueta' => 'Duración (días)',
            'tipo' => 'number',
            'min' => 1,
            'max' => 30,
        ],
        [
            'campo' => 'fecha_ultima_menstruacion',
            'etiqueta' => 'Última menstruación',
            'tipo' => 'date',
        ],
        [
            'campo' => 'edad_inicio_vida_sexual',
            'etiqueta' => 'Inicio de vida sexual',
            'tipo' => 'number',
            'min' => 5,
            'max' => 100,
        ],
        [
            'campo' => 'numero_parejas_sexuales',
            'etiqueta' => 'Número de parejas sexuales',
            'tipo' => 'number',
            'min' => 0,
            'max' => 1000,
        ],
        [
            'campo' => 'metodo_anticonceptivo',
            'etiqueta' => 'Método anticonceptivo',
            'placeholder' => 'Método actual o ninguno',
        ],
    ];

    $camposObstetricos = [
        'gestas' => 'Gestas',
        'partos' => 'Partos',
        'cesareas' => 'Cesáreas',
        'abortos' => 'Abortos',
        'embarazos_ectopicos' => 'Ectópicos',
        'hijos_vivos' => 'Hijos vivos',
    ];
@endphp

<span
    id="estado-validacion-ginecoobstetricos"
    class="hidden"
    data-tiene-errores="{{
        $errors->ginecoobstetricos->any()
            ? 'true'
            : 'false'
    }}">
</span>

{{-- ===================================================== --}}
{{-- TARJETA --}}
{{-- ===================================================== --}}

<details
    class="group overflow-hidden rounded-2xl
           border border-rose-200 bg-white shadow-sm">

    <summary
        class="flex cursor-pointer list-none
               items-center justify-between
               gap-4 px-6 py-5">

        <div>
            <h3 class="font-semibold text-slate-900">
                Antecedentes ginecoobstétricos
            </h3>

            <p class="mt-1 text-xs text-slate-400">
                Historia menstrual, obstétrica
                y estudios preventivos
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
                        abrirModalGinecoobstetricos();
                    "
                    class="rounded-xl bg-rose-600
                           px-4 py-2 text-xs font-semibold
                           text-white shadow-sm transition
                           hover:bg-rose-700">

                    {{ $ginecoobstetricos
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

        @if ($ginecoobstetricos)

            <div
                class="grid grid-cols-1
                       sm:grid-cols-2 lg:grid-cols-4">

                @foreach ($resumenGineco as $etiqueta => $valor)

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

                            {{
                                filled($valor) || $valor === 0
                                    ? $valor
                                    : 'No registrado'
                            }}
                        </p>
                    </div>

                @endforeach
            </div>

            @if (filled($ginecoobstetricos->observaciones))
                <div class="p-5">

                    <p class="text-xs font-medium text-slate-400">
                        Observaciones
                    </p>

                    <p
                        class="mt-2 whitespace-pre-line
                               text-sm text-slate-700">
                        {{ $ginecoobstetricos->observaciones }}
                    </p>
                </div>
            @endif

        @else

            <div class="px-6 py-10 text-center">

                <p class="text-sm font-semibold text-slate-700">
                    Sin antecedentes ginecoobstétricos
                </p>

                <p class="mt-1 text-sm text-slate-400">
                    Registra la historia menstrual
                    y obstétrica de la paciente.
                </p>
            </div>

        @endif
    </div>
</details>

{{-- ===================================================== --}}
{{-- MODAL --}}
{{-- ===================================================== --}}

@if (
    request()->user()->isAdmin()
    || request()->user()->isMedico()
)
    <div
        id="modal-ginecoobstetricos"
        class="fixed inset-0 z-50 hidden
               items-center justify-center
               bg-slate-950/60 p-4"
        aria-hidden="true"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-ginecoobstetricos">

        <div
            class="flex max-h-[90vh] w-full max-w-5xl
                   flex-col overflow-hidden rounded-2xl
                   bg-white shadow-2xl">

            <div
                class="flex items-start justify-between
                       border-b border-slate-200
                       px-6 py-5">

                <div>
                    <h2
                        id="titulo-modal-ginecoobstetricos"
                        class="text-lg font-semibold
                               text-slate-900">
                        Antecedentes ginecoobstétricos
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Historia ginecológica y obstétrica.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalGinecoobstetricos()"
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
                    . 'ginecoobstetricos.update',
                    $pacientes
                ) }}"
                class="flex min-h-0 flex-1 flex-col">

                @csrf
                @method('PUT')

                <div
                    class="flex-1 space-y-8
                           overflow-y-auto p-6">

                    @if ($errors->ginecoobstetricos->any())

                        <div
                            class="rounded-xl border
                                   border-red-200 bg-red-50
                                   px-4 py-3 text-sm
                                   text-red-700">

                            <p class="font-semibold">
                                Revisa los campos señalados.
                            </p>

                            <ul class="mt-2 list-disc pl-5">
                                @foreach (
                                    $errors
                                        ->ginecoobstetricos
                                        ->all()
                                    as $error
                                )
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>

                    @endif

                    {{-- Historia menstrual --}}

                    <section>
                        <h3 class="text-sm font-semibold text-slate-900">
                            Historia menstrual
                        </h3>

                        <div
                            class="mt-4 grid grid-cols-1 gap-4
                                   sm:grid-cols-2 lg:grid-cols-4">

                            @foreach ($camposMenstruales as $configuracion)

                                @include(
                                    'pacientes.partials.gineco-input',
                                    $configuracion
                                )

                            @endforeach
                        </div>
                    </section>

                    {{-- Historia obstétrica --}}

                    <section>
                        <h3 class="text-sm font-semibold text-slate-900">
                            Historia obstétrica
                        </h3>

                        <div
                            class="mt-4 grid grid-cols-2 gap-4
                                   sm:grid-cols-3 lg:grid-cols-6">

                            @foreach (
                                $camposObstetricos
                                as $campo => $etiqueta
                            )
                                @include(
                                    'pacientes.partials.gineco-input',
                                    [
                                        'campo' => $campo,
                                        'etiqueta' => $etiqueta,
                                        'tipo' => 'number',
                                        'min' => 0,
                                        'max' => 100,
                                    ]
                                )
                            @endforeach
                        </div>

                        <div
                            class="mt-4 grid grid-cols-1 gap-4
                                   sm:grid-cols-3">

                            @foreach (
                                [
                                    'embarazo_actual' =>
                                        'Embarazo actual',

                                    'menopausia' =>
                                        'Menopausia',
                                ]
                                as $campo => $etiqueta
                            )
                                @php
                                    $valorBooleano = old(
                                        $campo,
                                        is_null(
                                            $ginecoobstetricos
                                                ?->{$campo}
                                        )
                                            ? null
                                            : (int) $ginecoobstetricos
                                                ->{$campo}
                                    );
                                @endphp

                                <div>
                                    <label
                                        for="gineco_{{ $campo }}"
                                        class="mb-1.5 block
                                               text-xs font-semibold
                                               text-slate-600">
                                        {{ $etiqueta }}
                                    </label>

                                    <select
                                        id="gineco_{{ $campo }}"
                                        name="{{ $campo }}"
                                        class="w-full rounded-xl
                                               border-slate-300
                                               text-sm shadow-sm
                                               focus:border-rose-500
                                               focus:ring-rose-500">

                                        <option value="">
                                            No especificado
                                        </option>

                                        <option
                                            value="1"
                                            @selected(
                                                (string) $valorBooleano
                                                === '1'
                                            )>
                                            Sí
                                        </option>

                                        <option
                                            value="0"
                                            @selected(
                                                (string) $valorBooleano
                                                === '0'
                                            )>
                                            No
                                        </option>
                                    </select>
                                </div>
                            @endforeach

                            @include(
                                'pacientes.partials.gineco-input',
                                [
                                    'campo' => 'edad_menopausia',
                                    'etiqueta' =>
                                        'Edad de menopausia',
                                    'tipo' => 'number',
                                    'min' => 20,
                                    'max' => 100,
                                ]
                            )
                        </div>
                    </section>

                    {{-- Prevención --}}

                    <section>
                        <h3 class="text-sm font-semibold text-slate-900">
                            Prevención y antecedentes
                        </h3>

                        <div
                            class="mt-4 grid grid-cols-1 gap-4
                                   sm:grid-cols-2">

                            @include(
                                'pacientes.partials.gineco-input',
                                [
                                    'campo' =>
                                        'fecha_ultimo_papanicolaou',

                                    'etiqueta' =>
                                        'Último Papanicolaou',

                                    'tipo' => 'date',
                                ]
                            )

                            @include(
                                'pacientes.partials.gineco-input',
                                [
                                    'campo' =>
                                        'fecha_ultima_mastografia',

                                    'etiqueta' =>
                                        'Última mastografía',

                                    'tipo' => 'date',
                                ]
                            )

                            @include(
                                'pacientes.partials.gineco-textarea',
                                [
                                    'campo' =>
                                        'resultado_papanicolaou',

                                    'etiqueta' =>
                                        'Resultado de Papanicolaou',
                                ]
                            )

                            @include(
                                'pacientes.partials.gineco-textarea',
                                [
                                    'campo' =>
                                        'resultado_mastografia',

                                    'etiqueta' =>
                                        'Resultado de mastografía',
                                ]
                            )

                            @include(
                                'pacientes.partials.gineco-textarea',
                                [
                                    'campo' =>
                                        'infecciones_transmision_sexual',

                                    'etiqueta' =>
                                        'Infecciones de transmisión sexual',
                                ]
                            )

                            @include(
                                'pacientes.partials.gineco-textarea',
                                [
                                    'campo' => 'observaciones',
                                    'etiqueta' => 'Observaciones',
                                ]
                            )
                        </div>
                    </section>
                </div>

                <div
                    class="flex justify-end gap-3
                           border-t border-slate-200
                           bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="cerrarModalGinecoobstetricos()"
                        class="rounded-xl border
                               border-slate-300 bg-white
                               px-5 py-2.5 text-sm
                               font-semibold text-slate-700
                               transition hover:bg-slate-100">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-rose-600
                               px-5 py-2.5 text-sm
                               font-semibold text-white
                               shadow-sm transition
                               hover:bg-rose-700">
                        Guardar antecedentes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif