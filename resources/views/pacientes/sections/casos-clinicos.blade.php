<details
    class="group overflow-hidden rounded-2xl border
           border-slate-200 bg-white shadow-sm"
>
    <summary
        class="flex cursor-pointer list-none items-center
               justify-between gap-4 px-5 py-4
               transition hover:bg-slate-50
               focus-visible:outline-none
               focus-visible:ring-2
               focus-visible:ring-inset
               focus-visible:ring-[#0D3B7F]"
    >
        <div class="flex min-w-0 items-center gap-3">
            <span
                class="flex h-10 w-10 shrink-0 items-center
                       justify-center rounded-xl bg-blue-50
                       text-[#0D3B7F]"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12h6m-6 4h6M9 8h2
                           M6 3h9l3 3v15H6V3Z"
                    />
                </svg>
            </span>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="font-bold text-slate-900">
                        Casos clínicos
                    </h2>

                    <span
                        class="inline-flex min-w-6 items-center
                               justify-center rounded-full
                               bg-blue-100 px-2 py-0.5 text-xs
                               font-bold text-[#0D3B7F]"
                    >
                        {{ $pacientes->casosClinicos->count() }}
                    </span>
                </div>

                <p class="mt-0.5 text-sm text-slate-500">
                    Seguimiento clínico y expedientes PDF
                </p>
            </div>
        </div>

        <svg
            class="h-5 w-5 shrink-0 text-slate-400
                   transition-transform duration-200
                   group-open:rotate-180"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="m6 9 6 6 6-6"
            />
        </svg>
    </summary>

    <div class="border-t border-slate-200 bg-slate-50/70 p-4 sm:p-5">
        @forelse ($pacientes->casosClinicos as $casoClinico)
            <article
                class="mb-4 rounded-xl border border-slate-200
                       bg-white p-4 last:mb-0 sm:p-5"
            >
                <div
                    class="flex flex-col gap-4
                           lg:flex-row lg:items-start
                           lg:justify-between"
                >
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3
                                class="break-words font-bold
                                       text-slate-900"
                            >
                                {{ $casoClinico->nombre }}
                            </h3>

                            <span
                                class="inline-flex rounded-full px-2.5
                                       py-1 text-xs font-semibold
                                       {{ $casoClinico->estado === 'activo'
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-slate-200 text-slate-600' }}"
                            >
                                {{ $casoClinico->estado === 'activo'
                                    ? 'Activo'
                                    : 'Cerrado' }}
                            </span>
                        </div>

                        <p class="mt-1 text-xs font-semibold text-slate-400">
                            Caso
                            CC-{{ str_pad(
                                (string) $casoClinico->id,
                                6,
                                '0',
                                STR_PAD_LEFT
                            ) }}
                        </p>

                        @if (filled($casoClinico->descripcion_inicial))
                            <p
                                class="mt-3 line-clamp-2 text-sm
                                       leading-6 text-slate-600"
                            >
                                {{ $casoClinico->descripcion_inicial }}
                            </p>
                        @endif

                        <dl
                            class="mt-4 grid gap-3
                                   sm:grid-cols-2 xl:grid-cols-4"
                        >
                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase
                                           tracking-wide text-slate-400"
                                >
                                    Fecha de inicio
                                </dt>
                                <dd
                                    class="mt-1 text-sm font-semibold
                                           text-slate-700"
                                >
                                    {{ $casoClinico->fecha_inicio
                                        ?->format('d/m/Y') ?? 'Sin fecha' }}
                                </dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase
                                           tracking-wide text-slate-400"
                                >
                                    Evoluciones
                                </dt>
                                <dd
                                    class="mt-1 text-sm font-semibold
                                           text-slate-700"
                                >
                                    {{ $casoClinico->evoluciones_count }}
                                </dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase
                                           tracking-wide text-slate-400"
                                >
                                    Registrado por
                                </dt>
                                <dd
                                    class="mt-1 break-words text-sm
                                           font-semibold text-slate-700"
                                >
                                    {{ $casoClinico->creadoPor?->name
                                        ?? 'No disponible' }}
                                </dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase
                                           tracking-wide text-slate-400"
                                >
                                    Fecha de cierre
                                </dt>
                                <dd
                                    class="mt-1 text-sm font-semibold
                                           text-slate-700"
                                >
                                    {{ $casoClinico->fecha_cierre
                                        ?->format('d/m/Y') ?? 'No cerrado' }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="shrink-0">
                        <a
                            href="{{ route(
                                'casos-clinicos.pdf',
                                $casoClinico
                            ) }}"
                            class="inline-flex w-full items-center
                                   justify-center gap-2 rounded-lg
                                   bg-[#0D3B7F] px-4 py-2.5
                                   text-sm font-semibold text-white
                                   transition hover:bg-[#092b5e]
                                   focus-visible:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-[#0D3B7F]
                                   focus-visible:ring-offset-2
                                   lg:w-auto"
                        >
                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 3v12m0 0 4-4m-4 4-4-4
                                       M5 19h14"
                                />
                            </svg>

                            Generar PDF actualizado
                        </a>
                    </div>
                </div>

                @if (
                    $casoClinico->estaCerrado()
                    && filled($casoClinico->motivo_cierre)
                )
                    <div
                        class="mt-4 rounded-lg border border-slate-200
                               bg-slate-50 px-4 py-3"
                    >
                        <p
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-slate-400"
                        >
                            Motivo de cierre
                        </p>

                        <p class="mt-1 text-sm text-slate-600">
                            {{ $casoClinico->motivo_cierre }}
                        </p>
                    </div>
                @endif
            </article>
        @empty
            <div
                class="rounded-xl border border-dashed
                       border-slate-300 bg-white px-5 py-8
                       text-center"
            >
                <p class="font-semibold text-slate-700">
                    No hay casos clínicos registrados
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Los casos aparecerán aquí cuando se genere
                    seguimiento clínico para este paciente.
                </p>
            </div>
        @endforelse
    </div>
</details>