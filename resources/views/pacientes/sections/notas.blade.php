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
                    Notas
                </h3>

                <p class="mt-0.5 text-xs text-slate-400">
                    Observaciones generales
                </p>
            </div>

            @if (request()->user()->isAdmin())

                <button
                    type="button"
                    onclick="abrirModalNotas()"
                    class="text-xs font-semibold
                           text-blue-600
                           hover:text-blue-800">
                    Editar
                </button>

            @endif
        </div>

        <div
            class="whitespace-pre-line p-5
                   text-sm leading-6 text-slate-700">

            {{ $pacientes->notas
                ?? 'Sin notas registradas.' }}
        </div>
    </section>

@endunless