<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $pacientes->nombre }} {{ $pacientes->apellido }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow space-y-3">

                <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100">
                    <img src="{{ $pacientes->fotoUrl() }}"
                         alt="Foto de {{ $pacientes->nombre }}"
                         class="w-20 h-20 rounded-full object-cover border border-gray-200">
                    <div>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $pacientes->nombre }} {{ $pacientes->apellido }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $pacientes->edad ? $pacientes->edad . ' años' : 'Edad no registrada' }}
                        </p>
                    </div>
                </div>

                <p><span class="font-medium text-gray-600">Fecha de nacimiento:</span>
                    {{ $pacientes->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</p>
                <p><span class="font-medium text-gray-600">Edad:</span> {{ $pacientes->edad ?? '—' }}</p>
                <p><span class="font-medium text-gray-600">Teléfono:</span> {{ $pacientes->telefono ?? '—' }}</p>
                <p><span class="font-medium text-gray-600">Email:</span> {{ $pacientes->email ?? '—' }}</p>
                <p><span class="font-medium text-gray-600">Notas:</span><br>{{ $pacientes->notas ?? '—' }}</p>
            </div>

            <a href="{{ route('pacientes.index') }}" class="inline-block mt-4 text-emerald-700 hover:underline">
                ← Volver al listado
            </a>
        </div>
    </div>
</x-app-layout>