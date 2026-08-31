@php
    $aparatosPorClave =
        $aparatos->keyBy('aparato');

    $estilosEstadoAparato = [
        'no_evaluado' => [
            'etiqueta' => 'No evaluado',
            'punto' => 'bg-slate-300',
            'badge' => 'bg-slate-100 text-slate-600',
            'borde' => 'border-slate-200',
        ],

        'normal' => [
            'etiqueta' => 'Normal',
            'punto' => 'bg-emerald-500',
            'badge' => 'bg-emerald-100 text-emerald-700',
            'borde' => 'border-emerald-200',
        ],

        'requiere_atencion' => [
            'etiqueta' => 'Requiere atención',
            'punto' => 'bg-amber-400',
            'badge' => 'bg-amber-100 text-amber-800',
            'borde' => 'border-amber-200',
        ],

        'critico' => [
            'etiqueta' => 'Crítico',
            'punto' => 'bg-red-500',
            'badge' => 'bg-red-100 text-red-700',
            'borde' => 'border-red-200',
        ],
    ];
@endphp

<div class="mt-4 grid gap-3 lg:grid-cols-2">
    @foreach (
        \App\Models\EvolucionAparato::APARATOS
        as $clave => $configuracionAparato
    )
        @php
            $valoracionAparato =
                $aparatosPorClave->get($clave);

            $estadoAparato =
                $valoracionAparato?->estado
                ?? \App\Models\EvolucionAparato::ESTADO_NO_EVALUADO;

            $estiloAparato =
                $estilosEstadoAparato[$estadoAparato]
                ?? $estilosEstadoAparato['no_evaluado'];

            $observacionAparato =
                $valoracionAparato?->observaciones;
        @endphp

        <article
            class="rounded-xl border bg-white p-3
                   {{ $estiloAparato['borde'] }}">

            <div
                class="flex flex-col gap-2
                       sm:flex-row sm:items-start
                       sm:justify-between">

                <div
                    class="flex min-w-0
                           items-center gap-2.5">

                    <span
                        class="h-3 w-3 shrink-0
                               rounded-full
                               {{ $estiloAparato['punto'] }}"
                        aria-hidden="true">
                    </span>

                    <p
                        class="font-semibold
                               text-slate-800">
                        {{ $configuracionAparato['nombre'] }}
                    </p>
                </div>

                <span
                    class="w-fit shrink-0 rounded-full
                           px-2.5 py-1 text-[11px]
                           font-semibold
                           {{ $estiloAparato['badge'] }}">
                    {{ $estiloAparato['etiqueta'] }}
                </span>
            </div>

            @if ($observacionAparato)
                <p
                    class="mt-2 whitespace-pre-line
                           border-t border-slate-100
                           pt-2 text-xs leading-5
                           text-slate-600">
                    {{ $observacionAparato }}
                </p>
            @elseif (
                in_array(
                    $estadoAparato,
                    [
                        'requiere_atencion',
                        'critico',
                    ],
                    true
                )
            )
                <p
                    class="mt-2 text-xs font-medium
                           text-red-600">
                    Sin observación registrada.
                </p>
            @endif
        </article>
    @endforeach
</div>