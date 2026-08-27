@unless (request()->user()->isMedico())

    <section
        class="overflow-hidden rounded-2xl
               border border-slate-200
               bg-white shadow-sm">

        <div
            class="flex items-center justify-between
                   border-b border-slate-100
                   px-5 py-4">

            <div>
                <h3 class="text-sm font-semibold text-slate-900">
                    Contacto
                </h3>

                <p class="mt-0.5 text-xs text-slate-400">
                    Teléfono y correo
                </p>
            </div>

            @if (
                request()->user()->isAdmin()
                || request()->user()->isRecepcionista()
            )
                <button
                    type="button"
                    onclick="abrirModalContacto()"
                    class="text-xs font-semibold
                           text-blue-600
                           hover:text-blue-800">
                    Editar
                </button>
            @endif
        </div>

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
    </section>

@endunless 