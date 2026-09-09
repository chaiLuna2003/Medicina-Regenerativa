<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>
        Caso clínico #{{ $casoClinico->id }}
    </title>

    <style>
        @page {
            margin: 34px 38px 50px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #27364a;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9px;
            line-height: 1.45;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px solid #0d3b7f;
        }

        .header td {
            padding-bottom: 13px;
            vertical-align: middle;
        }

        .header-left {
            width: 55%;
        }

        .header-right {
            width: 45%;
            text-align: right;
        }

        .document-category {
            margin: 0;
            color: #238ccc;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .document-title {
            margin: 0;
            color: #0d3b7f;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .document-detail {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 8px;
        }

        .section {
            margin-top: 16px;
        }

        .section-title {
            margin: 0 0 8px;
            padding: 7px 9px;
            border-left: 4px solid #238ccc;
            background-color: #edf5fc;
            color: #0d3b7f;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .information-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .information-table td {
            padding: 7px 8px;
            border: 1px solid #dbe4f0;
            vertical-align: top;
        }

        .label {
            display: block;
            margin-bottom: 3px;
            color: #64748b;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .value {
            color: #172033;
            font-size: 9px;
            font-weight: bold;
            overflow-wrap: break-word;
        }

        .narrative {
            padding: 8px 10px;
            border: 1px solid #dbe4f0;
            background-color: #f8fafc;
            color: #27364a;
            white-space: pre-line;
            overflow-wrap: break-word;
        }

        .alert {
            padding: 8px 10px;
            border: 1px solid #fecaca;
            border-left: 4px solid #dc2626;
            background-color: #fef2f2;
            color: #991b1b;
        }

        .status {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-closed {
            background-color: #e2e8f0;
            color: #475569;
        }

        .evolution {
            margin-top: 16px;
            page-break-inside: auto;
        }

        .evolution-header {
            padding: 9px 11px;
            background-color: #0d3b7f;
            color: #ffffff;
        }

        .evolution-title {
            margin: 0;
            font-size: 11px;
            font-weight: bold;
        }

        .evolution-detail {
            margin: 3px 0 0;
            color: #dbeafe;
            font-size: 8px;
        }

        .subsection-title {
            margin: 11px 0 6px;
            color: #0d3b7f;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .empty {
            padding: 9px 11px;
            border: 1px dashed #cbd5e1;
            background-color: #f8fafc;
            color: #64748b;
            font-style: italic;
        }

        .page-break {
            page-break-before: always;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -34px;
            left: 0;
            padding-top: 7px;
            border-top: 1px solid #dbe4f0;
            color: #64748b;
            font-size: 7px;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
    $paciente = $casoClinico->paciente;

    $nombrePaciente = trim(
    ($paciente?->nombre ?? '')
    . ' '
    . ($paciente?->apellido ?? '')
    );

    $folio = 'CC-'
    . str_pad(
    (string) $casoClinico->id,
    6,
    '0',
    STR_PAD_LEFT
    );

    $estadoTexto = $casoClinico->estaCerrado()
    ? 'Cerrado'
    : 'Activo';

    $estadoClase = $casoClinico->estaCerrado()
    ? 'status-closed'
    : 'status-active';

    $estadoCivilTexto =
    \App\Models\Pacientes::ESTADOS_CIVILES[
    $paciente?->estado_civil
    ?? 'no_especificado'
    ] ?? 'No especificado';

    $escolaridadTexto =
    \App\Models\Pacientes::ESCOLARIDADES[
    $paciente?->escolaridad
    ?? 'no_especificado'
    ] ?? 'No especificada';

    $direccionPaciente = collect([
    $paciente?->domicilio,
    $paciente?->ciudad,
    $paciente?->estado,
    $paciente?->codigo_postal
    ? 'C.P. ' . $paciente->codigo_postal
    : null,
    ])
    ->filter()
    ->implode(', ');
    @endphp

    {{-- Encabezado --}}
    <table class="header">
        <tr>
            <td class="header-left">
                <p class="document-category">
                    Expediente clínico confidencial
                </p>
            </td>

            <td class="header-right">
                <p class="document-title">
                    Caso clínico
                </p>

                <p class="document-detail">
                    Folio: {{ $folio }}
                </p>

                <p class="document-detail">
                    Generado: {{ now()->format('d/m/Y H:i') }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Datos generales del paciente --}}
    <section class="section">
        <h2 class="section-title">
            Datos generales del paciente
        </h2>

        <table class="information-table">
            <tr>
                <td style="width: 55%;">
                    <span class="label">
                        Nombre completo
                    </span>

                    <span class="value">
                        {{ $nombrePaciente ?: 'No disponible' }}
                    </span>
                </td>

                <td style="width: 20%;">
                    <span class="label">
                        Edad
                    </span>

                    <span class="value">
                        {{ $paciente?->edad ?? 'No disponible' }}
                    </span>
                </td>

                <td style="width: 25%;">
                    <span class="label">
                        ID del paciente
                    </span>

                    <span class="value">
                        #{{ $paciente?->id ?? 'N/D' }}
                    </span>
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">
                        Fecha de nacimiento
                    </span>

                    <span class="value">
                        {{ $paciente?->fecha_nacimiento
                        ?->format('d/m/Y')
                        ?? 'No registrada' }}
                    </span>
                </td>

                <td>
                    <span class="label">
                        Sexo
                    </span>

                    <span class="value">
                        {{ $paciente?->sexo_texto
                        ?? 'No especificado' }}
                    </span>
                </td>

                <td>
                    <span class="label">
                        Tipo de sangre
                    </span>

                    <span class="value">
                        {{ $paciente?->tipo_sangre
                        ?? 'No registrado' }}
                    </span>
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">
                        Estado civil
                    </span>

                    <span class="value">
                        {{ $estadoCivilTexto }}
                    </span>
                </td>

                <td>
                    <span class="label">
                        Escolaridad
                    </span>

                    <span class="value">
                        {{ $escolaridadTexto }}
                    </span>
                </td>

                <td>
                    <span class="label">
                        Ocupación
                    </span>

                    <span class="value">
                        {{ $paciente?->ocupacion
                        ?: 'No registrada' }}
                    </span>
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">
                        Teléfono principal
                    </span>

                    <span class="value">
                        {{ $paciente?->telefono
                        ?: 'No registrado' }}
                    </span>
                </td>

                <td colspan="2">
                    <span class="label">
                        Correo electrónico
                    </span>

                    <span class="value">
                        {{ $paciente?->email
                        ?: 'No registrado' }}
                    </span>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <span class="label">
                        Dirección
                    </span>

                    <span class="value">
                        {{ $direccionPaciente
                        ?: 'No registrada' }}
                    </span>
                </td>
            </tr>
        </table>

        @if (filled($paciente?->alergias))
        <div class="alert" style="margin-top: 9px;">
            <strong>Alergias:</strong>
            {{ $paciente->alergias }}
        </div>
        @endif
    </section>

    {{-- Información del caso clínico --}}
    <section class="section">
        <h2 class="section-title">
            Información del caso clínico
        </h2>

        <table class="information-table">
            <tr>
                <td style="width: 50%;">
                    <span class="label">
                        Nombre del caso
                    </span>

                    <span class="value">
                        {{ $casoClinico->nombre }}
                    </span>
                </td>

                <td style="width: 25%;">
                    <span class="label">
                        Fecha de inicio
                    </span>

                    <span class="value">
                        {{ $casoClinico->fecha_inicio
                        ?->format('d/m/Y')
                        ?? 'No registrada' }}
                    </span>
                </td>

                <td style="width: 25%;">
                    <span class="label">
                        Estado
                    </span>

                    <span class="status {{ $estadoClase }}">
                        {{ $estadoTexto }}
                    </span>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <span class="label">
                        Caso abierto por
                    </span>

                    <span class="value">
                        {{ $casoClinico->creadoPor?->name
                        ?? 'Usuario no disponible' }}
                    </span>
                </td>

                <td>
                    <span class="label">
                        Evoluciones registradas
                    </span>

                    <span class="value">
                        {{ $casoClinico->evoluciones->count() }}
                    </span>
                </td>
            </tr>
        </table>

        <h3 class="subsection-title">
            Descripción inicial
        </h3>

        <div class="narrative">
            {{ $casoClinico->descripcion_inicial
            ?: 'No se registró una descripción inicial.' }}
        </div>

        @if ($casoClinico->estaCerrado())
        <h3 class="subsection-title">
            Cierre del caso
        </h3>

        <table class="information-table">
            <tr>
                <td style="width: 30%;">
                    <span class="label">
                        Fecha de cierre
                    </span>

                    <span class="value">
                        {{ $casoClinico->fecha_cierre
                        ?->format('d/m/Y H:i')
                        ?? 'No registrada' }}
                    </span>
                </td>

                <td style="width: 70%;">
                    <span class="label">
                        Cerrado por
                    </span>

                    <span class="value">
                        {{ $casoClinico->cerradoPor?->name
                        ?? 'Usuario no disponible' }}
                    </span>
                </td>
            </tr>
        </table>

        <div class="narrative" style="margin-top: 8px;">
            <span class="label">
                Motivo del cierre
            </span>

            {{ $casoClinico->motivo_cierre
            ?: 'No se registró un motivo de cierre.' }}
        </div>
        @endif
    </section>

    {{-- Evoluciones clínicas --}}
    <section class="section">
        <h2 class="section-title">
            Historial de evoluciones clínicas
        </h2>

        @forelse ($casoClinico->evoluciones as $indice => $evolucion)
        @php
        $cita = $evolucion->cita;
        $signos = $cita?->signoVital;
        $medico = $evolucion->medico;

        $nombreMedico = trim(
        ($medico?->nombre ?? '')
        . ' '
        . ($medico?->apellido_paterno ?? '')
        . ' '
        . ($medico?->apellido_materno ?? '')
        );

        if ($nombreMedico === '') {
        $nombreMedico =
        $medico?->user?->name
        ?? 'Médico no disponible';
        }

        $horaConsulta = $cita?->hora
        ? \Carbon\Carbon::parse(
        $cita->hora
        )->format('h:i A')
        : 'No registrada';
        @endphp

        <article class="evolution">
            <div class="evolution-header">
                <p class="evolution-title">
                    Evolución #{{ $indice + 1 }}
                </p>

                <p class="evolution-detail">
                    {{ $evolucion->fecha
                    ?->format('d/m/Y')
                    ?? 'Fecha no registrada' }}

                    · {{ $horaConsulta }}

                    · Dr. {{ $nombreMedico }}

                    @if ($cita)
                    · Cita #{{ $cita->id }}
                    @endif
                </p>
            </div>

            <h3 class="subsection-title">
                Información de la consulta
            </h3>

            <table class="information-table">
                <tr>
                    <td style="width: 35%;">
                        <span class="label">
                            Médico responsable
                        </span>

                        <span class="value">
                            Dr. {{ $nombreMedico }}
                        </span>
                    </td>

                    <td style="width: 25%;">
                        <span class="label">
                            Especialidad
                        </span>

                        <span class="value">
                            {{ $medico?->especialidad
                            ?: 'No registrada' }}
                        </span>
                    </td>

                    <td style="width: 20%;">
                        <span class="label">
                            Fecha
                        </span>

                        <span class="value">
                            {{ $evolucion->fecha
                            ?->format('d/m/Y')
                            ?? 'No registrada' }}
                        </span>
                    </td>

                    <td style="width: 20%;">
                        <span class="label">
                            Hora
                        </span>

                        <span class="value">
                            {{ $horaConsulta }}
                        </span>
                    </td>
                </tr>
            </table>

            <h3 class="subsection-title">
                ">
                Signos vitales
            </h3>

            @if ($signos)
            <table class="information-table">
                <tr>
                    <td style="width: 20%;">
                        <span class="label">Peso</span>
                        <span class="value">
                            {{ $signos->peso !== null
                            ? number_format((float) $signos->peso, 2) . ' kg'
                            : 'N/D' }}
                        </span>
                    </td>

                    <td style="width: 20%;">
                        <span class="label">Estatura</span>
                        <span class="value">
                            {{ $signos->estatura !== null
                            ? number_format((float) $signos->estatura, 2) . ' cm'
                            : 'N/D' }}
                        </span>
                    </td>

                    <td style="width: 20%;">
                        <span class="label">IMC</span>
                        <span class="value">
                            {{ $signos->imc !== null
                            ? number_format($signos->imc, 2)
                            : 'N/D' }}
                        </span>
                    </td>

                    <td style="width: 20%;">
                        <span class="label">Temperatura</span>
                        <span class="value">
                            {{ $signos->temperatura !== null
                            ? number_format(
                                (float) $signos->temperatura,
                                1
                            ) . ' °C'
                            : 'N/D' }}
                        </span>
                    </td>

                    <td style="width: 20%;">
                        <span class="label">Glucosa</span>
                        <span class="value">
                            {{ $signos->glucosa !== null
                            ? number_format(
                                (float) $signos->glucosa,
                                2
                            ) . ' mg/dL'
                            : 'N/D' }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="label">Presión arterial</span>
                        <span class="value">
                            {{ $signos->presion_sistolica ?? 'N/D' }}
                            /
                            {{ $signos->presion_diastolica ?? 'N/D' }}
                            mmHg
                        </span>
                    </td>

                    <td>
                        <span class="label">Frecuencia cardiaca</span>
                        <span class="value">
                            {{ $signos->frecuencia_cardiaca ?? 'N/D' }}
                            lpm
                        </span>
                    </td>

                    <td>
                        <span class="label">Frecuencia respiratoria</span>
                        <span class="value">
                            {{ $signos->frecuencia_respiratoria ?? 'N/D' }}
                            rpm
                        </span>
                    </td>

                    <td>
                        <span class="label">Saturación de oxígeno</span>
                        <span class="value">
                            {{ $signos->saturacion_oxigeno ?? 'N/D' }}
                            %
                        </span>
                    </td>

                    <td>
                        <span class="label">Registrados por</span>
                        <span class="value">
                            {{ $signos->registradoPor?->name
                            ?? 'Usuario no disponible' }}
                        </span>
                    </td>
                </tr>
            </table>

            @if (filled($signos->observaciones))
            <div class="narrative" style="margin-top: 7px;">
                <span class="label">
                    Observaciones de signos vitales
                </span>

                {{ $signos->observaciones }}
            </div>
            @endif
            @else
            <div class="empty">
                No se registraron signos vitales para esta consulta.
            </div>
            @endif

            <h3 class="subsection-title">
                Evolución e interrogatorio
            </h3>

            <div class="narrative">
                {{ $evolucion->evolucion_clinica
        ?: 'No se registró información de evolución clínica.' }}
            </div>

            <h3 class="subsection-title">
                Diagnóstico
            </h3>

            <div class="narrative">
                {{ $evolucion->diagnostico
        ?: 'No se registró un diagnóstico.' }}
            </div>

            <h3 class="subsection-title">
                Tratamiento
            </h3>

            <div class="narrative">
                {{ $evolucion->tratamiento
        ?: 'No se registró un tratamiento.' }}
            </div>

            <h3 class="subsection-title">
                Plan y recomendaciones
            </h3>

            <div class="narrative">
                {{ $evolucion->plan_recomendaciones
        ?: 'No se registraron recomendaciones.' }}
            </div>

            @if (filled($evolucion->indicaciones_enfermeria))
            <h3 class="subsection-title">
                Indicaciones para enfermería
            </h3>

            <div class="narrative">
                {{ $evolucion->indicaciones_enfermeria }}
            </div>
            @endif

            @if (filled($evolucion->observaciones))
            <h3 class="subsection-title">
                Observaciones adicionales
            </h3>

            <div class="narrative">
                {{ $evolucion->observaciones }}
            </div>
            @endif

            @php
            $exploracion = $cita?->exploracionFisica;
            $estudios = $cita?->estudios ?? collect();
            @endphp

            <div class="subsection-title">Exploración física</div>

            @if ($exploracion)
            @if (filled($exploracion->interrogatorio))
            <div class="narrative">
                <span class="label">Interrogatorio</span>
                <div class="value">
                    {!! nl2br(e($exploracion->interrogatorio)) !!}
                </div>
            </div>
            @endif

            @if (filled($exploracion->exploracion_fisica))
            <div class="narrative">
                <span class="label">Exploración física general</span>
                <div class="value">
                    {!! nl2br(e($exploracion->exploracion_fisica)) !!}
                </div>
            </div>
            @endif

            @if (filled($exploracion->anotaciones))
            <div class="narrative">
                <span class="label">Anotaciones</span>
                <div class="value">
                    {!! nl2br(e($exploracion->anotaciones)) !!}
                </div>
            </div>
            @endif

            @if (! empty($exploracion->sistemas))
            <table class="information-table">
                <tbody>
                    @foreach ($exploracion->sistemas as $sistema => $valor)
                    @php
                    $nombreSistema = \App\Models\ExploracionFisica::SISTEMAS[
                    $sistema
                    ] ?? ucfirst(str_replace('_', ' ', $sistema));

                    if (is_array($valor)) {
                    $detalleSistema = collect($valor)
                    ->filter(fn ($detalle) => filled($detalle))
                    ->map(function ($detalle, $campo) {
                    $nombreCampo = ucfirst(
                    str_replace('_', ' ', $campo)
                    );

                    return $nombreCampo . ': ' . $detalle;
                    })
                    ->implode(' · ');
                    } else {
                    $detalleSistema = $valor;
                    }
                    @endphp

                    @if (filled($detalleSistema))
                    <tr>
                        <td style="width: 30%;">
                            <span class="label">
                                {{ $nombreSistema }}
                            </span>
                        </td>
                        <td>
                            <span class="value">
                                {{ $detalleSistema }}
                            </span>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            @endif

            @if (filled($exploracion->recomendaciones))
            <div class="narrative">
                <span class="label">Recomendaciones de exploración</span>
                <div class="value">
                    {!! nl2br(e($exploracion->recomendaciones)) !!}
                </div>
            </div>
            @endif
            @else
            <div class="empty">
                No se registró exploración física en esta consulta.
            </div>
            @endif

            <div class="subsection-title">Estudios clínicos</div>

            @if ($estudios->isNotEmpty())
            <table class="information-table">
                <tbody>
                    @foreach ($estudios as $estudio)
                    <tr>
                        <td style="width: 25%;">
                            <span class="label">Estudio</span>
                            <div class="value">
                                {{ $estudio->nombre ?: 'Sin nombre' }}
                            </div>
                        </td>

                        <td style="width: 20%;">
                            <span class="label">Fecha</span>
                            <div class="value">
                                {{ $estudio->fecha_estudio
                                ? \Illuminate\Support\Carbon::parse(
                                    $estudio->fecha_estudio
                                )->format('d/m/Y')
                                : 'No registrada' }}
                            </div>
                        </td>

                        <td>
                            <span class="label">Descripción o resultado</span>
                            <div class="value">
                                {{ $estudio->descripcion
                                ?: 'Sin descripción registrada' }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty">
                No se registraron estudios clínicos en esta consulta.
            </div>
            @endif

            @php
    $valoracionesAparatos = $evolucion->aparatos ?? collect();

    $estadosAparatos = [
        'no_evaluado' => 'No evaluado',
        'normal' => 'Normal',
        'requiere_atencion' => 'Requiere atención',
        'critico' => 'Crítico',
    ];
@endphp

<div class="subsection-title">
    Valoración de aparatos y sistemas
</div>

@if ($valoracionesAparatos->isNotEmpty())
    <table class="information-table">
        <thead>
            <tr>
                <th style="width: 30%; text-align: left;">
                    Aparato o sistema
                </th>
                <th style="width: 20%; text-align: left;">
                    Estado
                </th>
                <th style="text-align: left;">
                    Observaciones
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach ($valoracionesAparatos as $valoracion)
                @php
                   $definicionAparato =
    \App\Models\EvolucionAparato::APARATOS[
        $valoracion->aparato
    ] ?? null;

if (is_array($definicionAparato)) {
    $nombreAparato =
        $definicionAparato['nombre']
        ?? $definicionAparato['label']
        ?? ucfirst(
            str_replace('_', ' ', $valoracion->aparato)
        );
} else {
    $nombreAparato =
        $definicionAparato
        ?? ucfirst(
            str_replace('_', ' ', $valoracion->aparato)
        );
}

                    $estadoAparato = $estadosAparatos[
                        $valoracion->estado
                    ] ?? ucfirst(
                        str_replace('_', ' ', $valoracion->estado)
                    );
                @endphp

                <tr>
                    <td>
                        <span class="value">
                            {{ $nombreAparato }}
                        </span>
                    </td>

                    <td>
                        <span
                            class="status
                                {{ $valoracion->estado === 'normal'
                                    ? 'status-active'
                                    : '' }}
                                {{ in_array(
                                    $valoracion->estado,
                                    ['requiere_atencion', 'critico'],
                                    true
                                )
                                    ? 'status-closed'
                                    : '' }}"
                        >
                            {{ $estadoAparato }}
                        </span>
                    </td>

                    <td>
                        <span class="value">
                            {{ filled($valoracion->observaciones)
                                ? $valoracion->observaciones
                                : 'Sin observaciones' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="empty">
        No se registró valoración de aparatos y sistemas
        en esta evolución.
    </div>
@endif

        </article>
        @empty
        <div class="empty">
            Este caso clínico todavía no tiene evoluciones registradas.
        </div>
       @endforelse
</section>

@if (! empty($graficas))
    <section class="section page-break">
        <div class="section-title">
            Evolución gráfica del paciente
        </div>

        <div class="narrative">
            Las siguientes gráficas representan los signos vitales
            registrados durante las evoluciones de este caso clínico.
        </div>

        @foreach ($graficas as $grafica)
            <div
                style="
                    margin-top: 16px;
                    padding: 10px;
                    border: 1px solid #dbe4f0;
                    border-radius: 8px;
                    background: #ffffff;
                    page-break-inside: avoid;
                "
            >
                <img
                    src="{{ $grafica['imagen'] }}"
                    alt="{{ $grafica['titulo'] }}"
                    style="
                        display: block;
                        width: 100%;
                        height: auto;
                    "
                >
            </div>
        @endforeach
    </section>
@endif

<div class="footer">
        Documento clínico confidencial ·
        Caso {{ $folio }} ·
        Paciente #{{ $paciente?->id ?? 'N/D' }}
    </div>
</body>

</html>