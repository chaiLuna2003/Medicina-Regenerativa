{{-- ================================================= --}}
{{-- MENSAJES DEL SISTEMA --}}
{{-- ================================================= --}}

@if (session('success'))
    <div
        id="alert-success"
        class="mx-auto mt-5 mb-5 flex max-w-7xl
               items-start justify-between gap-4
               rounded-xl border border-emerald-200
               bg-emerald-50 px-5 py-4
               text-emerald-800 shadow-sm
               sm:px-6 lg:px-8">

        <div class="flex items-start gap-3">

            <div
                class="mt-0.5 flex h-7 w-7
                       shrink-0 items-center justify-center
                       rounded-full bg-emerald-100">

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
                        d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <div>
                <p class="text-sm font-semibold">
                    Cambios guardados
                </p>

                <p class="mt-0.5 text-sm text-emerald-700">
                    {{ session('success') }}
                </p>
            </div>
        </div>

        <button
            type="button"
            onclick="
                document
                    .getElementById('alert-success')
                    ?.remove()
            "
            class="rounded-lg p-1
                   text-emerald-500 transition
                   hover:bg-emerald-100
                   hover:text-emerald-700"
            aria-label="Cerrar mensaje">

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
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif