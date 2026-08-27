   {{-- ================================================= --}}
                    {{-- ESTUDIOS CLÍNICOS --}}
                    {{-- ================================================= --}}
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
                       bg-violet-50 text-violet-600">
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
                                            d="M9 12h6m-6 4h6M7 3h7l4 4v14H7z" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Estudios clínicos
                                    </h3>

                                    <p class="text-xs text-slate-400">
                                        Documentos asociados al paciente
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span
                                    class="rounded-full bg-violet-50
                       px-2.5 py-1 text-xs
                       font-semibold text-violet-700">
                                    {{ $pacientes->estudios->count() }}
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

                            @forelse ($pacientes->estudios as $estudio)

                            <div
                                class="border-b border-slate-100
                       px-6 py-5 last:border-0">
                                <div
                                    class="flex flex-col gap-4
                           sm:flex-row
                           sm:items-start
                           sm:justify-between">

                                    {{-- Información --}}
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-semibold text-slate-900">
                                                {{ $estudio->nombre }}
                                            </p>

                                            <span
                                                class="rounded-full bg-red-50
                                       px-2 py-0.5
                                       text-[11px] font-semibold
                                       text-red-600">
                                                PDF
                                            </span>
                                        </div>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $estudio->fecha_estudio?->format('d/m/Y')
                                ?? 'Fecha no registrada' }}
                                        </p>

                                        @if ($estudio->descripcion)
                                        <p
                                            class="mt-2 max-w-2xl
                                       text-sm leading-5
                                       text-slate-600">
                                            {{ $estudio->descripcion }}
                                        </p>
                                        @endif

                                        <div
                                            class="mt-3 flex flex-wrap gap-x-4
                                   gap-y-1 text-xs text-slate-400">
                                            <span>
                                                Archivo:
                                                {{ $estudio->archivo_original
                                    ?? 'No disponible' }}
                                            </span>

                                            @if ($estudio->subidoPor)
                                            <span>
                                                Subido por:
                                                {{ $estudio->subidoPor->name }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Acciones --}}
                                    <div
                                        class="flex shrink-0
                               flex-wrap items-center gap-2">
                                        <a
                                            href="{{ route(
                                'estudios.archivo',
                                $estudio
                            ) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center
                                   gap-1.5 rounded-lg
                                   border border-slate-200
                                   bg-white px-3 py-2
                                   text-xs font-semibold
                                   text-slate-700 transition
                                   hover:bg-slate-50">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6.75
                                       9.75-6.75S21.75 12
                                       21.75 12 18 18.75
                                       12 18.75 2.25 12
                                       2.25 12Z" />

                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.75" />
                                            </svg>

                                            Ver
                                        </a>

                                        <a
                                            href="{{ route(
                                'estudios.descargar',
                                $estudio
                            ) }}"
                                            class="inline-flex items-center
                                   gap-1.5 rounded-lg
                                   bg-violet-600
                                   px-3 py-2
                                   text-xs font-semibold
                                   text-white transition
                                   hover:bg-violet-700">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 3v12m0 0
                                       4-4m-4 4-4-4
                                       M5 21h14" />
                                            </svg>

                                            Descargar
                                        </a>
                                    </div>

                                </div>
                            </div>

                            @empty

                            <div
                                class="px-6 py-10 text-center">
                                <p class="text-sm font-medium text-slate-600">
                                    No hay estudios registrados.
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Los estudios asociados al paciente
                                    aparecerán aquí.
                                </p>
                            </div>

                            @endforelse

                            @if ($pacientes->estudios->isNotEmpty())
                            <div
                                class="border-t border-slate-100
                       bg-slate-50/60 px-6 py-4
                       text-right">
                                <a
                                    href="{{ route(
                        'pacientes.estudios.index',
                        $pacientes
                    ) }}"
                                    class="text-sm font-semibold
                           text-violet-600
                           hover:text-violet-800">
                                    Ver historial completo →
                                </a>
                            </div>
                            @endif

                        </div>
                    </details>