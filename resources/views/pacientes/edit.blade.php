<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pacientes.index') }}"
               class="text-gray-400 transition hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ auth()->user()->isRecepcionista()
                    ? 'Actualizar datos del paciente'
                    : 'Editar paciente' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <form
                action="{{ route('pacientes.update', $pacientes) }}"
                method="POST"
                enctype="multipart/form-data"
                class="rounded-xl bg-white p-6 shadow-sm sm:p-8"
            >
                @csrf
                @method('PUT')

                @include('pacientes._form')

                <div class="flex flex-wrap gap-3">
                    <button
                        type="submit"
                        class="rounded-lg bg-emerald-600 px-5 py-2.5
                               text-sm font-medium text-white shadow-sm
                               transition hover:bg-emerald-700"
                    >
                        {{ auth()->user()->isRecepcionista()
                            ? 'Guardar cambios'
                            : 'Actualizar paciente' }}
                    </button>

                    <a
                        href="{{ route('pacientes.index') }}"
                        class="rounded-lg px-5 py-2.5 text-sm font-medium
                               text-gray-600 transition hover:bg-gray-100"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>