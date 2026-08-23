<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('pacientes.index') }}"
                class="text-slate-400 transition hover:text-slate-700"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </a>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Nuevo paciente
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Registra la información básica del paciente
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <form
                action="{{ route('pacientes.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="overflow-hidden rounded-2xl
                       border border-slate-200
                       bg-white shadow-sm"
            >
                @csrf

                <div class="p-6 sm:p-8">
                    @include('pacientes._form')
                </div>

                <div
                    class="flex flex-col-reverse gap-3
                           border-t border-slate-100
                           bg-slate-50 px-6 py-4
                           sm:flex-row sm:justify-end"
                >
                    <a
                        href="{{ route('pacientes.index') }}"
                        class="inline-flex items-center
                               justify-center rounded-xl
                               border border-slate-300
                               bg-white px-5 py-2.5
                               text-sm font-semibold
                               text-slate-700 transition
                               hover:bg-slate-100"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center
                               justify-center gap-2
                               rounded-xl bg-blue-600
                               px-5 py-2.5
                               text-sm font-semibold
                               text-white shadow-sm
                               transition
                               hover:bg-blue-700
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500
                               focus:ring-offset-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>

                        Crear paciente
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>