@props([
'cita',
'esInicio' => false,
'esFinal' => false,
'mostrarNotas' => false,
])

@php
$color = match ($cita->estado_actual) {
'confirmada' =>
'border-emerald-500 bg-emerald-500 text-white',

'en_espera' =>
'border-amber-400 bg-amber-400 text-white',

'en_curso', 'en_consulta' =>
'border-blue-500 bg-blue-500 text-white',

'finalizada' =>
'border-slate-400 bg-slate-400 text-white',

'cancelada' =>
'border-red-200 bg-red-50 text-red-600',

default =>
'border-indigo-500 bg-indigo-500 text-white',
};

$bordes = $esInicio && $esFinal
? 'rounded-md'
: (
$esInicio
? 'rounded-t-md border-b-0'
: (
$esFinal
? 'rounded-b-md border-t-0'
: 'rounded-none border-y-0'
)
);

$paciente = trim(
($cita->paciente?->nombre ?? '')
. ' '
. ($cita->paciente?->apellido ?? '')
);

$paciente = $paciente
?: 'Paciente no disponible';

$duracion =
$cita->duracion_minutos ?? 15;

$titulo =
$paciente
. ' · '
. $duracion
. ' minutos';

if (
$mostrarNotas
&& filled($cita->notas)
) {
$titulo .= ' · ' . $cita->notas;
}
@endphp

<a
    href="{{ route('citas.show', $cita) }}"
    title="{{ $titulo }}"
    {{ $attributes->class([
        'flex h-full items-center gap-2 overflow-hidden border px-2',
        'text-xs font-semibold shadow-sm transition hover:brightness-95',
        $color,
        $bordes,
    ]) }}>
    @if ($esInicio)
    <span
        class="shrink-0 rounded bg-white/20
                   px-1.5 py-0.5 text-[9px]
                   font-bold uppercase">
        {{ $cita->modalidad === 'videoconsulta'
                ? 'Video'
                : 'Cita' }}
    </span>

    <div class="min-w-0 flex-1">
    <p class="truncate">
        {{ $paciente }} · {{ $duracion }} min
    </p>

    @if (
        $mostrarNotas
        && filled($cita->notas)
    )
        <p
            class="mt-0.5 truncate text-[10px]
                   font-medium opacity-80"
        >
            {{ \Illuminate\Support\Str::limit(
                $cita->notas,
                70
            ) }}
        </p>
    @endif
</div>
    @else
    <span
        class="h-1.5 w-full rounded-full
                   bg-white/25"></span>
    @endif
</a>