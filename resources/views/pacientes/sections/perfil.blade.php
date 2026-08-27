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