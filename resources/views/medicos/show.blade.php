<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('medicos.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dr(a). {{ $medicos->nombre }} {{ $medicos->apellido_paterno }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6 sm:p-8">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-semibold shrink-0">
                        {{ strtoupper(substr($medicos->nombre, 0, 1) . substr($medicos->apellido_paterno, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-800">
                            Dr(a). {{ $medicos->nombre }} {{ $medicos->apellido_paterno }} {{ $medicos->apellido_materno }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $medicos->especialidad }}</p>
                    </div>
                    <div class="ml-auto flex flex-col items-end gap-2">
                        @if ($medicos->status)
                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-medium px-2 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 text-xs font-medium px-2 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactivo
                            </span>
                        @endif
                        <a href="{{ route('medicos.edit', $medicos) }}"
                           class="text-amber-600 hover:bg-amber-50 rounded-lg px-3 py-1 text-sm font-medium">
                            Editar
                        </a>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-gray-400">Cédula profesional</dt>
                        <dd class="text-gray-800 font-medium">{{ $medicos->cedula }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Consultorio</dt>
                        <dd class="text-gray-800 font-medium">{{ $medicos->consultorio }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Teléfono</dt>
                        <dd class="text-gray-800 font-medium">{{ $medicos->telefono }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Correo</dt>
                        <dd class="text-gray-800 font-medium">{{ $medicos->correo }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
