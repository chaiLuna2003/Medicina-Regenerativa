@unless (request()->user()->isMedico())

    <details
        name="informacion-paciente"
        class="group overflow-hidden rounded-2xl
               border border-slate-200
               bg-white shadow-sm">

        <summary
            class="flex items-center justify-between
                   cursor-pointer list-none px-5 py-4
                   transition hover:bg-slate-50
                   focus:outline-none focus-visible:ring-2
                   focus-visible:ring-inset focus-visible:ring-blue-500
                   [&::-webkit-details-marker]:hidden">

            <div>
                <h3 class="text-sm font-semibold text-slate-900">
                    Contacto
                </h3>

                <p class="mt-0.5 text-xs text-slate-400">
                    Teléfono y correo
                </p>
            </div>

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0 text-slate-400
                       transition-transform duration-200
                       group-open:rotate-180 motion-reduce:transition-none"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
            </svg>
        </summary>

        <div class="border-t border-slate-100">
            @if (
                request()->user()->isAdmin()
                || request()->user()->isRecepcionista()
            )
                <div class="flex justify-end px-5 pt-4">
                    <button
                        type="button"
                        onclick="abrirModalContacto()"
                        class="rounded-lg px-2.5 py-1.5 text-xs
                               font-semibold text-blue-600 transition
                               hover:bg-blue-50 hover:text-blue-800">
                        Editar
                    </button>
                </div>
            @endif

            <dl class="space-y-4 p-5">

            @foreach (
                [
                    'telefono' => 'Celular / WhatsApp',

                    'telefono_fijo' =>
                        'Teléfono',

                    'telefono_secundario' =>
                        'Teléfono secundario',

                    'email' =>
                        'Correo electrónico',
                ]
                as $campo => $etiqueta
            )

                <div>
                    <dt class="text-xs font-medium text-slate-400">
                        {{ $etiqueta }}
                    </dt>

                    <dd
                        class="mt-1 break-words
                               text-sm font-semibold
                               text-slate-800">
                        {{ $pacientes->{$campo}
                            ?: 'No registrado' }}
                    </dd>
                </div>

            @endforeach

            <div class="border-t border-slate-100 pt-4">

                <dt class="text-xs font-medium text-slate-400">
                    Domicilio
                </dt>

                <dd class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $pacientes->domicilio
                        ?: 'No registrado' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-slate-400">
                    Ciudad y estado
                </dt>

                <dd class="mt-1 text-sm font-semibold text-semibold text-slate-800">
                    {{
                        collect([
                            $pacientes->ciudad,
                            $pacientes->estado,
                        ])
                            ->filter()
                            ->implode(', ')
                        ?: 'No registrado'
                    }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-slate-400">
                    Código postal
                </dt>

                <dd class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $pacientes->codigo_postal
                        ?: 'No registrado' }}
                </dd>
            </div>

            <div class="border-t border-slate-100 pt-4">

                <dt class="text-xs font-medium text-slate-400">
                    Costo personalizado
                </dt>

                <dd class="mt-1 text-sm font-semibold text-slate-800">

                    @if (
                        $pacientes
                            ->costo_consulta_personalizado
                        !== null
                    )
                        ${{ number_format(
                            (float) $pacientes
                                ->costo_consulta_personalizado,
                            2
                        ) }}
                    @else
                        No configurado
                    @endif
                </dd>
            </div>
            </dl>
        </div>
    </details>

@endunless