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
                    Notas
                </h3>

                <p class="mt-0.5 text-xs text-slate-400">
                    Observaciones generales
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
                        onclick="abrirModalNotas()"
                        class="rounded-lg px-2.5 py-1.5 text-xs
                               font-semibold text-blue-600 transition
                               hover:bg-blue-50 hover:text-blue-800">
                        Editar
                    </button>
                </div>
            @endif

            <div
                class="whitespace-pre-line p-5
                       text-sm leading-6 text-slate-700">

            {{ $pacientes->notas
                ?? 'Sin notas registradas.' }}
            </div>
        </div>
    </details>

@endunless