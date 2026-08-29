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
                Datos generales
            </h3>

            <p class="mt-0.5 text-xs text-slate-400">
                Información personal
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
        @if (request()->user()->isAdmin())
            <div class="flex justify-end px-5 pt-4">
                <button
                    type="button"
                    onclick="abrirModalDatosGenerales()"
                    class="rounded-lg px-2.5 py-1.5 text-xs
                           font-semibold text-blue-600 transition
                           hover:bg-blue-50 hover:text-blue-800">
                    Editar
                </button>
            </div>
        @endif

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

        <div>
            <dt class="text-xs font-medium text-slate-400">
                Estado civil
            </dt>

            <dd class="mt-1 text-sm font-semibold text-slate-800">
                {{ \App\Models\Pacientes::ESTADOS_CIVILES[
                    $pacientes->estado_civil
                ] ?? 'No registrado' }}
            </dd>
        </div>

        <div>
            <dt class="text-xs font-medium text-slate-400">
                Escolaridad
            </dt>

            <dd class="mt-1 text-sm font-semibold text-slate-800">
                {{ \App\Models\Pacientes::ESCOLARIDADES[
                    $pacientes->escolaridad
                ] ?? 'No registrada' }}
            </dd>
        </div>

        <div>
            <dt class="text-xs font-medium text-slate-400">
                Tipo de sangre
            </dt>

            <dd class="mt-1 text-sm font-semibold text-slate-800">
                {{ \App\Models\Pacientes::TIPOS_SANGRE[
                    $pacientes->tipo_sangre
                ] ?? 'No registrado' }}
            </dd>
        </div>

        <div class="sm:col-span-2 lg:col-span-1 xl:col-span-2">
            <dt class="text-xs font-medium text-slate-400">
                Alergias
            </dt>

            <dd class="mt-1 whitespace-pre-line text-sm font-semibold text-slate-800">{{ $pacientes->alergias ?: 'No registradas' }}</dd>
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
    </div>
</details>