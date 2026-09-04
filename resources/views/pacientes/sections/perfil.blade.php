<section
    class="overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">

    <div class="p-6">

        <div class="flex items-start gap-4">

            <img
                src="{{ $pacientes->fotoUrl() }}"
                alt="Foto de {{ $pacientes->nombre }}"
                class="h-24 w-24 shrink-0
                       rounded-xl border
                       border-slate-200
                       object-cover shadow-sm">

            <div class="min-w-0 flex-1">

                <h1
                    class="text-xl font-bold
                           text-slate-900">
                    {{ $pacientes->nombre }}
                    {{ $pacientes->apellido }}
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $pacientes->edad
                        ?? 'Edad no disponible' }}
                </p>

                @if (filled($pacientes->alergias))

                    <div
                        data-alerta-alergias
                        role="alert"
                        class="mt-3 rounded-xl border
                               border-red-200 bg-red-50
                               px-3 py-2.5 text-red-900">

                        <div class="flex items-start gap-2">

                            <svg
                                class="mt-0.5 h-4 w-4 shrink-0
                                       text-red-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v4m0 4h.01M10.29 3.86
                                       1.82 18a2 2 0 0 0 1.71 3h16.94
                                       a2 2 0 0 0 1.71-3L13.71 3.86
                                       a2 2 0 0 0-3.42 0z" />
                            </svg>

                            <div class="min-w-0">

                                <p
                                    class="text-xs font-bold uppercase
                                           tracking-wide text-red-700">
                                    Alergias
                                </p>

                                <p
                                    class="mt-0.5 break-words text-sm
                                           font-semibold leading-5">
                                    {{ $pacientes->alergias }}
                                </p>
                            </div>
                        </div>
                    </div>

                @endif

                {{-- Sexo y condición --}}

                <div class="mt-2 flex flex-wrap gap-2">

                    <span
                        class="inline-flex rounded-full
                               bg-slate-100 px-2.5 py-1
                               text-xs font-semibold
                               text-slate-700">

                        {{ $pacientes->sexo_texto }}
                    </span>

                    @if ($pacientes->finado)

                        <span
                            class="inline-flex rounded-full
                                   bg-red-100 px-2.5 py-1
                                   text-xs font-semibold
                                   text-red-700">
                            Finado
                        </span>

                    @endif
                </div>

                {{-- ID y categoría --}}

                <div class="mt-2 flex flex-wrap gap-2">

                    <span
                        class="inline-flex rounded-full
                               bg-blue-50 px-2.5 py-1
                               text-xs font-semibold
                               text-blue-700">

                        Paciente #{{ $pacientes->id }}
                    </span>

                    @unless (request()->user()->isMedico())

                        @php
                            $estiloCategoria =
                                $pacientes->categoria_estilo;

                            $estiloCategoriaInline = sprintf(
                                'background-color: %s; color: %s; border-color: %s;',
                                $estiloCategoria['fondo'],
                                $estiloCategoria['texto'],
                                $estiloCategoria['borde']
                            );
                        @endphp

                        <span
                            class="inline-flex rounded-full
                                   border px-2.5 py-1
                                   text-xs font-semibold"
                            style="{{ $estiloCategoriaInline }}">

                            {{ $pacientes->categoria_texto }}
                        </span>

                    @endunless
                </div>
            </div>
        </div>
    </div>
</section>