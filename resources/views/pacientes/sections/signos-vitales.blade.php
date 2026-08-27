  {{-- ================================================= --}}
                    {{-- SIGNOS VITALES --}}
                    {{-- ================================================= --}}
                    @if (
                    request()->user()->isAdmin()
                    || request()->user()->role === 'medico'
                    || request()->user()->role === 'enfermero'
                    )
                    <details
                        class="group overflow-hidden rounded-2xl
                          border border-slate-200
                          bg-white shadow-sm">
                        <summary
                            class="flex cursor-pointer
                   list-none items-center
                   justify-between px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center
                           justify-center rounded-lg
                           bg-rose-50 text-rose-600">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 12h4l2-6 4 12 2-6h6" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Signos vitales
                                    </h3>

                                    <p class="text-xs text-slate-400">
                                        Registros clínicos del paciente
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span
                                    class="rounded-full bg-rose-50
                           px-2.5 py-1 text-xs
                           font-semibold text-rose-700">
                                    {{ $pacientes->signosVitales->count() }}
                                </span>

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                           transition group-open:rotate-180"
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

                            @forelse ($pacientes->signosVitales as $signo)

                            <div
                                class="border-b border-slate-100
                           px-6 py-5 last:border-0">
                                <div
                                    class="flex flex-col gap-4
                               lg:flex-row
                               lg:items-center
                               lg:justify-between">
                                    <div
                                        class="grid flex-1 gap-4
                                   sm:grid-cols-2
                                   xl:grid-cols-6">
                                        {{-- Fecha --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Fecha
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                {{ $signo->created_at?->format('d/m/Y')
                                        ?? '—' }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ $signo->created_at?->format('h:i A')
                                        ?? '' }}
                                            </p>
                                        </div>

                                        {{-- Peso --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Peso
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                {{ $signo->peso
                                        ? $signo->peso . ' kg'
                                        : '—' }}
                                            </p>
                                        </div>

                                        {{-- Presión --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Presión arterial
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                @if (
                                                $signo->presion_sistolica
                                                && $signo->presion_diastolica
                                                )
                                                {{ $signo->presion_sistolica }}
                                                /
                                                {{ $signo->presion_diastolica }}
                                                mmHg
                                                @else
                                                —
                                                @endif
                                            </p>
                                        </div>

                                        {{-- Frecuencia cardiaca --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                FC
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                {{ $signo->frecuencia_cardiaca
                                        ? $signo->frecuencia_cardiaca . ' lpm'
                                        : '—' }}
                                            </p>
                                        </div>

                                        {{-- Saturación --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                SpO₂
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                {{ $signo->spo2
                                        ? $signo->spo2 . '%'
                                        : '—' }}
                                            </p>
                                        </div>

                                        {{-- Temperatura --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Temperatura
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                {{ $signo->temperatura
                                        ? $signo->temperatura . ' °C'
                                        : '—' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Acción --}}
                                    @if (
                                    request()->user()->isAdmin()
                                    || request()->user()->role === 'enfermero'
                                    )
                                    <div class="shrink-0">
                                        <a
                                            href="{{ route(
                                        'signos-vitales.show',
                                        $signo
                                    ) }}"
                                            class="inline-flex items-center gap-1.5
                                           rounded-lg border
                                           border-slate-200
                                           bg-white px-3 py-2
                                           text-xs font-semibold
                                           text-slate-700 shadow-sm
                                           transition
                                           hover:border-rose-200
                                           hover:bg-rose-50
                                           hover:text-rose-700">
                                            Ver detalle

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            @empty

                            <div class="px-6 py-10 text-center">
                                <p class="text-sm font-medium text-slate-600">
                                    No hay signos vitales registrados.
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Los registros de enfermería aparecerán aquí.
                                </p>
                            </div>

                            @endforelse

                        </div>
                    </details>
                    @endif