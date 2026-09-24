<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tratamiento de precisión #{{ $control->id }}</title>
    <style>
        @page { margin: 18px 22px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; font: 9px/1.35 "DejaVu Sans", sans-serif; }
        h1 { margin: 2px 0 3px; color: #183f78; font-size: 17px; }
        h2 { margin: 0 0 5px; color: #183f78; font-size: 9px; text-transform: uppercase; }
        p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; }
        /* El marco cubre cada hoja; el contenido conserva su flujo natural. */
        .page-frame { position: fixed; top: 0; right: 0; bottom: 0; left: 0; border: 1.5px solid #183f78; }
        .sheet { padding: 16px 18px; }
        .header { border-bottom: 2px solid #183f78; padding-bottom: 11px; }
        .eyebrow { color: #526480; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: .7px; }
        .subtitle { color: #566478; font-size: 9px; }
        .meta { float: right; width: 135px; text-align: right; font-size: 8px; line-height: 1.6; }
        .meta strong { display: block; color: #183f78; font-size: 10px; }
        .info { margin-top: 11px; table-layout: fixed; }
        .info td, .measure td, .peptides th, .peptides td { border: 1px solid #cbd5e1; padding: 5px 7px; vertical-align: top; }
        .label { display: block; margin-bottom: 2px; color: #64748b; font-size: 7px; font-weight: bold; text-transform: uppercase; }
        .value { font-weight: bold; word-wrap: break-word; }
        .measure { margin-top: 9px; table-layout: fixed; }
        .measure td { width: 25%; text-align: center; }
        .section { margin-top: 10px; }
        .section h2 { page-break-after: avoid; }
        .panel { border: 1px solid #cbd5e1; padding: 6px 8px; word-wrap: break-word; }
        .numbered { margin: 2px 0; word-wrap: break-word; }
        .peptides { table-layout: fixed; }
        .peptides th { background: #edf4fb; color: #183f78; text-align: left; }
        .peptides td { word-wrap: break-word; }
        .peptides tr, .info tr, .measure tr { page-break-inside: avoid; }
        .footer { border-top: 1px solid #cbd5e1; margin-top: 12px; padding-top: 6px; color: #566478; font-size: 8px; }
    </style>
</head>
<body>
@php $cita = $control->cita; @endphp
<div class="page-frame"></div>
<main class="sheet">
    <div class="header">
        <div class="meta">
            Folio <strong>TP-{{ str_pad((string) $control->id, 6, '0', STR_PAD_LEFT) }}</strong>
            Registrado el {{ $control->created_at?->format('d/m/Y') ?? '—' }}
        </div>
        <div class="eyebrow">Registro de seguimiento</div>
        <h1>Tratamiento de precisión</h1>
        <div class="subtitle">Plan individual de tratamiento</div>
    </div>

    <table class="info"><tr>
        <td style="width: 42%"><span class="label">Paciente</span><span class="value">{{ trim($paciente->nombre.' '.$paciente->apellido) }}</span></td>
        <td style="width: 16%"><span class="label">Edad</span><span class="value">{{ $paciente->edad ?? '—' }}</span></td>
        <td style="width: 25%"><span class="label">Fecha de cita</span><span class="value">{{ $cita?->fecha?->format('d/m/Y') ?? '—' }}</span></td>
        <td style="width: 17%"><span class="label">Cita</span><span class="value">#{{ $control->cita_id }}</span></td>
    </tr></table>

    <div class="section"><h2>Diagnóstico</h2><div class="panel">{!! nl2br(e($control->diagnostico ?: '—')) !!}</div></div>

    <table class="measure">
        @foreach (array_chunk([
            'Peso' => [$control->peso, 'kg'], 'Talla' => [$control->talla, 'cm'],
            'IMC' => [$control->imc, ''], 'Índice antioxidante' => [$control->indice_antioxidante, ''],
            'Grasa' => [$control->porcentaje_grasa, '%'], 'Músculo' => [$control->porcentaje_musculo, '%'],
            'Agua' => [$control->porcentaje_agua, '%'], 'Hueso' => [$control->porcentaje_hueso, '%'],
        ], 4, true) as $fila)
            <tr>
                @foreach ($fila as $etiqueta => [$valor, $unidad])
                    <td><span class="label">{{ $etiqueta }}</span><span class="value">{{ $valor !== null && $valor !== '' ? trim($valor.' '.$unidad) : '—' }}</span></td>
                @endforeach
            </tr>
        @endforeach
    </table>

    <div class="section"><h2>Objetivos terapéuticos</h2><div class="panel">
        @forelse ($control->objetivos ?? [] as $objetivo)
            <p class="numbered">{{ $loop->iteration }}. {{ $objetivo }}</p>
        @empty — @endforelse
    </div></div>

    <div class="section"><h2>Tratamiento base</h2><div class="panel">{!! nl2br(e($control->tratamiento_base ?: '—')) !!}</div></div>
    <div class="section"><h2>Tratamiento complementario</h2><div class="panel">{!! nl2br(e($control->tratamiento_complementario ?: '—')) !!}</div></div>

    <div class="section"><h2>Péptidos de precisión</h2>
        <table class="peptides"><thead><tr><th>Péptido</th><th>Dosis</th><th>Tiempo de uso</th></tr></thead><tbody>
            @forelse ($control->peptidos ?? [] as $peptido)
                <tr><td>{{ $peptido['nombre'] ?: '—' }}</td><td>{{ $peptido['dosis'] ?: '—' }}</td><td>{{ $peptido['tiempo'] ?: '—' }}</td></tr>
            @empty <tr><td colspan="3">—</td></tr> @endforelse
        </tbody></table>
    </div>

    <div class="section"><h2>Suplementos y cofactores indicados</h2><div class="panel">
        @forelse ($control->suplementos ?? [] as $suplemento)
            <p class="numbered">{{ $loop->iteration }}. {{ $suplemento }}</p>
        @empty — @endforelse
    </div></div>

    <div class="section"><h2>Indicación dietética</h2><div class="panel">{!! nl2br(e($control->indicacion_dietetica ?: '—')) !!}</div></div>
    <div class="section"><h2>Actividad física indicada</h2><div class="panel">{!! nl2br(e($control->actividad_fisica ?: '—')) !!}</div></div>

    <div class="footer">Tratamiento #{{ $control->id }} · Cita #{{ $control->cita_id }}</div>
</main>
</body>
</html>
