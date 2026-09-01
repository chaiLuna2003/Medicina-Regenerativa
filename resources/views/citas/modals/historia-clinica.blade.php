@if ($puedeConsultarInformacionClinica)
@php
$historia = $cita->paciente?->historiaClinica;

$habitoAlimenticio =
$historia?->habitoAlimenticio;

$comidasHabituales = collect(
$habitoAlimenticio?->comidas ?? []
)->filter(
fn ($valor) => (bool) $valor
);

$alimentosHabituales = collect(
$habitoAlimenticio?->alimentos ?? []
)->filter(
fn ($valor) => filled($valor)
);

$esPacienteFemenina =
$cita->paciente?->sexo === 'femenino';

$ginecoobstetricos =
$esPacienteFemenina
? $historia?->antecedenteGinecoobstetrico
: null;

$resumenGineco = collect([
'Edad de menarca' =>
$ginecoobstetricos?->edad_menarca !== null
? $ginecoobstetricos->edad_menarca . ' años'
: null,

'Ritmo menstrual' =>
$ginecoobstetricos?->ritmo_menstrual,

'Duración de menstruación' =>
$ginecoobstetricos?->duracion_menstruacion_dias !== null
? $ginecoobstetricos->duracion_menstruacion_dias . ' días'
: null,

'Última menstruación' =>
$ginecoobstetricos
?->fecha_ultima_menstruacion
?->format('d/m/Y'),

'Inicio de vida sexual' =>
$ginecoobstetricos?->edad_inicio_vida_sexual !== null
? $ginecoobstetricos->edad_inicio_vida_sexual . ' años'
: null,

'Número de parejas sexuales' =>
$ginecoobstetricos?->numero_parejas_sexuales,

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
: ($ginecoobstetricos->embarazo_actual ? 'Sí' : 'No'),

'Método anticonceptivo' =>
$ginecoobstetricos?->metodo_anticonceptivo,

'Menopausia' =>
is_null($ginecoobstetricos?->menopausia)
? null
: ($ginecoobstetricos->menopausia ? 'Sí' : 'No'),

'Edad de menopausia' =>
$ginecoobstetricos?->edad_menopausia !== null
? $ginecoobstetricos->edad_menopausia . ' años'
: null,

'Último Papanicolaou' =>
$ginecoobstetricos
?->fecha_ultimo_papanicolaou
?->format('d/m/Y'),

'Resultado de Papanicolaou' =>
$ginecoobstetricos?->resultado_papanicolaou,

'Última mastografía' =>
$ginecoobstetricos
?->fecha_ultima_mastografia
?->format('d/m/Y'),

'Resultado de mastografía' =>
$ginecoobstetricos?->resultado_mastografia,

'Infecciones de transmisión sexual' =>
$ginecoobstetricos?->infecciones_transmision_sexual,
])->filter(
fn ($valor) => filled($valor) || $valor === 0
);

$heredofamiliares = collect(
$historia?->antecedentesHeredofamiliares?->antecedentes ?? []
)->filter(
fn ($valor) => filled($valor)
);

$personalesPatologicos = collect(
$historia?->antecedentesPersonalesPatologicos?->antecedentes ?? []
)->filter(
fn ($valor) => filled($valor)
);

$personalesNoPatologicos = collect(
$historia?->antecedentesPersonalesNoPatologicos?->antecedentes ?? []
)->filter(
fn ($valor) => filled($valor)
);

$formatearValorClinico = function ($valor): string {
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
        class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
        data-cerrar-modal-clinico>
    </div>

    <div
        class="relative flex min-h-full items-center
                   justify-center p-3 sm:p-6">

        <div
            class="relative flex max-h-[92vh] w-full min-w-0
                       max-w-5xl flex-col overflow-hidden
                       rounded-2xl bg-white shadow-2xl
                       [overflow-wrap:anywhere]">

            {{-- Encabezado --}}
            <header
                class="flex shrink-0 items-start justify-between
                           gap-5 border-b border-slate-200
                           px-5 py-4 sm:px-6">

                <div class="min-w-0 flex-1">
                    <p
                        class="text-xs font-semibold uppercase
                                   tracking-wide text-[#0D3B7F]">
                        Expediente del paciente
                    </p>

                    <h2
                        id="titulo-modal-historia"
                        class="mt-1 text-xl font-bold text-slate-900">
                        Historia clínica
                    </h2>

                    <p class="mt-1 text-sm text-slate-600">
                        {{ $cita->paciente?->nombre }}
                        {{ $cita->paciente?->apellido }}
                    </p>
                </div>

                <button
                    type="button"
                    data-cerrar-modal-clinico
                    aria-label="Cerrar Historia clínica"
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

            {{-- Contenido --}}
            <div
                class="min-h-0 min-w-0 flex-1 overflow-y-auto
                           bg-slate-50/70 p-5 sm:p-6">

                @if (
                ! $historia
                && blank($cita->paciente?->alergias)
                )
                <div
                    class="rounded-2xl border border-dashed
                                   border-slate-300 bg-white
                                   px-6 py-12 text-center">

                    <p class="font-semibold text-slate-700">
                        No existe Historia clínica registrada.
                    </p>

                    <p class="mt-1 text-sm text-slate-600">
                        El expediente aparecerá aquí cuando
                        sea capturado.
                    </p>
                </div>
                @else
                <div class="grid gap-4 md:grid-cols-2">

                    {{-- Resumen principal --}}
                    <section
                        class="min-w-0 rounded-2xl border
                                       border-slate-200 bg-white p-5">

                        <h3 class="font-bold text-slate-900">
                            Resumen clínico
                        </h3>

                        <dl class="mt-4 space-y-4">
                            <div>
                                <dt
                                    class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-slate-600">
                                    Patología base
                                </dt>

                                <dd
                                    class="mt-1 whitespace-pre-line
                                                   text-sm text-slate-700">{{ $historia?->patologia_base ?: 'No registrada' }}</dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-slate-600">
                                    Padecimiento actual
                                </dt>

                                <dd
                                    class="mt-1 whitespace-pre-line
                                                   text-sm text-slate-700">{{ $historia?->padecimiento_actual ?: 'No registrado' }}</dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-slate-600">
                                    Tratamientos actuales
                                </dt>

                                <dd
                                    class="mt-1 whitespace-pre-line
                                                   text-sm text-slate-700">{{ $historia?->tratamientos_actuales ?: 'No registrados' }}</dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-slate-600">
                                    Prioridad de análisis médico
                                </dt>

                                <dd
                                    class="mt-1 whitespace-pre-line
                                                   text-sm text-slate-700">{{ $historia?->prioridad_analisis_medico ?: 'No registrada' }}</dd>
                            </div>
                        </dl>
                    </section>

                    {{-- Alertas --}}
                    <section
                        class="min-w-0 rounded-2xl border
                                       border-rose-200 bg-rose-50 p-5">

                        <h3 class="font-bold text-rose-950">
                            Alertas clínicas
                        </h3>

                        <p
                            class="mt-4 text-xs font-semibold uppercase
                                           tracking-wide text-rose-700">
                            Alergias
                        </p>

                        <p
                            class="mt-2 whitespace-pre-line
                                           text-sm text-rose-900">{{ $cita->paciente?->alergias ?: 'No se registraron alergias.' }}</p>
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
                        class="min-w-0 rounded-2xl border
                                           border-slate-200 bg-white p-5">

                        <h3 class="font-bold text-slate-900">
                            {{ $grupo['titulo'] }}
                        </h3>

                        @if ($grupo['datos']->isEmpty())
                        <p class="mt-4 text-sm text-slate-600">
                            Sin antecedentes registrados.
                        </p>
                        @else
                        <dl class="mt-4 space-y-3">
                            @foreach ($grupo['datos'] as $clave => $valor)
                            <div>
                                <dt
                                    class="text-xs font-semibold
                                                               text-slate-600">
                                    {{ $grupo['catalogo'][$clave]
                                                            ?? ucfirst(
                                                                str_replace('_', ' ', $clave)
                                                            ) }}
                                </dt>

                                <dd
                                    class="mt-0.5 whitespace-pre-line
                                                               text-sm text-slate-800">{{ $formatearValorClinico($valor) }}</dd>
                            </div>
                            @endforeach
                        </dl>
                        @endif
                    </section>
                    @endforeach
                </div>
                {{-- Hábitos alimenticios --}}
                <section
                    class="mt-4 min-w-0 rounded-2xl border
                    border-indigo-200 bg-indigo-50/40 p-5">

                    <div>
                        <h3 class="font-bold text-indigo-950">
                            Hábitos alimenticios
                        </h3>

                        <p class="mt-1 text-sm text-indigo-700">
                            Comidas habituales y frecuencia de consumo.
                        </p>
                    </div>

                    @if (! $habitoAlimenticio)
                    <p
                        class="mt-4 rounded-xl border border-dashed
                   border-indigo-200 bg-white px-4 py-5
                   text-center text-sm text-indigo-700">
                        Sin hábitos alimenticios registrados.
                    </p>
                    @else
                    <div class="mt-5">
                        <p
                            class="text-xs font-semibold uppercase
                       tracking-wide text-indigo-700">
                            Comidas habituales
                        </p>

                        @if ($comidasHabituales->isEmpty())
                        <p class="mt-2 text-sm text-slate-600">
                            No se especificaron comidas habituales.
                        </p>
                        @else
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($comidasHabituales as $clave => $valor)
                            <span
                                class="rounded-full bg-white px-3 py-1.5
                                   text-xs font-semibold text-indigo-800
                                   shadow-sm">
                                {{
                                \App\Models\HabitoAlimenticio::COMIDAS[$clave]
                                    ?? ucfirst(
                                        str_replace('_', ' ', $clave)
                                    )
                            }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="mt-5">
                        <p
                            class="text-xs font-semibold uppercase
                       tracking-wide text-indigo-700">
                            Frecuencia o cantidad de alimentos
                        </p>

                        @if ($alimentosHabituales->isEmpty())
                        <p class="mt-2 text-sm text-slate-600">
                            No se registraron alimentos.
                        </p>
                        @else
                        <dl
                            class="mt-3 grid gap-3
                           sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($alimentosHabituales as $clave => $valor)
                            <div
                                class="min-w-0 rounded-xl border
                                   border-indigo-100 bg-white p-3">
                                <dt
                                    class="text-xs font-semibold
                                       text-slate-600">
                                    {{
                                    \App\Models\HabitoAlimenticio::ALIMENTOS[$clave]
                                        ?? ucfirst(
                                            str_replace('_', ' ', $clave)
                                        )
                                }}
                                </dt>

                                <dd
                                    class="mt-1 whitespace-pre-line
                                       text-sm text-slate-800">{{ $valor }}</dd>
                            </div>
                            @endforeach
                        </dl>
                        @endif
                    </div>
                    @endif
                </section>
                {{-- Antecedentes ginecoobstétricos --}}
                @if ($esPacienteFemenina)
                <section
                    class="mt-4 min-w-0 rounded-2xl border
                  border-rose-200 bg-rose-50/40 p-5">

                    <div>
                        <h3 class="font-bold text-rose-950">
                            Antecedentes ginecoobstétricos
                        </h3>

                        <p class="mt-1 text-sm text-rose-700">
                            Historia menstrual, obstétrica y preventiva.
                        </p>
                    </div>

                    @if (! $ginecoobstetricos)
                    <p
                        class="mt-4 rounded-xl border border-dashed
                       border-rose-200 bg-white px-4 py-5
                       text-center text-sm text-rose-700">
                        Sin antecedentes ginecoobstétricos registrados.
                    </p>
                    @else
                    @if ($resumenGineco->isEmpty())
                    <p
                        class="mt-4 rounded-xl border border-dashed
                           border-rose-200 bg-white px-4 py-5
                           text-center text-sm text-rose-700">
                        No se especificaron datos ginecoobstétricos.
                    </p>
                    @else
                    <dl
                        class="mt-5 grid gap-3
                           sm:grid-cols-2 lg:grid-cols-3">

                        @foreach ($resumenGineco as $etiqueta => $valor)
                        <div
                            class="min-w-0 rounded-xl border
                                   border-rose-100 bg-white p-3">

                            <dt
                                class="text-xs font-semibold
                                       text-slate-600">
                                {{ $etiqueta }}
                            </dt>

                            <dd
                                class="mt-1 whitespace-pre-line
                                       text-sm text-slate-800">{{ $valor }}</dd>
                        </div>
                        @endforeach
                    </dl>
                    @endif

                    @if (filled($ginecoobstetricos->observaciones))
                    <div
                        class="mt-4 rounded-xl border
                           border-rose-100 bg-white p-4">

                        <p
                            class="text-xs font-semibold uppercase
                               tracking-wide text-rose-700">
                            Observaciones
                        </p>

                        <p
                            class="mt-2 whitespace-pre-line
                               text-sm text-slate-800">{{ $ginecoobstetricos->observaciones }}</p>
                    </div>
                    @endif
                    @endif
                </section>
                @endif
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