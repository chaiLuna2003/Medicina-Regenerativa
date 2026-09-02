@php
$nombrePaciente = trim(
($cita->paciente?->nombre ?? '') . ' ' .
(
$cita->paciente?->apellido_paterno
?? $cita->paciente?->apellido
?? ''
) . ' ' .
($cita->paciente?->apellido_materno ?? '')
);

$nombreMedico = trim(
($cita->medico?->nombre ?? '') . ' ' .
($cita->medico?->apellido_paterno ?? '')
);

[$estadoClases, $puntoClases, $estadoTexto] = match ($cita->estado) {
'confirmada' => [
'bg-emerald-50 text-emerald-700',
'bg-emerald-500',
'Confirmada',
],
'en_espera' => [
'bg-amber-50 text-amber-700',
'bg-amber-500',
'En espera',
],
'en_consulta' => [
'bg-blue-50 text-blue-700',
'bg-blue-500',
'En consulta',
],
'finalizada' => [
'bg-gray-100 text-gray-600',
'bg-gray-500',
'Finalizada',
],
default => [
'bg-violet-50 text-violet-700',
'bg-violet-500',
'Programada',
],
};

$esAtrasada = ($tipoValoracion ?? null) === 'atrasada';
$estaRealizada = $cita->signoVital !== null;
@endphp

<article
    class="group flex flex-col gap-4 px-6 py-5
           transition hover:bg-gray-50
           lg:flex-row lg:items-center">

    {{-- Horario --}}
    <div class="flex items-center gap-4 lg:w-24 lg:shrink-0">
        <div
            @class([ 'flex h-12 w-16 items-center justify-center' , 'rounded-xl text-sm font-bold' , 'bg-red-50 text-red-700'=> $esAtrasada,
            'bg-emerald-50 text-emerald-700' => $estaRealizada,
            'bg-blue-50 text-blue-700' =>
            ! $esAtrasada && ! $estaRealizada,
            ])>
            {{ \Carbon\Carbon::parse($cita->hora)->format('H:i') }}
        </div>
    </div>

    {{-- Información --}}
    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-2">
            <h4 class="font-semibold text-gray-900">
                {{ $nombrePaciente ?: 'Paciente no disponible' }}
            </h4>

            <span
                class="inline-flex items-center gap-1.5
                       rounded-full px-2.5 py-1 text-xs
                       font-semibold {{ $estadoClases }}">

                <span
                    class="h-1.5 w-1.5 rounded-full
                           {{ $puntoClases }}">
                </span>

                {{ $estadoTexto }}
            </span>

            @if ($esAtrasada)
            <span
                class="inline-flex items-center gap-1.5
                           rounded-full bg-red-50 px-2.5 py-1
                           text-xs font-semibold text-red-700">

                <span
                    class="h-1.5 w-1.5 rounded-full bg-red-500">
                </span>

                Valoración atrasada
            </span>
            @elseif ($estaRealizada)
            <span
                class="inline-flex items-center gap-1.5
                           rounded-full bg-emerald-50 px-2.5 py-1
                           text-xs font-semibold text-emerald-700">

                <svg
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>

                Valoración realizada
            </span>
            @else
            <span
                class="inline-flex items-center gap-1.5
                           rounded-full bg-blue-50 px-2.5 py-1
                           text-xs font-semibold text-blue-700">

                <span
                    class="h-1.5 w-1.5 rounded-full bg-blue-500">
                </span>

                Próxima
            </span>
            @endif
        </div>

        <p class="mt-1 text-sm text-gray-500">
            Dr. {{ $nombreMedico ?: 'No asignado' }}
        </p>

        @if ($cita->motivo)
        <p class="mt-1 truncate text-sm text-gray-400">
            {{ $cita->motivo }}
        </p>
        @endif
    </div>

    {{-- Acción --}}
    <div class="shrink-0">
        @if ($estaRealizada)
        <a
            href="{{ route(
                    'signos-vitales.show',
                    $cita->signoVital
                ) }}"
            class="inline-flex w-full items-center
                       justify-center gap-2 rounded-xl
                       border border-emerald-200 bg-emerald-50
                       px-4 py-2.5 text-sm font-semibold
                       text-emerald-700 transition
                       hover:border-emerald-300
                       hover:bg-emerald-100 lg:w-auto">

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0
                           3 3 0 016 0z" />

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943
                           7.523 5 12 5
                           c4.478 0 8.268 2.943
                           9.542 7-1.274 4.057
                           -5.064 7-9.542 7
                           -4.477 0-8.268-2.943
                           -9.542-7z" />
            </svg>

            Ver signos vitales
        </a>
        @else
        <button
            type="button"
            data-abrir-modal-signos
            data-cita-id="{{ $cita->id }}"
            data-url="{{ route('signos-vitales.store', $cita) }}"
            data-paciente="{{ $nombrePaciente ?: 'Paciente no disponible' }}"
            @class([ 'inline-flex w-full items-center justify-center' , 'gap-2 rounded-xl px-4 py-2.5' , 'text-sm font-semibold text-white shadow-sm' , 'transition focus:outline-none focus:ring-2' , 'focus:ring-offset-2 lg:w-auto' , 'bg-red-600 hover:bg-red-500 focus:ring-red-600'=>
            $esAtrasada,
            'bg-gray-900 hover:bg-gray-800 focus:ring-gray-900' =>
            ! $esAtrasada,
            ])>

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4" />
            </svg>

            Registrar signos
        </button>
        @endif
    </div>
</article>