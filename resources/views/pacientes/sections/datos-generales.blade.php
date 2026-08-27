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
                Datos generales
            </h3>

            <p class="mt-0.5 text-xs text-slate-400">
                Información personal
            </p>
        </div>

        @if (request()->user()->isAdmin())

            <button
                type="button"
                onclick="abrirModalDatosGenerales()"
                class="inline-flex items-center gap-1.5
                       rounded-lg px-2.5 py-1.5
                       text-xs font-semibold text-blue-600
                       transition hover:bg-blue-50
                       hover:text-blue-800">

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
                        d="M16.862 4.487
                           18.55 2.8a1.875 1.875 0 1 1
                           2.652 2.652L10.582 16.07
                           a4.5 4.5 0 0 1-1.897 1.13
                           L6 18l.8-2.685
                           a4.5 4.5 0 0 1
                           1.13-1.897l8.932-8.931Z" />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 7.125V16.5
                           A2.25 2.25 0 0 1
                           17.25 18.75H5.625
                           A2.25 2.25 0 0 1
                           3.375 16.5V4.875
                           A2.25 2.25 0 0 1
                           5.625 2.625H15" />
                </svg>

                Editar
            </button>

        @endif
    </div>

    <dl
        class="grid grid-cols-1 gap-4 p-5
               sm:grid-cols-2
               lg:grid-cols-1
               xl:grid-cols-2">

        <div>
            <dt class="text-xs font-medium text-slate-400">
                Fecha de nacimiento
            </dt>

            <dd class="mt-1 text-sm font-semibold text-slate-800">
                {{ $pacientes->fecha_nacimiento
                    ?->format('d/m/Y')
                    ?? 'No registrada' }}
            </dd>
        </div>

        <div>
            <dt class="text-xs font-medium text-slate-400">
                Edad
            </dt>

            <dd class="mt-1 text-sm font-semibold text-slate-800">
                {{ $pacientes->edad
                    ?? 'No disponible' }}
            </dd>
        </div>

        <div>
            <dt class="text-xs font-medium text-slate-400">
                Sexo
            </dt>

            <dd class="mt-1 text-sm font-semibold text-slate-800">
                {{ $pacientes->sexo_texto }}
            </dd>
        </div>

        @unless (request()->user()->isMedico())

            <div>
                <dt class="text-xs font-medium text-slate-400">
                    Lugar de nacimiento
                </dt>

                <dd class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $pacientes->lugar_nacimiento
                        ?: 'No registrado' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-slate-400">
                    Ocupación
                </dt>

                <dd class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $pacientes->ocupacion
                        ?: 'No registrada' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-slate-400">
                    Religión
                </dt>

                <dd class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $pacientes->religion
                        ?: 'No registrada' }}
                </dd>
            </div>

        @endunless
    </dl>
</section>