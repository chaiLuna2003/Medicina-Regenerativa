<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col gap-4
                   sm:flex-row sm:items-center
                   sm:justify-between"
        >
            <div>
                <h2
                    class="text-xl font-semibold
                           text-gray-900"
                >
                    Registrar nueva cita
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Selecciona al paciente, médico,
                    fecha y horario.
                </p>
            </div>

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center
                       justify-center rounded-lg border
                       border-gray-300 bg-white px-4 py-2
                       text-sm font-semibold text-gray-700
                       transition hover:bg-gray-50"
            >
                Regresar al dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div
            class="mx-auto max-w-4xl
                   px-4 sm:px-6 lg:px-8"
        >
            @include('citas._form', [
                'medicos' => $medicos,

                'datosPrecargados' =>
                    $datosPrecargados ?? [],
            ])
        </div>
    </div>
</x-app-layout>