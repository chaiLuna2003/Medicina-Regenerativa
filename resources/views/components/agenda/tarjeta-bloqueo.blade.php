@props([
    'bloqueo',
    'esInicio' => false,
    'esFinal' => false,
])

@php
    $formaBloqueo = match (true) {
        $esInicio && $esFinal =>
            'rounded-lg border',

        $esInicio =>
            'rounded-t-lg border-x border-t',

        $esFinal =>
            'rounded-b-lg border-x border-b',

        default =>
            'border-x',
    };
@endphp

<div
    class="h-full border-[#A84848] bg-[#A84848]
           {{ $formaBloqueo }}"
    title="{{ $bloqueo->motivo }}">

    @if ($esInicio)
        <button
            type="button"
            class="editar-bloqueo-agenda flex h-full w-full
                   items-center gap-2 overflow-hidden px-2
                   text-left transition hover:bg-[#963F3F]"
            data-bloqueo-id="{{ $bloqueo->id }}"
            data-update-url="{{ route('agenda-bloqueos.update', $bloqueo) }}"
            data-medico-id="{{ $bloqueo->medico_id }}"
            data-fecha="{{ $bloqueo->fecha->format('Y-m-d') }}"
            data-hora-inicio="{{ substr($bloqueo->hora_inicio, 0, 5) }}"
            data-hora-fin="{{ substr($bloqueo->hora_fin, 0, 5) }}"
            data-motivo="{{ $bloqueo->motivo }}">

            <span
                class="flex h-6 w-6 shrink-0 items-center
                       justify-center rounded-md bg-amber-500
                       text-white">
                <svg
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0
                           002-2v-6a2 2 0 00-2-2H6a2
                           2 0 00-2 2v6a2 2 0 002 2zm10-10
                           V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </span>

            <span class="min-w-0">
                <span
                    class="block truncate text-[11px]
                           font-bold text-white">
                    Agenda bloqueada
                </span>

                <span
                    class="block truncate text-[10px]
                           text-white/80">
                    {{ substr($bloqueo->hora_inicio, 0, 5) }}
                    –
                    {{ substr($bloqueo->hora_fin, 0, 5) }}
                </span>
            </span>
        </button>
    @endif
</div>