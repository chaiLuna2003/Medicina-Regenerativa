<details id="control-peso" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-5 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700" aria-hidden="true">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M5 16l3-8h8l3 8H5Zm7-8V5m-3 0h6" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-slate-900">Tratamiento de precisión</h3>
                <p class="text-xs text-slate-500">Historial de tratamientos y planes terapéuticos</p>
            </div>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">{{ $pacientes->controlesPeso->count() }}</span>
            <svg class="h-5 w-5 text-slate-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
            </svg>
        </div>
    </summary>

    <div class="border-t border-slate-100">
        @if (request()->user()->isMedico() || request()->user()->isEnfermero())
            <div class="border-b border-slate-100 px-4 py-4 sm:px-6">
                @php
                    $citasControlPeso = $pacientes->citas->filter(fn ($cita) =>
                        request()->user()->isEnfermero()
                        || $cita->medico_id === request()->user()->medico?->id
                    );
                @endphp
                <button type="button" onclick="abrirModalControlPeso()" @disabled($citasControlPeso->isEmpty())
                    class="w-full rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto">
                    Nuevo tratamiento de precisión
                </button>
                @if ($citasControlPeso->isEmpty())
                    <p class="mt-2 text-xs text-slate-500">Se necesita una cita autorizada para registrar un tratamiento.</p>
                @endif
            </div>
        @endif

        @forelse ($pacientes->controlesPeso as $control)
            <div class="border-b border-slate-100 px-4 py-5 last:border-0 sm:px-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900">
                            Tratamiento #{{ $control->id }} · {{ $control->cita?->fecha?->format('d/m/Y') ?? 'Fecha no disponible' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Cita #{{ $control->cita_id }} · {{ $control->cita?->medico?->user?->name ?? 'Médico no disponible' }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('pacientes.controles-peso.pdf', [$pacientes, $control]) }}"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            Descargar PDF
                        </a>
                        @if (request()->user()->isEnfermero() || (request()->user()->isMedico() && $control->cita?->medico_id === request()->user()->medico?->id))
                            <button type="button" onclick="abrirModalControlPeso({{ $control->id }})"
                                class="rounded-lg border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50">
                                Editar
                            </button>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-sm sm:grid-cols-3">
                    <div><span class="block text-xs text-slate-500">Peso</span><strong>{{ $control->peso }} kg</strong></div>
                    <div><span class="block text-xs text-slate-500">Talla</span><strong>{{ $control->talla }} cm</strong></div>
                    <div><span class="block text-xs text-slate-500">IMC</span><strong>{{ $control->imc }}</strong></div>
                </div>

                <details class="mt-3 rounded-lg border border-slate-200">
                    <summary class="cursor-pointer px-3 py-2 text-sm font-medium text-blue-700">Consultar registro completo</summary>
                    <div class="space-y-4 border-t border-slate-200 px-3 py-4 text-sm text-slate-700">
                        <div><strong>Diagnóstico:</strong> {{ $control->diagnostico ?: '—' }}</div>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                            <div><span class="block text-xs text-slate-500">Grasa</span>{{ $control->porcentaje_grasa !== null ? $control->porcentaje_grasa.' %' : '—' }}</div>
                            <div><span class="block text-xs text-slate-500">Músculo</span>{{ $control->porcentaje_musculo !== null ? $control->porcentaje_musculo.' %' : '—' }}</div>
                            <div><span class="block text-xs text-slate-500">Agua</span>{{ $control->porcentaje_agua !== null ? $control->porcentaje_agua.' %' : '—' }}</div>
                            <div><span class="block text-xs text-slate-500">Hueso</span>{{ $control->porcentaje_hueso !== null ? $control->porcentaje_hueso.' %' : '—' }}</div>
                            <div><span class="block text-xs text-slate-500">Índice antioxidante</span>{{ $control->indice_antioxidante ?: '—' }}</div>
                        </div>
                        <div><strong>Objetivos terapéuticos:</strong>
                            @forelse ($control->objetivos ?? [] as $objetivo)
                                <p class="whitespace-pre-wrap">{{ $loop->iteration }}. {{ $objetivo }}</p>
                            @empty <span>—</span> @endforelse
                        </div>
                        <div><strong>Tratamiento base:</strong> <span class="whitespace-pre-wrap">{{ $control->tratamiento_base ?: '—' }}</span></div>
                        <div><strong>Tratamiento complementario:</strong> <span class="whitespace-pre-wrap">{{ $control->tratamiento_complementario ?: '—' }}</span></div>
                        <div><strong>Péptidos de precisión:</strong>
                            @forelse ($control->peptidos ?? [] as $peptido)
                                <p>{{ $peptido['nombre'] ?: '—' }} · Dosis: {{ $peptido['dosis'] ?: '—' }} · Tiempo: {{ $peptido['tiempo'] ?: '—' }}</p>
                            @empty <span>—</span> @endforelse
                        </div>
                        <div><strong>Suplementos y cofactores:</strong>
                            @forelse ($control->suplementos ?? [] as $suplemento)
                                <p>{{ $loop->iteration }}. {{ $suplemento }}</p>
                            @empty <span>—</span> @endforelse
                        </div>
                        <div><strong>Indicación dietética:</strong> <span class="whitespace-pre-wrap">{{ $control->indicacion_dietetica ?: '—' }}</span></div>
                        <div><strong>Actividad física indicada:</strong> <span class="whitespace-pre-wrap">{{ $control->actividad_fisica ?: '—' }}</span></div>
                    </div>
                </details>

                @if (request()->user()->isEnfermero() || (request()->user()->isMedico() && $control->cita?->medico_id === request()->user()->medico?->id))
                    <script type="application/json" id="datos-control-peso-{{ $control->id }}">{!! json_encode($control->only([
                        'cita_id', 'diagnostico', 'peso', 'talla', 'imc', 'porcentaje_grasa',
                        'porcentaje_musculo', 'porcentaje_agua', 'porcentaje_hueso',
                        'indice_antioxidante', 'objetivos', 'tratamiento_base',
                        'tratamiento_complementario', 'peptidos', 'suplementos',
                        'indicacion_dietetica', 'actividad_fisica',
                    ]), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
                @endif
            </div>
        @empty
            <p class="px-4 py-6 text-sm text-slate-500 sm:px-6">Todavía no hay tratamientos de precisión registrados.</p>
        @endforelse
    </div>
</details>
