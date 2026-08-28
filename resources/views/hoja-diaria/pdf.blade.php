<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>
        Hoja diaria {{ $fecha->format('d/m/Y') }}
    </title>

    <style>
        @page {
            margin: 30px 34px 48px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9px;
            line-height: 1.35;
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

        .brand {
            width: 55%;
        }

        .document-data {
            width: 45%;
            text-align: right;
        }

        .logo {
            display: block;
            width: 92px;
            max-height: 60px;
            object-fit: contain;
        }

        .brand-fallback {
            margin: 0;
            color: #0d3b7f;
            font-size: 20px;
            font-weight: bold;
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
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .document-detail {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 8px;
        }

        .summary {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .summary td {
            padding: 8px 10px;
            border: 1px solid #dbe4f0;
            vertical-align: middle;
        }

        .summary-label {
            display: block;
            margin-bottom: 2px;
            color: #64748b;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .summary-value {
            color: #0f172a;
            font-size: 10px;
            font-weight: bold;
        }

        .section-title {
            margin: 16px 0 7px;
            color: #0d3b7f;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .appointments {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .appointments thead {
            display: table-header-group;
        }

        .appointments tr {
            page-break-inside: avoid;
        }

        .appointments th {
            padding: 7px 5px;
            border: 1px solid #0d3b7f;
            background-color: #0d3b7f;
            color: #ffffff;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 0.3px;
            text-align: left;
            text-transform: uppercase;
        }

        .appointments td {
            padding: 7px 5px;
            border: 1px solid #dbe4f0;
            color: #334155;
            font-size: 8px;
            vertical-align: middle;
            overflow-wrap: break-word;
        }

        .appointments tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .appointments .cancelled td {
            background-color: #f8fafc;
            color: #94a3b8;
        }

        .number,
        .center {
            text-align: center;
        }

        .patient-name {
            color: #0f172a;
            font-weight: bold;
        }

        .cancelled .patient-name {
            color: #64748b;
            text-decoration: line-through;
        }

        .status {
            display: inline-block;
            padding: 3px 5px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-finalizada {
            background-color: #bbf7d0;
            color: #14532d;
        }

        .status-confirmada {
            background-color: #ecfdf5;
            color: #047857;
        }

        .status-programada {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-cancelada {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .status-espera {
            background-color: #fff7ed;
            color: #c2410c;
        }

        .status-curso {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .status-default {
            background-color: #f1f5f9;
            color: #475569;
        }

        .empty {
            padding: 30px 15px !important;
            color: #64748b !important;
            text-align: center;
        }

        .totals {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
        }

        .totals td {
            padding: 7px 10px;
            border: 1px solid #dbe4f0;
            text-align: center;
        }

        .total-number {
            display: block;
            color: #0d3b7f;
            font-size: 14px;
            font-weight: bold;
        }

        .total-label {
            display: block;
            margin-top: 2px;
            color: #64748b;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -32px;
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
    $logoPath = public_path('images/logo-receta.png');

    $logoBase64 = file_exists($logoPath)
    ? 'data:image/png;base64,'
    . base64_encode(file_get_contents($logoPath))
    : null;

    $nombreMedicoSeleccionado = null;

    if ($medicoSeleccionado) {
    $nombreMedicoSeleccionado = trim(
    ($medicoSeleccionado->nombre ?? '')
    . ' '
    . ($medicoSeleccionado->apellido_paterno ?? '')
    . ' '
    . ($medicoSeleccionado->apellido_materno ?? '')
    );

    if ($nombreMedicoSeleccionado === '') {
    $nombreMedicoSeleccionado =
    $medicoSeleccionado->user?->name
    ?? 'Médico no disponible';
    }
    }

    $fechaDocumento = $fecha
    ->copy()
    ->locale('es')
    ->translatedFormat('l, d \d\e F \d\e Y');

    $generadoPor = auth()->user()?->name
    ?? 'Usuario del sistema';
    @endphp

    {{-- Encabezado --}}
    <table class="header">
        <tr>
            <td class="brand">
                @if ($logoBase64)
                <img
                    src="{{ $logoBase64 }}"
                    alt="Medicina Regenerativa"
                    class="logo">
                @else
                <p class="brand-fallback">
                    Medicina Regenerativa
                </p>
                @endif

                <p class="brand-description">
                    Atención médica especializada
                </p>
            </td>

            <td class="document-data">
                <p class="document-title">
                    Hoja diaria
                </p>

                <p class="document-detail">
                    {{ ucfirst($fechaDocumento) }}
                </p>

                <p class="document-detail">
                    Generada:
                    {{ now()->format('d/m/Y h:i A') }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Información general --}}
    <table class="summary">
        <tr>
            <td style="width: 30%;">
                <span class="summary-label">
                    Fecha de agenda
                </span>

                <span class="summary-value">
                    {{ $fecha->format('d/m/Y') }}
                </span>
            </td>

            <td style="width: 40%;">
                <span class="summary-label">
                    Médico
                </span>

                <span class="summary-value">
                    {{ $nombreMedicoSeleccionado
                        ? 'Dr. ' . $nombreMedicoSeleccionado
                        : 'Todos los médicos' }}
                </span>
            </td>

            <td style="width: 30%;">
                <span class="summary-label">
                    Generada por
                </span>

                <span class="summary-value">
                    {{ $generadoPor }}
                </span>
            </td>
        </tr>
    </table>

    <h2 class="section-title">
        Relación de citas
    </h2>

    {{-- Tabla de citas --}}
    <table class="appointments">
        <thead>
            <tr>
                <th style="width: 4%;" class="center">#</th>
                <th style="width: 8%;" class="center">Hora</th>
                <th style="width: 18%;">Paciente</th>
                <th style="width: 5%;" class="center">Edad</th>
                <th style="width: 7%;" class="center">Sexo</th>
                <th style="width: 16%;">Motivo</th>
                <th style="width: 10%;">Modalidad</th>
                <th style="width: 19%;">Médico</th>
                <th style="width: 13%;" class="center">Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($citas as $indice => $cita)
            @php
            $paciente = $cita->paciente;
            $medico = $cita->medico;

            $nombrePaciente = trim(
            ($paciente?->nombre ?? '')
            . ' '
            . ($paciente?->apellido ?? '')
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
            ?? 'No disponible';
            }

            $hora = $cita->hora
            ? \Carbon\Carbon::parse($cita->hora)
            ->format('h:i A')
            : 'N/D';

            $sexo = match ($paciente?->sexo) {
            'masculino' => 'Masculino',
            'femenino' => 'Femenino',
            default => $paciente?->sexo
            ? ucfirst($paciente->sexo)
            : 'N/D',
            };

            $motivo = match ($cita->motivo) {
            'consulta_inicial' => 'Consulta inicial',
            'consulta_subsecuente' => 'Consulta subsecuente',
            'consulta_emergencia' => 'Emergencia',
            default => $cita->motivo
            ? ucfirst(str_replace('_', ' ', $cita->motivo))
            : 'No especificado',
            };

            $modalidad = match ($cita->modalidad) {
            'presencial' => 'Presencial',
            'videoconsulta' => 'Videoconsulta',
            'telefonica' => 'Telefónica',
            'fuera_instalaciones' => 'Fuera de instalaciones',
            default => $cita->modalidad
            ? ucfirst(str_replace('_', ' ', $cita->modalidad))
            : 'No especificada',
            };

            [$estadoTexto, $estadoClase] = match ($cita->estado) {
            'finalizada' => [
            'Finalizada',
            'status-finalizada',
            ],

            'confirmada' => [
            'Confirmada',
            'status-confirmada',
            ],

            'programada' => [
            'Programada',
            'status-programada',
            ],

            'en_espera' => [
            'En espera',
            'status-espera',
            ],

            'en_curso' => [
            'En curso',
            'status-curso',
            ],

            'cancelada' => [
            'Cancelada',
            'status-cancelada',
            ],

            default => [
            ucfirst(
            str_replace(
            '_',
            ' ',
            $cita->estado ?? 'Sin estado'
            )
            ),
            'status-default',
            ],
            };
            @endphp

            <tr class="{{ $cita->estado === 'cancelada'
                    ? 'cancelled'
                    : '' }}">

                <td class="number">
                    {{ $indice + 1 }}
                </td>

                <td class="center">
                    {{ $hora }}
                </td>

                <td>
                    <span class="patient-name">
                        {{ $nombrePaciente ?: 'No disponible' }}
                    </span>
                </td>

                <td class="center">
                    {{ $paciente?->edad ?? 'N/D' }}
                </td>

                <td class="center">
                    {{ $sexo }}
                </td>

                <td>
                    {{ $motivo }}
                </td>

                <td>
                    {{ $modalidad }}
                </td>

                <td>
                    Dr. {{ $nombreMedico }}
                </td>

                <td class="center">
                    <span class="status {{ $estadoClase }}">
                        {{ $estadoTexto }}
                    </span>
                </td>
            </tr>

            @empty

            <tr>
                <td colspan="9" class="empty">
                    No hay citas registradas para esta selección.
                </td>
            </tr>

            @endforelse
        </tbody>
    </table>

    {{-- Totales --}}
    <table class="totals">
        <tr>
            <td style="width: 25%;">
                <span class="total-number">
                    {{ $totalCitas }}
                </span>

                <span class="total-label">
                    Citas registradas
                </span>
            </td>

            <td style="width: 25%;">
                <span class="total-number">
                    {{ $totalCitasActivas }}
                </span>

                <span class="total-label">
                    Citas activas
                </span>
            </td>

            <td style="width: 25%;">
                <span class="total-number">
                    {{ $totalPacientes }}
                </span>

                <span class="total-label">
                    Pacientes programados
                </span>
            </td>

            <td style="width: 25%;">
                <span class="total-number">
                    {{ $totalCitasCanceladas }}
                </span>

                <span class="total-label">
                    Citas canceladas
                </span>
            </td>
        </tr>
    </table>

    {{-- Pie de página --}}
    <div class="footer">
        <strong>
            Av. León de los Aldama #3475, Col. San Felipe de Jesús,
            Alc. G.A.M., CDMX, C.P. 07510
        </strong>

        &nbsp;&nbsp;|&nbsp;&nbsp;

        Tel. 55 6645 0302

        &nbsp;&nbsp;|&nbsp;&nbsp;

        Generado por el sistema de Medicina Regenerativa
    </div>
</body>

</html>