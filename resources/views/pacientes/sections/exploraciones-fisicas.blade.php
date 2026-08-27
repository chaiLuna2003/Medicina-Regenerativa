  @php
                    $exploracionesFisicas = $pacientes
                    ->historiaClinica
                    ?->exploracionesFisicas
                    ?? collect();
                    @endphp

                    {{-- ========================================================= --}}
                    {{-- EXPLORACIONES FÍSICAS --}}
                    {{-- ========================================================= --}}

                    <details
                        class="group overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">

                        <summary
                            class="flex cursor-pointer list-none
               items-center justify-between
               gap-4 px-6 py-5">

                            <div>
                                <div class="flex items-center gap-2">

                                    <h3 class="font-semibold text-slate-900">
                                        Exploraciones físicas
                                    </h3>

                                    <span
                                        class="inline-flex min-w-6 items-center
                           justify-center rounded-full
                           bg-indigo-50 px-2 py-0.5
                           text-xs font-semibold
                           text-indigo-700">

                                        {{ $exploracionesFisicas->count() }}
                                    </span>
                                </div>

                                <p class="mt-1 text-xs text-slate-400">
                                    Historial clínico por consulta y signos vitales asociados
                                </p>
                            </div>

                            <div class="flex items-center gap-3">

                                @if (request()->user()->isMedico())
                                <button
                                    type="button"
                                    onclick="
                        event.preventDefault();
                        event.stopPropagation();
                        abrirModalExploracionFisica();
                    "
                                    class="inline-flex items-center
                           justify-center rounded-xl
                           bg-indigo-600 px-4 py-2
                           text-xs font-semibold
                           text-white shadow-sm
                           transition hover:bg-indigo-700">

                                    Registrar o editar
                                </button>
                                @endif

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                       transition duration-200
                       group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100">

                            @forelse ($exploracionesFisicas as $exploracion)

                            @php
                            $citaExploracion = $exploracion->cita;
                            $signosExploracion =
                            $citaExploracion?->signoVital;

                            $nombreMedico = trim(
                            ($exploracion->medico?->nombre ?? '')
                            . ' '
                            . ($exploracion->medico?->apellido_paterno ?? '')
                            . ' '
                            . ($exploracion->medico?->apellido_materno ?? '')
                            );
                            @endphp

                            <article
                                class="border-b border-slate-100
                       p-5 last:border-b-0">

                                {{-- Cabecera de la consulta --}}
                                <div
                                    class="flex flex-col justify-between
                           gap-3 sm:flex-row
                           sm:items-start">

                                    <div>
                                        <p
                                            class="text-sm font-semibold
                                   text-slate-900">

                                            Consulta del
                                            {{ $citaExploracion?->fecha
                                ? $citaExploracion
                                    ->fecha
                                    ->format('d/m/Y')
                                : 'Sin fecha' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Dr. {{ filled($nombreMedico)
                                ? $nombreMedico
                                : 'No disponible' }}

                                            @if ($citaExploracion?->hora)
                                            ·
                                            {{ \Carbon\Carbon::parse(
                                    $citaExploracion->hora
                                )->format('H:i') }}
                                            @endif
                                        </p>
                                    </div>

                                    <span
                                        class="inline-flex w-fit rounded-full
                               bg-emerald-50 px-3 py-1
                               text-xs font-semibold
                               text-emerald-700">

                                        Registro clínico
                                    </span>
                                </div>

                                {{-- Signos vitales reutilizados --}}
                                <section
                                    class="mt-5 rounded-xl
                           border border-slate-200
                           bg-slate-50 p-4">

                                    <p
                                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-500">
                                        Signos vitales de la consulta
                                    </p>

                                    @if ($signosExploracion)

                                    <div
                                        class="mt-3 grid grid-cols-2
                                   gap-3 sm:grid-cols-3
                                   lg:grid-cols-6">

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Peso
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $signosExploracion->peso ?? '—' }}
                                                @if ($signosExploracion->peso)
                                                kg
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Presión arterial
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                @if (
                                                $signosExploracion->presion_sistolica
                                                && $signosExploracion->presion_diastolica
                                                )
                                                {{ $signosExploracion->presion_sistolica }}
                                                /
                                                {{ $signosExploracion->presion_diastolica }}
                                                @else
                                                —
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                F. cardiaca
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $signosExploracion
                                        ->frecuencia_cardiaca ?? '—' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                F. respiratoria
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $signosExploracion
                                        ->frecuencia_respiratoria ?? '—' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Temperatura
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $signosExploracion->temperatura ?? '—' }}
                                                @if ($signosExploracion->temperatura)
                                                °C
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                SatO₂
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $signosExploracion
                                        ->saturacion_oxigeno ?? '—' }}
                                                @if ($signosExploracion->saturacion_oxigeno)
                                                %
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    @if ($signosExploracion->observaciones)
                                    <p
                                        class="mt-3 whitespace-pre-line
                                       border-t border-slate-200
                                       pt-3 text-xs text-slate-600">

                                        <span class="font-semibold">
                                            Observaciones de enfermería:
                                        </span>

                                        {{ $signosExploracion->observaciones }}
                                    </p>
                                    @endif

                                    @else

                                    <p class="mt-2 text-sm text-slate-400">
                                        Enfermería todavía no ha registrado signos
                                        vitales para esta cita.
                                    </p>

                                    @endif
                                </section>

                                {{-- Información narrativa --}}
                                <div
                                    class="mt-5 grid grid-cols-1 gap-4
                                            lg:grid-cols-2">

                                    @foreach (
                                    $camposExploracionFisica
                                    as $clave => $etiqueta
                                    )
                                    <div
                                        class="rounded-xl border
                                   border-slate-200 p-4">

                                        <p
                                            class="text-xs font-semibold
                                       text-slate-500">
                                            {{ $etiqueta }}
                                        </p>

                                        <p
                                            class="mt-2 whitespace-pre-line
                                       text-sm text-slate-700">

                                            {{ filled($exploracion->{$clave})
                                    ? $exploracion->{$clave}
                                    : 'No registrado' }}
                                        </p>
                                    </div>
                                    @endforeach
                                </div>

                                @php
                                $sistemasRegistrados =
                                $exploracion->sistemas ?? [];

                                $sistemasConDatos = collect(
                                $sistemasExploracionFisica
                                )->filter(
                                function ($etiqueta, $clave)
                                use ($sistemasRegistrados) {
                                return filled(
                                data_get(
                                $sistemasRegistrados,
                                $clave
                                )
                                );
                                }
                                );
                                @endphp

                                {{-- Exploración por sistemas --}}
                                <section class="mt-5">

                                    <div class="flex items-center justify-between gap-3">

                                        <div>
                                            <h4
                                                class="text-sm font-semibold
                       text-slate-900">
                                                Exploración por sistemas y órganos
                                            </h4>

                                            <p class="mt-1 text-xs text-slate-400">
                                                Hallazgos registrados durante la consulta
                                            </p>
                                        </div>

                                        <span
                                            class="rounded-full bg-indigo-50
                   px-3 py-1 text-xs font-semibold
                   text-indigo-700">

                                            {{ $sistemasConDatos->count() }}
                                            registrados
                                        </span>
                                    </div>

                                    @if ($sistemasConDatos->isNotEmpty())

                                    <div
                                        class="mt-4 grid grid-cols-1 gap-4
                   md:grid-cols-2
                   xl:grid-cols-3">

                                        @foreach (
                                        $sistemasConDatos
                                        as $clave => $etiqueta
                                        )
                                        @php
                                        $hallazgo = data_get(
                                        $sistemasRegistrados,
                                        $clave
                                        );

                                        $iniciales = collect(
                                        preg_split('/\s+/', $etiqueta)
                                        )
                                        ->filter()
                                        ->map(
                                        fn ($palabra) =>
                                        mb_strtoupper(
                                        mb_substr(
                                        $palabra,
                                        0,
                                        1
                                        )
                                        )
                                        )
                                        ->take(2)
                                        ->implode('');
                                        @endphp

                                        <article
                                            class="overflow-hidden rounded-xl
                           border border-slate-200
                           bg-white">

                                            <div
                                                class="flex items-center gap-3
                               border-b border-slate-100
                               bg-slate-50 px-4 py-3">

                                                <div
                                                    class="flex h-9 w-9 shrink-0
                                   items-center justify-center
                                   rounded-lg bg-indigo-100
                                   text-xs font-bold
                                   text-indigo-700">

                                                    {{ $iniciales }}
                                                </div>

                                                <p
                                                    class="text-sm font-semibold
                                   text-slate-800">
                                                    {{ $etiqueta }}
                                                </p>
                                            </div>

                                            <p
                                                class="whitespace-pre-line
                               p-4 text-sm text-slate-700">
                                                {{ $hallazgo }}
                                            </p>
                                        </article>
                                        @endforeach
                                    </div>

                                    @else

                                    <div
                                        class="mt-4 rounded-xl border
                   border-dashed border-slate-300
                   px-5 py-8 text-center">

                                        <p class="text-sm text-slate-400">
                                            No se registraron hallazgos específicos
                                            por sistema en esta consulta.
                                        </p>
                                    </div>

                                    @endif
                                </section>
                            </article>

                            @empty

                            <div class="px-6 py-10 text-center">

                                <p class="text-sm font-semibold text-slate-700">
                                    Sin exploraciones físicas registradas
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Las exploraciones realizadas durante cada consulta
                                    aparecerán en este historial.
                                </p>
                            </div>

                            @endforelse
                        </div>
                    </details>