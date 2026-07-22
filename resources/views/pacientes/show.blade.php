<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pacientes.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $pacientes->nombre }} {{ $pacientes->apellido }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white rounded-xl shadow-sm p-6 sm:p-8">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
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
                    <div class="ml-auto flex gap-2">
                        <a href="{{ route('pacientes.edit', $pacientes) }}"
                           class="text-amber-600 hover:bg-amber-50 rounded-lg px-3 py-1.5 text-sm font-medium">
                            Editar
                        </a>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-gray-400">Fecha de nacimiento</dt>
                        <dd class="text-gray-800 font-medium">{{ $pacientes->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Edad</dt>
                        <dd class="text-gray-800 font-medium">{{ $pacientes->edad ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Teléfono</dt>
                        <dd class="text-gray-800 font-medium">{{ $pacientes->telefono ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Email</dt>
                        <dd class="text-gray-800 font-medium">{{ $pacientes->email ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-400 mb-1">Notas</dt>
                        <dd class="text-gray-800 whitespace-pre-line">{{ $pacientes->notas ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
