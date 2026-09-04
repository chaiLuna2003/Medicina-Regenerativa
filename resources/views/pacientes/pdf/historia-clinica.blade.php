<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>
        Historia clínica de
        {{ $paciente->nombre }}
        {{ $paciente->apellido }}
    </title>

    <style>
        @page {
            margin: 34px 38px 45px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #27364a;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9px;
            line-height: 1.4;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px solid #0d3b7f;
        }

        .header td {
            padding: 0 0 13px;
            vertical-align: middle;
        }

        .brand {
            width: 55%;
        }

        .document {
            width: 45%;
            text-align: right;
        }

        .brand-description {
            margin: 3px 0 0;
            color: #238ccc;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .document-title {
            margin: 0;
            color: #0d3b7f;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .document-detail {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 8px;
        }

        .section {
            margin-top: 15px;
        }

        .section-title {
            margin: 0 0 7px;
            padding: 7px 9px;
            border-left: 4px solid #238ccc;
            background-color: #edf5fc;
            color: #0d3b7f;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .subsection-title {
            margin: 10px 0 6px;
            color: #0d3b7f;
            font-size: 9px;
            font-weight: bold;
        }

        .clinical-table {
            width: 100%;
            border-collapse: collapse;
        }

        .clinical-table td {
            padding: 6px 8px;
            border: 1px solid #dbe4f0;
            vertical-align: top;
        }

        .clinical-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .field-name {
            width: 31%;
            color: #475569;
            font-weight: bold;
        }

        .field-value {
            width: 69%;
            color: #1e293b;
            white-space: pre-line;
        }

        .patient-photo-wrapper {
            margin: 8px 0 10px;
            text-align: center;
        }

        .patient-photo-frame {
            display: inline-block;
            padding: 4px;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            background-color: #eff6ff;
        }

        .patient-photo {
            display: block;
            width: 82px;
            height: 82px;
            border-radius: 6px;
        }

        .patient-photo-caption {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 7px;
        }

        .patient-name {
            color: #0d3b7f;
            font-size: 11px;
            font-weight: bold;
        }

        .empty {
            padding: 9px 11px;
            border: 1px dashed #cbd5e1;
            background-color: #f8fafc;
            color: #64748b;
            font-style: italic;
        }

        .alert {
            padding: 9px 11px;
            border: 1px solid #fed7aa;
            border-left: 4px solid #f97316;
            background-color: #fff7ed;
            color: #7c2d12;
        }

        .page-break {
            page-break-before: always;
        }

        .exploration-header {
            margin-bottom: 8px;
            padding: 9px 11px;
            background-color: #0d3b7f;
            color: #ffffff;
        }

        .exploration-title {
            margin: 0;
            font-size: 11px;
            font-weight: bold;
        }

        .exploration-detail {
            margin: 3px 0 0;
            color: #dbeafe;
            font-size: 8px;
        }

        .narrative {
            margin-top: 7px;
            padding: 8px 10px;
            border: 1px solid #dbe4f0;
            background-color: #ffffff;
            color: #27364a;
            white-space: pre-line;
        }

        .narrative-title {
            margin: 0 0 4px;
            color: #0d3b7f;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 22px;
            padding-top: 8px;
            border-top: 1px solid #dbe4f0;
            color: #94a3b8;
            font-size: 7px;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
    $historia = $paciente->historiaClinica;

    $nombreCompleto = trim(
    ($paciente->nombre ?? '')
    . ' '
    . ($paciente->apellido ?? '')
    );

    $tieneValor = function ($valor): bool {
    return $valor !== null
    && $valor !== ''
    && $valor !== [];
    };

    $formatearValor = function ($valor): string {
    if (is_bool($valor)) {
    return $valor ? 'Sí' : 'No';
    }

    if ($valor instanceof \DateTimeInterface) {
    return $valor->format('d/m/Y');
    }

    if (is_array($valor)) {
    return collect($valor)
    ->filter(
    fn ($elemento) =>
    $elemento !== null
    && $elemento !== ''
    )
    ->implode(', ');
    }

    return trim((string) $valor);
    };

    $heredofamiliares = collect(
    $historia
    ?->antecedentesHeredofamiliares
    ?->antecedentes
    ?? []
    )->filter($tieneValor);

    $personalesPatologicos = collect(
    $historia
    ?->antecedentesPersonalesPatologicos
    ?->antecedentes
    ?? []
    )->filter($tieneValor);

    $personalesNoPatologicos = collect(
    $historia
    ?->antecedentesPersonalesNoPatologicos
    ?->antecedentes
    ?? []
    )->filter($tieneValor);

    $habitoAlimenticio =
    $historia?->habitoAlimenticio;

    $comidasHabituales = collect(
    $habitoAlimenticio?->comidas ?? []
    )->filter(
    fn ($valor) => (bool) $valor
    );

    $alimentosHabituales = collect(
    $habitoAlimenticio?->alimentos ?? []
    )->filter($tieneValor);

    $ginecoobstetricos =
    $paciente->sexo === 'femenino'
    ? $historia
    ?->antecedenteGinecoobstetrico
    : null;

    $exploraciones =
    $historia?->exploracionesFisicas
    ?? collect();

    $domicilio = collect([
    $paciente->domicilio,
    $paciente->ciudad,
    $paciente->estado,
    $paciente->codigo_postal,
    ])
    ->filter()
    ->implode(', ');

    $gruposAntecedentes = [
    [
    'titulo' =>
    'Antecedentes heredofamiliares',

    'datos' =>
    $heredofamiliares,

    'catalogo' =>
    \App\Models\AntecedenteHeredofamiliar::CAMPOS,
    ],
    [
    'titulo' =>
    'Antecedentes personales patológicos',

    'datos' =>
    $personalesPatologicos,

    'catalogo' =>
    \App\Models\AntecedentePersonalPatologico::CAMPOS,
    ],
    [
    'titulo' =>
    'Antecedentes personales no patológicos',

    'datos' =>
    $personalesNoPatologicos,

    'catalogo' =>
    \App\Models\AntecedentePersonalNoPatologico::CAMPOS,
    ],
    ];

    $camposGineco = [
    'edad_menarca' =>
    'Edad de menarca',

    'ritmo_menstrual' =>
    'Ritmo menstrual',

    'duracion_menstruacion_dias' =>
    'Duración de menstruación',

    'fecha_ultima_menstruacion' =>
    'Última menstruación',

    'edad_inicio_vida_sexual' =>
    'Inicio de vida sexual',

    'numero_parejas_sexuales' =>
    'Número de parejas sexuales',

    'gestas' =>
    'Gestas',

    'partos' =>
    'Partos',

    'cesareas' =>
    'Cesáreas',

    'abortos' =>
    'Abortos',

    'embarazos_ectopicos' =>
    'Embarazos ectópicos',

    'hijos_vivos' =>
    'Hijos vivos',

    'embarazo_actual' =>
    'Embarazo actual',

    'metodo_anticonceptivo' =>
    'Método anticonceptivo',

    'menopausia' =>
    'Menopausia',

    'edad_menopausia' =>
    'Edad de menopausia',

    'fecha_ultimo_papanicolaou' =>
    'Último Papanicolaou',

    'resultado_papanicolaou' =>
    'Resultado de Papanicolaou',

    'fecha_ultima_mastografia' =>
    'Última mastografía',

    'resultado_mastografia' =>
    'Resultado de mastografía',

    'infecciones_transmision_sexual' =>
    'Infecciones de transmisión sexual',

    'observaciones' =>
    'Observaciones',
    ];
    @endphp

    {{-- Encabezado --}}
    <table class="header">
        <tr>
            <td class="brand">
                <p class="brand-description">
                    Expediente clínico
                </p>
            </td>

            <td class="document">
                <p class="document-title">
                    Historia clínica
                </p>

                <p class="document-detail">
                    Folio HC-{{
                        str_pad(
                            (string) $paciente->id,
                            6,
                            '0',
                            STR_PAD_LEFT
                        )
                    }}
                </p>

                <p class="document-detail">
                    Emisión:
                    {{ now()->format('d/m/Y H:i') }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Identificación --}}
    <div class="section">
        <h2 class="section-title">
            Ficha de identificación
        </h2>

        @if ($fotoPaciente)
        <div class="patient-photo-wrapper">
            <div class="patient-photo-frame">
                <img
                    src="{{ $fotoPaciente }}"
                    alt="Fotografía del paciente"
                    class="patient-photo">
            </div>

            <p class="patient-photo-caption">
                Fotografía de identificación
            </p>
        </div>
        @endif

        <table class="clinical-table">
            <tr>
                <td class="field-name">
                    Paciente
                </td>

                <td class="field-value patient-name">
                    {{ $nombreCompleto ?: 'No registrado' }}
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Fecha de nacimiento
                </td>

                <td class="field-value">
                    {{
                        $paciente->fecha_nacimiento
                            ?->format('d/m/Y')
                        ?? 'No registrada'
                    }}
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Edad
                </td>

                <td class="field-value">
                    {{ $paciente->edad ?? 'No registrada' }}
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Sexo
                </td>

                <td class="field-value">
                    {{ $paciente->sexo_texto }}
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Estado civil
                </td>

                <td class="field-value">
                    {{
                        \App\Models\Pacientes::ESTADOS_CIVILES[
                            $paciente->estado_civil
                        ]
                        ?? 'No registrado'
                    }}
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Ocupación
                </td>

                <td class="field-value">
                    {{
                        $paciente->ocupacion
                        ?? 'No registrada'
                    }}
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Escolaridad
                </td>

                <td class="field-value">
                    {{
                        \App\Models\Pacientes::ESCOLARIDADES[
                            $paciente->escolaridad
                        ]
                        ?? 'No registrada'
                    }}
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Tipo de sangre
                </td>

                <td class="field-value">
                    {{
                        $paciente->tipo_sangre
                        ?? 'No registrado'
                    }}
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Religión
                </td>

                <td class="field-value">
                    {{
                        $paciente->religion
                        ?? 'No registrada'
                    }}
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Domicilio
                </td>

                <td class="field-value">
                    {{
                        $domicilio !== ''
                            ? $domicilio
                            : 'No registrado'
                    }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Alergias --}}
    @if ($paciente->alergias)
    <div class="section">
        <div class="alert">
            <strong>Alergias:</strong>
            {{ $paciente->alergias }}
        </div>
    </div>
    @endif

    {{-- Resumen clínico --}}
    <div class="section">
        <h2 class="section-title">
            Resumen clínico principal
        </h2>

        @if (
        !$historia
        || (
        !$historia->padecimiento_actual
        && !$historia->tratamientos_actuales
        && !$historia->prioridad_analisis_medico
        )
        )
        <div class="empty">
            Sin resumen clínico registrado.
        </div>
        @else
        <table class="clinical-table">
            @if ($historia->padecimiento_actual)
            <tr>
                <td class="field-name">
                    Padecimiento actual
                </td>

                <td class="field-value">
                    {{ $historia->padecimiento_actual }}
                </td>
            </tr>
            @endif

            @if ($historia->tratamientos_actuales)
            <tr>
                <td class="field-name">
                    Tratamientos actuales
                </td>

                <td class="field-value">
                    {{ $historia->tratamientos_actuales }}
                </td>
            </tr>
            @endif

            @if ($historia->prioridad_analisis_medico)
            <tr>
                <td class="field-name">
                    Prioridad de análisis médico
                </td>

                <td class="field-value">
                    {{
                                $historia
                                    ->prioridad_analisis_medico
                            }}
                </td>
            </tr>
            @endif
        </table>
        @endif
    </div>

    {{-- Antecedentes --}}
    @foreach ($gruposAntecedentes as $indice => $grupo)
    <div class="section">
        <h2 class="section-title">
            {{ $grupo['titulo'] }}
        </h2>

        @if (
        $indice === 0
        && $historia
        ?->antecedentesHeredofamiliares
        ?->numero_hermanos !== null
        )
        <table class="clinical-table">
            <tr>
                <td class="field-name">
                    Número de hermanos
                </td>

                <td class="field-value">
                    {{
                                $historia
                                    ->antecedentesHeredofamiliares
                                    ->numero_hermanos
                            }}
                </td>
            </tr>
        </table>
        @endif

        @if ($grupo['datos']->isEmpty())
        <div class="empty">
            Sin información registrada.
        </div>
        @else
        <table class="clinical-table">
            @foreach (
            $grupo['datos']
            as $clave => $valor
            )
            <tr>
                <td class="field-name">
                    {{
                                    $grupo['catalogo'][$clave]
                                    ?? ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $clave
                                        )
                                    )
                                }}
                </td>

                <td class="field-value">
                    {{ $formatearValor($valor) }}
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
    @endforeach

    {{-- Hábitos alimenticios --}}
    <div class="section">
        <h2 class="section-title">
            Hábitos alimenticios
        </h2>

        @if (!$habitoAlimenticio)
        <div class="empty">
            Sin hábitos alimenticios registrados.
        </div>
        @else
        <table class="clinical-table">
            <tr>
                <td class="field-name">
                    Comidas habituales
                </td>

                <td class="field-value">
                    @if ($comidasHabituales->isEmpty())
                    No especificadas
                    @else
                    {{
                                $comidasHabituales
                                    ->keys()
                                    ->map(
                                        fn ($clave) =>
                                            \App\Models\HabitoAlimenticio
                                                ::COMIDAS[$clave]
                                            ?? ucfirst($clave)
                                    )
                                    ->implode(', ')
                            }}
                    @endif
                </td>
            </tr>
        </table>

        <h3 class="subsection-title">
            Frecuencia o cantidad de alimentos
        </h3>

        @if ($alimentosHabituales->isEmpty())
        <div class="empty">
            Sin alimentos registrados.
        </div>
        @else
        <table class="clinical-table">
            @foreach (
            $alimentosHabituales
            as $clave => $valor
            )
            <tr>
                <td class="field-name">
                    {{
                                    \App\Models\HabitoAlimenticio
                                        ::ALIMENTOS[$clave]
                                    ?? ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $clave
                                        )
                                    )
                                }}
                </td>

                <td class="field-value">
                    {{ $formatearValor($valor) }}
                </td>
            </tr>
            @endforeach
        </table>
        @endif
        @endif
    </div>

    {{-- Ginecoobstétricos --}}
    @if ($paciente->sexo === 'femenino')
    <div class="section">
        <h2 class="section-title">
            Antecedentes ginecoobstétricos
        </h2>

        @if (!$ginecoobstetricos)
        <div class="empty">
            Sin antecedentes ginecoobstétricos
            registrados.
        </div>
        @else
        @php
        $datosGineco = collect(
        $camposGineco
        )
        ->mapWithKeys(
        fn ($etiqueta, $campo) => [
        $campo => [
        'etiqueta' =>
        $etiqueta,

        'valor' =>
        $ginecoobstetricos
        ->{$campo},
        ],
        ]
        )
        ->filter(
        fn ($dato) =>
        $tieneValor(
        $dato['valor']
        )
        );
        @endphp

        @if ($datosGineco->isEmpty())
        <div class="empty">
            Sin información ginecoobstétrica
            capturada.
        </div>
        @else
        <table class="clinical-table">
            @foreach ($datosGineco as $dato)
            <tr>
                <td class="field-name">
                    {{ $dato['etiqueta'] }}
                </td>

                <td class="field-value">
                    {{
                                        $formatearValor(
                                            $dato['valor']
                                        )
                                    }}
                </td>
            </tr>
            @endforeach
        </table>
        @endif
        @endif
    </div>
    @endif

    {{-- Exploraciones --}}
    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">
            Historial de exploraciones físicas
        </h2>

        @if ($exploraciones->isEmpty())
        <div class="empty">
            No existen exploraciones físicas registradas.
        </div>
        @else
        @foreach (
        $exploraciones
        as $exploracion
        )
        @if (!$loop->first)
        <div class="page-break"></div>
        @endif

        @php
        $cita = $exploracion->cita;

        $signosVitales =
        $cita?->signoVital;

        $fechaExploracion =
        $cita?->fecha
        ? $cita->fecha->format(
        'd/m/Y'
        )
        : $exploracion
        ->created_at
        ?->format('d/m/Y');

        $horaExploracion =
        $cita?->hora
        ? \Carbon\Carbon::parse(
        $cita->hora
        )->format('H:i')
        : $exploracion
        ->created_at
        ?->format('H:i');

        $nombreMedico = trim(
        (
        $exploracion
        ->medico
        ?->nombre
        ?? ''
        )
        . ' '
        . (
        $exploracion
        ->medico
        ?->apellido_paterno
        ?? ''
        )
        . ' '
        . (
        $exploracion
        ->medico
        ?->apellido_materno
        ?? ''
        )
        );

        $sistemas = collect(
        $exploracion->sistemas
        ?? []
        )->filter($tieneValor);
        @endphp

        <div class="exploration-header">
            <p class="exploration-title">
                Exploración #{{ $loop->iteration }}
            </p>

            <p class="exploration-detail">
                {{
                            $fechaExploracion
                            ?? 'Fecha no registrada'
                        }}

                @if ($horaExploracion)
                · {{ $horaExploracion }} h
                @endif

                @if ($nombreMedico)
                · Dr. {{ $nombreMedico }}
                @endif

                @if ($exploracion->medico?->cedula)
                · Cédula
                {{
                                $exploracion
                                    ->medico
                                    ->cedula
                            }}
                @endif
            </p>
        </div>

        {{-- Signos vitales --}}
        <h3 class="subsection-title">
            Signos vitales asociados
        </h3>

        @if (!$signosVitales)
        <div class="empty">
            Sin signos vitales asociados
            a esta exploración.
        </div>
        @else
        <table class="clinical-table">
            <tr>
                <td class="field-name">
                    Peso
                </td>

                <td class="field-value">
                    {{
                                    $signosVitales->peso
                                    ?? 'No registrado'
                                }}
                    kg
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Estatura
                </td>

                <td class="field-value">
                    {{
                                    $signosVitales->estatura
                                    ?? 'No registrada'
                                }}
                    cm
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Presión arterial
                </td>

                <td class="field-value">
                    {{
                                    $signosVitales
                                        ->presion_sistolica
                                    ?? '—'
                                }}/{{
                                    $signosVitales
                                        ->presion_diastolica
                                    ?? '—'
                                }}
                    mmHg
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Temperatura
                </td>

                <td class="field-value">
                    {{
                                    $signosVitales
                                        ->temperatura
                                    ?? 'No registrada'
                                }}
                    °C
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Frecuencia cardiaca
                </td>

                <td class="field-value">
                    {{
                                    $signosVitales
                                        ->frecuencia_cardiaca
                                    ?? 'No registrada'
                                }}
                    lpm
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Frecuencia respiratoria
                </td>

                <td class="field-value">
                    {{
                                    $signosVitales
                                        ->frecuencia_respiratoria
                                    ?? 'No registrada'
                                }}
                    rpm
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Saturación de oxígeno
                </td>

                <td class="field-value">
                    {{
                                    $signosVitales
                                        ->saturacion_oxigeno
                                    ?? 'No registrada'
                                }}
                    %
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Glucosa
                </td>

                <td class="field-value">
                    {{
                                    $signosVitales->glucosa
                                    ?? 'No registrada'
                                }}
                    mg/dL
                </td>
            </tr>

            <tr>
                <td class="field-name">
                    Observaciones de enfermería
                </td>

                <td class="field-value">
                    {{
                                    $signosVitales
                                        ->observaciones
                                    ?: 'Sin observaciones'
                                }}
                </td>
            </tr>
        </table>
        @endif

        {{-- Campos narrativos --}}
        @foreach (
        \App\Models\ExploracionFisica::CAMPOS
        as $campo => $etiqueta
        )
        @if ($exploracion->{$campo})
        <div class="narrative">
            <p class="narrative-title">
                {{ $etiqueta }}
            </p>

            {{ $exploracion->{$campo} }}
        </div>
        @endif
        @endforeach

        @if ($exploracion->exploracion_fisica)
        <div class="narrative">
            <p class="narrative-title">
                Exploración narrativa anterior
            </p>

            {{
                            $exploracion
                                ->exploracion_fisica
                        }}
        </div>
        @endif

        {{-- Sistemas --}}
        <h3 class="subsection-title">
            Valoración por sistemas y órganos
        </h3>

        @if ($sistemas->isEmpty())
        <div class="empty">
            Sin valoración por sistemas registrada.
        </div>
        @else
        <table class="clinical-table">
            @foreach (
            $sistemas
            as $clave => $valor
            )
            <tr>
                <td class="field-name">
                    {{
                                        \App\Models\ExploracionFisica
                                            ::SISTEMAS[$clave]
                                        ?? ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $clave
                                            )
                                        )
                                    }}
                </td>

                <td class="field-value">
                    {{
                                        $formatearValor(
                                            $valor
                                        )
                                    }}
                </td>
            </tr>
            @endforeach
        </table>
        @endif
        @endforeach
        @endif
    </div>

    <div class="footer">
        Expediente clínico confidencial ·
        Paciente #{{ $paciente->id }} ·
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>