<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="space-y-5 p-4 sm:p-6">
        {{-- Identidad --}}
        <div class="flex min-w-0 items-start gap-3 sm:gap-4">
            <img
                src="{{ $pacientes->fotoUrl() }}"
                alt="Foto de {{ $pacientes->nombre }}"
                class="h-20 w-20 shrink-0 rounded-xl border border-slate-200 object-cover shadow-sm sm:h-24 sm:w-24">

            <div class="min-w-0 flex-1 pt-0.5">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <h1 class="min-w-0 break-words text-lg font-bold leading-snug text-slate-900 sm:text-xl">
                        {{ $pacientes->nombre }} {{ $pacientes->apellido }}
                    </h1>
                    @if ($pacientes->finado)
                        <x-luto size="lg" />
                    @endif
                </div>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $pacientes->edad ?? 'Edad no disponible' }}
                </p>
                <p class="mt-2 text-xs font-medium text-slate-500">
                    Paciente #{{ $pacientes->id }}
                </p>
            </div>
        </div>

        {{-- Datos breves --}}
        <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-4">
            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                {{ $pacientes->sexo_texto }}
            </span>

            @if ($pacientes->finado)
                <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                    Finado
                </span>
            @endif

            @unless (request()->user()->isMedico())
                @php
                    $estiloCategoria = $pacientes->categoria_estilo;
                    $estiloCategoriaInline = sprintf(
                        'background-color: %s; color: %s; border-color: %s;',
                        $estiloCategoria['fondo'],
                        $estiloCategoria['texto'],
                        $estiloCategoria['borde']
                    );
                @endphp
                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold"
                    style="{{ $estiloCategoriaInline }}">
                    {{ $pacientes->categoria_texto }}
                </span>
            @endunless
        </div>

        @if (filled($pacientes->alergias))
            <div data-alerta-alergias role="alert"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-900">
                <div class="flex items-start gap-2.5">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94 a2 2 0 0 0 1.71-3L13.71 3.86 a2 2 0 0 0-3.42 0z" />
                    </svg>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wide text-red-700">Alergias</p>
                        <p class="mt-1 break-words text-sm font-semibold leading-5">{{ $pacientes->alergias }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="border-t border-slate-100 pt-4">
            <div class="flex flex-col items-start gap-3 xl:flex-row xl:items-center xl:justify-between">
                <h2 class="text-sm font-semibold text-slate-800">Clasificación del paciente</h2>
                @if (request()->user()->isMedico() || request()->user()->isEnfermero())
                    <button type="button"
                        onclick="document.getElementById('modal-clasificaciones').classList.remove('hidden'); document.getElementById('modal-clasificaciones').classList.add('flex')"
                        class="w-full rounded-lg border border-blue-200 px-3 py-2 text-center text-sm font-semibold text-blue-700 transition hover:bg-blue-50 xl:w-auto xl:py-1.5 xl:text-xs">
                        Editar clasificación
                    </button>
                @endif
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                @forelse ($pacientes->clasificaciones ?? [] as $clasificacion)
                    <span class="max-w-full break-words rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-800">
                        {{ \App\Models\Pacientes::CLASIFICACIONES[$clasificacion] ?? $clasificacion }}
                    </span>
                @empty
                    <span class="text-sm text-slate-500">Sin clasificación registrada.</span>
                @endforelse
            </div>
        </div>
    </div>
</section>
