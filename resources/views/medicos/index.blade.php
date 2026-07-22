<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Médicos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-emerald-100 text-emerald-800 px-4 py-3 rounded-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('medicos.create') }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo médico
                </a>
            </div>

            {{-- Tabla: escritorio --}}
            <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
                        <tr>
                            <th class="px-4 py-3">Médico</th>
                            <th class="px-4 py-3">Especialidad</th>
                            <th class="px-4 py-3">Cédula</th>
                            <th class="px-4 py-3">Consultorio</th>
                            <th class="px-4 py-3">Estatus</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($medicos as $medico)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-semibold shrink-0">
                                            {{ strtoupper(substr($medico->nombre, 0, 1) . substr($medico->apellido_paterno, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-800">Dr(a). {{ $medico->nombre }} {{ $medico->apellido_paterno }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $medico->especialidad }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $medico->cedula }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $medico->consultorio }}</td>
                                <td class="px-4 py-3">
                                    @if ($medico->status)
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-medium px-2 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 text-xs font-medium px-2 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-3">
                                    <a href="{{ route('medicos.show', $medico) }}" class="text-blue-600 hover:underline">Ver</a>
                                    <a href="{{ route('medicos.edit', $medico) }}" class="text-amber-600 hover:underline">Editar</a>
                                    <form action="{{ route('medicos.destroy', $medico) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Eliminar este médico?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">No hay médicos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tarjetas: móvil --}}
            <div class="md:hidden space-y-3">
                @forelse ($medicos as $medico)
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-11 h-11 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-semibold shrink-0">
                                {{ strtoupper(substr($medico->nombre, 0, 1) . substr($medico->apellido_paterno, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-800 truncate">Dr(a). {{ $medico->nombre }} {{ $medico->apellido_paterno }}</p>
                                <p class="text-xs text-gray-400">{{ $medico->especialidad }}</p>
                            </div>
                            @if ($medico->status)
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-medium px-2 py-1 rounded-full shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 text-xs font-medium px-2 py-1 rounded-full shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactivo
                                </span>
                            @endif
                        </div>
                        <dl class="text-sm text-gray-600 space-y-1 mb-3">
                            <div class="flex justify-between"><dt class="text-gray-400">Cédula</dt><dd>{{ $medico->cedula }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-400">Consultorio</dt><dd>{{ $medico->consultorio }}</dd></div>
                        </dl>
                        <div class="flex items-center gap-4 text-sm pt-3 border-t border-gray-100">
                            <a href="{{ route('medicos.show', $medico) }}" class="text-blue-600">Ver</a>
                            <a href="{{ route('medicos.edit', $medico) }}" class="text-amber-600">Editar</a>
                            <form action="{{ route('medicos.destroy', $medico) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este médico?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm p-6 text-center text-gray-400">
                        No hay médicos registrados.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $medicos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
