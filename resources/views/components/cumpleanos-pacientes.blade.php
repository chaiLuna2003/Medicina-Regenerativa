@props([
    'pacientes',
])

@once
    <style>
        @keyframes cumpleanos-destacado {
            0%,
            100% {
                background-color: #ffffff;
                box-shadow: inset 0 0 0 0 rgba(236, 72, 153, 0);
            }

            50% {
                background-color: #fdf2f8;
                box-shadow: inset 4px 0 0 rgba(236, 72, 153, 0.75);
            }
        }

  .cumpleanos-hoy {
    animation:
        cumpleanos-destacado
        1.8s
        ease-in-out
        infinite;
}

/*
 * Permite detenerla temporalmente al colocar
 * el cursor o navegar dentro de la tarjeta.
 */
.cumpleanos-hoy:hover,
.cumpleanos-hoy:focus-within {
    animation-play-state: paused;
}

@media (prefers-reduced-motion: reduce) {
    .cumpleanos-hoy {
        animation: none;
    }
}

        @media (prefers-reduced-motion: reduce) {
            .cumpleanos-hoy {
                animation: none;
            }
        }
    </style>
@endonce

<section
    aria-labelledby="titulo-cumpleanos-pacientes"
    class="overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">

    {{-- Encabezado --}}
    <div
        class="flex items-center justify-between gap-4
               border-b border-slate-100
               px-4 py-4 sm:px-6 sm:py-5">

        <div class="flex min-w-0 items-center gap-3">

            <div
                aria-hidden="true"
                class="flex h-10 w-10 shrink-0
                       items-center justify-center
                       rounded-xl bg-pink-50
                       text-pink-600">
                🎂
            </div>

            <div class="min-w-0">

                <h3
                    id="titulo-cumpleanos-pacientes"
                    class="font-semibold text-slate-900">
                    Cumpleaños de pacientes
                </h3>

                <p class="mt-0.5 text-xs text-slate-400">
                    Hoy y próximos 7 días
                </p>
            </div>
        </div>

        <span
            aria-label="{{ $pacientes->count() }}
                cumpleaños próximos"
            class="inline-flex min-w-8 shrink-0
                   items-center justify-center
                   rounded-full bg-pink-50
                   px-2.5 py-1 text-xs
                   font-semibold text-pink-700">

            {{ $pacientes->count() }}
        </span>
    </div>

    {{-- Pacientes --}}
    <div class="divide-y divide-slate-100">

        @forelse ($pacientes as $paciente)

            @php
                $esHoy =
    (int) $paciente->dias_para_cumpleanos === 0;

                $telefono = preg_replace(
                    '/\D+/',
                    '',
                    (string) $paciente->telefono
                );

                if (strlen($telefono) === 10) {
                    $telefono = '52' . $telefono;
                }

                $mensaje = rawurlencode(
                    "¡Hola {$paciente->nombre}! 🎉 "
                    . "De parte de todo el equipo de la clínica "
                    . "te deseamos un feliz cumpleaños. "
                    . "Esperamos que tengas un excelente día."
                );
            @endphp

            <article
                @class([
                    'flex flex-col gap-4 px-4 py-5',
                    'sm:flex-row sm:items-center sm:justify-between',
                    'cumpleanos-hoy' => $esHoy,
                ])
                @if ($esHoy)
                    aria-label="Cumpleaños de hoy:
                        {{ $paciente->nombre }}
                        {{ $paciente->apellido }}"
                @endif>

                {{-- Información --}}
                <div class="flex min-w-0 items-center gap-4">

                    <div class="relative shrink-0">

                        <img
                            src="{{ $paciente->fotoUrl() }}"
                            alt="Foto de {{ $paciente->nombre }}"
                            class="h-12 w-12 rounded-xl
                                   border border-slate-200
                                   object-cover">

                        @if ($esHoy)
                            <span
                                aria-hidden="true"
                                class="absolute -right-1 -top-1
                                       flex h-4 w-4 items-center
                                       justify-center rounded-full
                                       border-2 border-white
                                       bg-pink-500">
                            </span>
                        @endif
                    </div>

                    <div class="min-w-0">

                        <div class="flex flex-wrap items-center gap-2">

                            <p
                                class="truncate font-semibold
                                       text-slate-900">

                                {{ $paciente->nombre }}
                                {{ $paciente->apellido }}
                            </p>

                            @if ($esHoy)
                                <span
                                    class="rounded-full bg-pink-100
                                           px-2 py-0.5 text-[11px]
                                           font-bold text-pink-700">

                                    Cumpleaños hoy
                                </span>
                            @endif
                        </div>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $paciente
                                ->proximo_cumpleanos
                                ->format('d/m/Y') }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Cumple
                            {{ $paciente->edad_cumpleanos }}
                            años

                            @unless ($esHoy)
                                · En
                                {{ $paciente->dias_para_cumpleanos }}
                                {{ (int) $paciente->dias_para_cumpleanos === 1
    ? 'día'
    : 'días' }}
                            @endunless
                        </p>
                    </div>
                </div>

                {{-- Acciones --}}
                <div
                    class="flex w-full flex-col gap-2
                           sm:w-auto sm:flex-row sm:flex-wrap">

                    <a
                        href="{{ route(
                            'pacientes.show',
                            $paciente
                        ) }}"
                        class="inline-flex w-full items-center
                               justify-center rounded-lg
                               border border-slate-200
                               bg-white px-3 py-2
                               text-xs font-semibold
                               text-slate-700 transition
                               hover:bg-slate-50
                               focus:outline-none focus:ring-2
                               focus:ring-slate-300
                               sm:w-auto">

                        Ver paciente
                    </a>

                    @if ($telefono)
                        <a
                            href="https://wa.me/{{ $telefono }}?text={{ $mensaje }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Felicitar por WhatsApp a
                                {{ $paciente->nombre }}"
                            class="inline-flex w-full items-center
                                   justify-center rounded-lg
                                   bg-green-600 px-3 py-2
                                   text-xs font-semibold
                                   text-white transition
                                   hover:bg-green-700
                                   focus:outline-none focus:ring-2
                                   focus:ring-green-300
                                   sm:w-auto">

                            Felicitar por WhatsApp
                        </a>
                    @endif
                </div>
            </article>

        @empty

            <div class="px-4 py-10 text-center sm:px-6">

                <p class="text-sm font-medium text-slate-600">
                    No hay cumpleaños próximos.
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Aquí aparecerán los pacientes que cumplan
                    años durante los próximos 7 días.
                </p>
            </div>

        @endforelse
    </div>
</section>