@props([
'cita',
'esInicio' => false,
'esFinal' => false,
'mostrarNotas' => false,
'abrirEnModal' => false,
])

@php
$color = match ($cita->estado_actual) {
'programada', 'confirmada' =>
'border-[#315F9F] bg-[#315F9F] text-white',

'en_espera', 'en_curso', 'en_consulta' =>
'border-[#F2C94C] bg-[#F2C94C] text-[#5F4500]',

'finalizada' =>
'border-[#347557] bg-[#347557] text-white',

'cancelada' =>
'border-[#A84848] bg-[#FDECEC] text-[#A84848]',

default =>
'border-[#315F9F] bg-[#315F9F] text-white',
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
    href="{{ $abrirEnModal ? '#' : route('citas.show', $cita) }}"
    @if ($abrirEnModal)
    data-cita-id="{{ $cita->id }}"
    @endif
    title="{{ $titulo }}"
    {{ $attributes->class([
        'abrir-modal-detalle-cita' => $abrirEnModal,
        'flex h-full items-center gap-2 overflow-hidden border px-2',
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
                   font-medium opacity-80">
            {{ \Illuminate\Support\Str::limit(
                $cita->notas,
                70
            ) }}
        </p>
        @endif
    </div>
    @if ($cita->estado_actual === 'finalizada')
    <span
        class="shrink-0 text-white"
        title="Cita finalizada"
        aria-label="Cita finalizada">
        <svg
            class="h-5 w-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2.5"
                d="M1 12l4 4L14 7" />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2.5"
                d="M9 16l3 3L23 8" />
        </svg>
    </span>
@endif
    @else
    <span
        class="h-1.5 w-full rounded-full
                   bg-white/25"></span>
    @endif
</a>