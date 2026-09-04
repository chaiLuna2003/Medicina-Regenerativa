<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>
        Receta médica #{{ $receta->id }}
    </title>

    <style>
        @page {
            margin: 38px 42px 55px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11px;
            line-height: 1.5;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px solid #0d3b7f;
        }

        .header td {
            padding-bottom: 18px;
            vertical-align: middle;
        }

        .brand {
            width: 58%;
        }

        .brand-description {
            margin: 4px 0 0;
            color: #238ccc;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .document-data {
            width: 42%;
            text-align: right;
        }

        .document-title {
            margin: 0;
            color: #0d3b7f;
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .folio {
            margin-top: 4px;
            color: #6b7280;
            font-size: 9px;
        }

        .section {
            margin-top: 22px;
        }

        .section-title {
            margin: 0 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #dbe4f0;
            color: #0d3b7f;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
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

        .information-table .label {
            display: block;
            margin-bottom: 3px;
            color: #6b7280;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .information-table .value {
            color: #111827;
            font-size: 10px;
            font-weight: bold;
        }

        .prescription {
            min-height: 290px;
            margin-top: 10px;
            padding: 20px 22px;
            border: 1px solid #dbe4f0;
            border-left: 5px solid #0d3b7f;
            background-color: #f8fafc;
        }

        .prescription-heading {
            margin: 0 0 16px;
            color: #0d3b7f;
            font-size: 16px;
            font-weight: bold;
        }

        .prescription-content {
            color: #1f2937;
            font-size: 11px;
            line-height: 1.75;
            overflow-wrap: break-word;
        }

        .signature-wrapper {
            margin-top: 46px;
            page-break-inside: avoid;
            text-align: center;
        }

        .signature-line {
            width: 260px;
            margin: 0 auto;
            border-top: 1px solid #374151;
        }

        .signature-name {
            margin: 8px 0 0;
            color: #111827;
            font-size: 11px;
            font-weight: bold;
        }

        .signature-detail {
            margin: 2px 0 0;
            color: #6b7280;
            font-size: 9px;
        }

        .contact-box {
            margin-top: 25px;
            padding: 11px 14px;
            border-radius: 4px;
            background-color: #edf5fc;
            color: #374151;
            font-size: 9px;
            text-align: center;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -36px;
            left: 0;
            padding-top: 8px;
            border-top: 1px solid #dbe4f0;
            color: #6b7280;
            font-size: 8px;
            text-align: center;
        }

        .muted {
            color: #6b7280;
            font-weight: normal;
        }

        .logo {
            display: block;
            width: 100px;
            max-height: 75px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    @php
    $cita = $receta->cita;
    $paciente = $cita->paciente;
    $medico = $cita->medico;

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
    $nombreMedico =
    $medico?->user?->name
    ?? 'Médico no disponible';
    }

    $fechaExpedicion = $receta->fecha_expedicion
    ? \Carbon\Carbon::parse(
    $receta->fecha_expedicion
    )->locale('es')->translatedFormat(
    'd \d\e F \d\e Y'
    )
    : 'No disponible';

    $fechaConsulta = $cita->fecha
    ? \Carbon\Carbon::parse(
    $cita->fecha
    )->format('d/m/Y')
    : 'No disponible';

    $horaConsulta = $cita->hora
    ? \Carbon\Carbon::parse(
    $cita->hora
    )->format('h:i A')
    : 'No disponible';

    $modalidad = match ($cita->modalidad) {
    'videoconsulta' => 'Videoconsulta',
    'presencial' => 'Presencial',
    default => 'No especificada',
    };

    $motivo = match ($cita->motivo) {
    'consulta_inicial' => 'Consulta inicial',
    'consulta_subsecuente' => 'Consulta subsecuente',
    'consulta_emergencia' => 'Consulta de emergencia',
    default => $cita->motivo
    ? ucfirst(str_replace('_', ' ', $cita->motivo))
    : 'No especificado',
    };

    $folio = 'REC-'
    . str_pad(
    (string) $receta->id,
    6,
    '0',
    STR_PAD_LEFT
    );


    $logoPath = public_path(
    'images/logo-receta.png'
    );

    $logoBase64 = file_exists($logoPath)
    ? 'data:image/png;base64,'
    . base64_encode(
    file_get_contents($logoPath)
    )
    : null;

    $universidad = $medico?->universidad;

    $universidadLogoRelativo =
    $universidad?->logo_path
    ?: 'images/universidades/default.png';

    $universidadLogoPath = public_path(
    $universidadLogoRelativo
    );

    if (! file_exists($universidadLogoPath)) {
    $universidadLogoPath = public_path(
    'images/universidades/default.png'
    );
    }

    $universidadLogoBase64 =
    file_exists($universidadLogoPath)
    ? 'data:image/png;base64,'
    . base64_encode(
    file_get_contents($universidadLogoPath)
    )
    : null;
    @endphp

    {{-- Encabezado --}}
    <table class="header">
        <tr>
            <td class="brand">
                @if ($logoBase64)
                <img
                    src="{{ $logoBase64 }}"
                    alt="Logotipo institucional"
                    class="logo">
                @endif

                <p class="brand-description">
                    Atención médica especializada
                </p>
            </td>

            <td class="document-data">
                <p class="document-title">
                    Receta médica
                </p>

                <p class="folio">
                    Folio: {{ $folio }}
                </p>

                <p class="folio">
                    Expedida: {{ $fechaExpedicion }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Datos del paciente --}}
    <section class="section">
        <h2 class="section-title">
            Información del paciente
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
        </table>
    </section>

    {{-- Datos de la consulta --}}
    <section class="section">
        <h2 class="section-title">
            Información de la consulta
        </h2>

        <table class="information-table">
            <tr>
                <td style="width: 25%;">
                    <span class="label">
                        Fecha
                    </span>

                    <span class="value">
                        {{ $fechaConsulta }}
                    </span>
                </td>

                <td style="width: 20%;">
                    <span class="label">
                        Horario
                    </span>

                    <span class="value">
                        {{ $horaConsulta }}
                        -
                        {{ $cita->hora_fin->format('h:i A') }}
                    </span>

                    <br>

                    <span class="muted">
                        {{ $cita->duracion_minutos ?? 15 }}
                        minutos
                    </span>
                </td>

                <td style="width: 25%;">
                    <span class="label">
                        Modalidad
                    </span>

                    <span class="value">
                        {{ $modalidad }}
                    </span>
                </td>

                <td style="width: 30%;">
                    <span class="label">
                        Motivo
                    </span>

                    <span class="value">
                        {{ $motivo }}
                    </span>
                </td>
            </tr>
        </table>
    </section>

    {{-- Contenido de la receta --}}
    <section class="section">
        <h2 class="section-title">
            Tratamiento e indicaciones
        </h2>

        <div class="prescription">
            <p class="prescription-heading">
                Indicaciones médicas
            </p>

            <div class="prescription-content">
                {!! nl2br(e($receta->contenido)) !!}
            </div>
        </div>
    </section>

    {{-- Firma del médico --}}
    {{-- Firma del médico --}}
    <div class="signature-wrapper">

        @if ($universidadLogoBase64)
        <img
            src="{{ $universidadLogoBase64 }}"
            alt="{{ $universidad?->nombre ?? 'Universidad' }}"
            style="
                display: block;
                width: 70px;
                max-height: 70px;
                margin: 0 auto 12px;
                object-fit: contain;
            ">
        @endif

        <div class="signature-line"></div>

        <p class="signature-name">
            Dr. {{ $nombreMedico }}
        </p>

        <p class="signature-detail">
            {{ $medico?->especialidad
            ?: 'Especialidad no registrada' }}
        </p>

        <p class="signature-detail">
            Cédula profesional:
            {{ $medico?->cedula
            ?: 'No registrada' }}
        </p>

        <p class="signature-detail">
            {{ $universidad?->nombre
            ?: 'Universidad no registrada' }}
        </p>
    </div>

    {{-- Contacto --}}
    <div class="contact-box">
        @if ($medico?->consultorio)
        Consultorio:
        {{ $medico->consultorio }}
        @endif

        @if ($medico?->telefono)
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Teléfono:
        {{ $medico->telefono }}
        @endif

        @if ($medico?->user?->email)
        &nbsp;&nbsp;|&nbsp;&nbsp;

        Correo:

        {{ $medico->user->email }}
        @endif
    </div>

    {{-- Pie de página --}}
    <div class="footer">
        <strong>
            Av. León de los Aldama #3475, Col. San Felipe de Jesús,
            Alc. G.A.M., CDMX, C.P. 07510
        </strong>

        <br>

        Tel. 55 6645 0302

        <br>

        Horario de atención de Lunes a Viernes de 9:00 a 18:00 hrs.
    </div>
</body>

</html>