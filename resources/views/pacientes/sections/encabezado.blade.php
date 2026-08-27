<div class="flex items-center justify-between gap-4">

    <div class="flex items-center gap-3">

        <a
            href="{{ request()->user()->isMedico()
                ? route('citas.index')
                : route('pacientes.index') }}"
            class="text-slate-400 transition
                   hover:text-slate-700">

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
                    d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        <div>
            <h2 class="text-xl font-semibold text-slate-900">
                {{ $pacientes->nombre }}
                {{ $pacientes->apellido }}
            </h2>

            <p class="text-sm text-slate-500">
                Ficha del paciente
            </p>

            @if ($ultimaActividad)

                <div
                    class="mt-2 flex flex-wrap
                           items-center gap-2">

                    <span
                        class="inline-flex h-2 w-2
                               rounded-full bg-blue-500">
                    </span>

                    <p class="text-xs text-slate-500">
                        Última actividad:

                        <span class="font-semibold text-slate-700">
                            {{ $ultimaActividad['titulo'] }}
                        </span>

                        <span class="mx-1 text-slate-300">
                            ·
                        </span>

                        {{ $ultimaActividad['fecha']->format('d/m/Y') }}

                        <span class="text-slate-400">
                            {{ $ultimaActividad['fecha']->format('h:i A') }}
                        </span>
                    </p>
                </div>

            @else

                <p class="mt-2 text-xs text-slate-400">
                    Sin actividad clínica registrada
                </p>

            @endif
        </div>
    </div>

    <span
        class="rounded-full px-3 py-1
               text-xs font-semibold
               {{ $pacientes->status
                    ? 'bg-emerald-50 text-emerald-700'
                    : 'bg-red-50 text-red-700' }}">

        {{ $pacientes->status
            ? 'Activo'
            : 'Inactivo' }}
    </span>
</div>