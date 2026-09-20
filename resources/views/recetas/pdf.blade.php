<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Receta médica #{{ $receta->id }}</title>

    <style>
        @page {
            margin: 10px 12px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #172033;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            line-height: 1.35;
        }

        .sheet {
            min-height: 720px;
            padding: 14px 16px;
            border: 2px solid #183f78;
            border-radius: 18px;
        }

        .header,
        .patient-table,
        .content-table,
        .vital-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header {
            border-bottom: 2px solid #183f78;
        }

        .header td {
            padding: 0 0 12px;
            vertical-align: top;
        }

        .attention {
            width: 30%;
        }

        .doctor {
            width: 70%;
            text-align: center;
        }

        .doctor-table {
            width: 100%;
            border-collapse: collapse;
        }

        .doctor-table td {
            padding: 0;
            vertical-align: middle;
        }

        .doctor-copy {
            text-align: center;
        }

        .logo {
            display: block;
            width: 58px;
            max-height: 52px;
            margin: 0 auto;
            object-fit: contain;
        }

        .attention-copy {
            display: inline-block;
            max-width: 315px;
            vertical-align: middle;
        }

        .attention-title {
            margin: 0 0 3px;
            color: #183f78;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .attention-text {
            margin: 1px 0;
            color: #4b5563;
            font-size: 8px;
        }

        .doctor-name {
            margin: 0;
            color: #183f78;
            font-size: 15px;
            font-weight: bold;
        }

        .doctor-detail {
            margin: 2px 0 0;
            color: #374151;
            font-size: 9px;
        }

        .document-title {
            margin: 7px 0 0;
            color: #183f78;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .patient-table {
            margin-top: 12px;
            table-layout: fixed;
        }

        .patient-table td {
            padding: 7px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .label {
            display: block;
            margin-bottom: 2px;
            color: #64748b;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .value {
            color: #111827;
            font-size: 9px;
            font-weight: bold;
        }

        .content-table {
            margin-top: 12px;
            table-layout: fixed;
        }

        .content-table>tbody>tr>td {
            vertical-align: top;
        }

        .prescription-column {
            width: 72%;
            padding-right: 12px;
        }

        .vitals-column {
            width: 28%;
        }

        .panel {
            height: 420px;
            border: 1px solid #cbd5e1;
        }

        .panel-title {
            margin: 0;
            padding: 8px 10px;
            border-bottom: 1px solid #cbd5e1;
            background-color: #edf4fb;
            color: #183f78;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .prescription-content {
            padding: 14px 16px;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.65;
            overflow-wrap: break-word;
        }

        .vital-table {
            table-layout: fixed;
        }

        .vital-table td {
            padding: 7px 9px;
            border-bottom: 1px solid #e2e8f0;
        }

        .vital-name {
            width: 55%;
            color: #475569;
            font-size: 8px;
            font-weight: bold;
        }

        .vital-value {
            width: 45%;
            color: #111827;
            font-size: 9px;
            font-weight: bold;
            text-align: right;
        }

        .empty-vitals {
            padding: 18px 12px;
            color: #64748b;
            font-size: 9px;
            line-height: 1.5;
            text-align: center;
        }

        .bottom-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        .bottom-table td {
            width: 50%;
            vertical-align: bottom;
        }

        .folio {
            color: #64748b;
            font-size: 8px;
        }

        .signature {
            text-align: center;
        }

        .signature-line {
            width: 250px;
            margin: 0 auto 6px;
            border-top: 1px solid #334155;
        }

        .signature-name {
            margin: 0;
            color: #111827;
            font-size: 9px;
            font-weight: bold;
        }

        .signature-detail {
            margin: 2px 0 0;
            color: #64748b;
            font-size: 8px;
        }
    </style>
</head>

<body>
    @php
    $cita = $receta->cita;
    $paciente = $cita->paciente;
    $medico = $cita->medico;
    $signosVitales = $cita->signoVital;

    $nombrePaciente = trim(
    ($paciente?->nombre ?? '')
    . ' '
    . ($paciente?->apellido_paterno
    ?? $paciente?->apellido
    ?? '')
    . ' '
    . ($paciente?->apellido_materno ?? '')
    );

    $nombreMedico = trim(
    ($medico?->nombre ?? '')
    . ' '
    . ($medico?->apellido_paterno ?? '')
    . ' '
    . ($medico?->apellido_materno ?? '')
    );

    if ($nombreMedico === '') {
    $nombreMedico = $medico?->user?->name
    ?? 'Médico no disponible';
    }

    $fechaExpedicion = $receta->fecha_expedicion
    ? \Carbon\Carbon::parse(
    $receta->fecha_expedicion
    )->format('d/m/Y')
    : now()->format('d/m/Y');

    $folio = 'REC-'
    . str_pad(
    (string) $receta->id,
    6,
    '0',
    STR_PAD_LEFT
    );

    $universidad = $medico?->universidad;

    $universidadLogoRelativo = $universidad?->logo_path
    ?: 'images/universidades/default.png';

    $universidadLogoPath = public_path(
    $universidadLogoRelativo
    );

    if (! file_exists($universidadLogoPath)) {
    $universidadLogoPath = public_path(
    'images/universidades/default.png'
    );
    }

    $universidadLogoBase64 = file_exists(
    $universidadLogoPath
    )
    ? 'data:image/png;base64,'
    . base64_encode(
    file_get_contents($universidadLogoPath)
    )
    : null;

    $sexo = $paciente?->sexo_texto
    ?? 'No especificado';
    @endphp

    <main class="sheet">
        <table class="header">
            <tr>
                <td class="attention">
                    @if ($universidadLogoBase64)
                        <img src="{{ $universidadLogoBase64 }}" alt="Logotipo" class="logo" style="margin-left: 0;">
                    @endif
                </td>

                <td class="doctor">
                    <table class="doctor-table">
                        <tr>
                            <td class="doctor-copy">
                                <p class="doctor-name">
                                    Dr. {{ $nombreMedico }}
                                </p>

                                <p class="doctor-detail">
                                    {{ $medico?->especialidad
                        ?: 'Especialidad no registrada' }}
                                </p>

                                <p class="doctor-detail">
                                    Cédula profesional:
                                    {{ $medico?->cedula
                        ?: 'No registrada' }}
                                </p>

                                @if (config('clinic.telefono_fijo'))
                                <p class="doctor-detail">Teléfono fijo: {{ config('clinic.telefono_fijo') }}</p>
                                @endif

                                <p class="doctor-detail">
                                    Av. León de los Aldama #3475, Col. San Felipe de Jesús,
                                    Alc. G.A.M., CDMX, C.P. 07510
                                </p>

                                <p class="document-title">
                                    Receta médica
                                </p>
                            </td>

                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="patient-table">
            <tr>
                <td style="width: 48%;">
                    <span class="label">Nombre del paciente</span>
                    <span class="value">
                        {{ $nombrePaciente ?: 'No disponible' }}
                    </span>
                </td>

                <td style="width: 14%;">
                    <span class="label">Edad</span>
                    <span class="value">
                        {{ $paciente?->edad ?? '—' }}
                    </span>
                </td>

                <td style="width: 18%;">
                    <span class="label">Sexo</span>
                    <span class="value">{{ $sexo }}</span>
                </td>

                <td style="width: 20%;">
                    <span class="label">Fecha</span>
                    <span class="value">{{ $fechaExpedicion }}</span>
                </td>
            </tr>
        </table>

        <table class="content-table">
            <tr>
                <td class="prescription-column">
                    <div class="panel">
                        <p class="panel-title">
                            Indicaciones médicas
                        </p>

                        <div class="prescription-content">
                            {!! nl2br(e($receta->contenido)) !!}
                        </div>
                    </div>
                </td>

                <td class="vitals-column">
                    <div class="panel">
                        <p class="panel-title">
                            Signos vitales
                        </p>

                        @if ($signosVitales)
                        <table class="vital-table">
                            <tr>
                                <td class="vital-name">Peso</td>
                                <td class="vital-value">
                                    {{ $signosVitales->peso ?? '—' }}
                                    @if ($signosVitales->peso)
                                    kg
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td class="vital-name">Estatura</td>
                                <td class="vital-value">
                                    {{ $signosVitales->estatura ?? '—' }}
                                    @if ($signosVitales->estatura)
                                    cm
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td class="vital-name">IMC</td>
                                <td class="vital-value">
                                    {{ $signosVitales->imc ?? '—' }}
                                </td>
                            </tr>

                            <tr>
                                <td class="vital-name">Temperatura</td>
                                <td class="vital-value">
                                    {{ $signosVitales->temperatura ?? '—' }}
                                    @if ($signosVitales->temperatura)
                                    °C
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td class="vital-name">
                                    Presión arterial
                                </td>
                                <td class="vital-value">
                                    @if (
                                    $signosVitales->presion_sistolica
                                    && $signosVitales->presion_diastolica
                                    )
                                    {{
                                                $signosVitales
                                                    ->presion_sistolica
                                            }}/{{ $signosVitales
                                                ->presion_diastolica }}
                                    mmHg
                                    @else
                                    —
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td class="vital-name">
                                    Frecuencia cardiaca
                                </td>
                                <td class="vital-value">
                                    {{
                                            $signosVitales
                                                ->frecuencia_cardiaca
                                            ?? '—'
                                        }}
                                    @if (
                                    $signosVitales
                                    ->frecuencia_cardiaca
                                    )
                                    lpm
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td class="vital-name">
                                    Frecuencia respiratoria
                                </td>
                                <td class="vital-value">
                                    {{
                                            $signosVitales
                                                ->frecuencia_respiratoria
                                            ?? '—'
                                        }}
                                    @if (
                                    $signosVitales
                                    ->frecuencia_respiratoria
                                    )
                                    rpm
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td class="vital-name">SpO₂</td>
                                <td class="vital-value">
                                    {{
                                            $signosVitales
                                                ->saturacion_oxigeno
                                            ?? '—'
                                        }}
                                    @if (
                                    $signosVitales
                                    ->saturacion_oxigeno
                                    )
                                    %
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td class="vital-name">Glucosa</td>
                                <td class="vital-value">
                                    {{ $signosVitales->glucosa ?? '—' }}
                                    @if ($signosVitales->glucosa)
                                    mg/dL
                                    @endif
                                </td>
                            </tr>
                        </table>
                        @else
                        <div class="empty-vitals">
                            No se registraron signos vitales
                            para esta consulta.
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="bottom-table">
            <tr>
                <td>
                    <p class="attention-text">Horario de atención: lunes a viernes de 9:00 a 18:00 hrs.</p>
                    <div class="folio">
                        Folio: {{ $folio }}
                    </div>
                </td>

                <td class="signature">
                    <div class="signature-line"></div>

                    <p class="signature-name">
                        Dr. {{ $nombreMedico }}
                    </p>

                    <p class="signature-detail">
                        {{ $medico?->especialidad
                            ?: 'Especialidad no registrada' }}
                        · Cédula:
                        {{ $medico?->cedula
                            ?: 'No registrada' }}
                    </p>
                </td>
            </tr>
        </table>
    </main>
</body>

</html>
