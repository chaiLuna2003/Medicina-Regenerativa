<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pacientes
        </h2>
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

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <a href="{{ route('pacientes.create') }}"
                   class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo paciente
                </a>

                <form method="GET" action="{{ route('pacientes.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Buscar por nombre o apellido..."
                           class="w-full sm:w-72 border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    <button type="submit"
                            class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium shrink-0">
                        Buscar
                    </button>
                    @if (request('search'))
                        <a href="{{ route('pacientes.index') }}"
                           class="text-gray-500 hover:underline px-2 py-2 text-sm shrink-0">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            {{-- Tabla: solo visible en pantallas medianas en adelante --}}
            <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
                        <tr>
                            <th class="px-4 py-3">Paciente</th>
                            <th class="px-4 py-3">Edad</th>
                            <th class="px-4 py-3">Teléfono</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($pacientes as $paciente)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $paciente->fotoUrl() }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                                        <span class="font-medium text-gray-800">{{ $paciente->nombre }} {{ $paciente->apellido }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $paciente->edad ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $paciente->telefono ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $paciente->email ?? '—' }}</td>
                                <td class="px-4 py-3 text-right space-x-3">
                                    <a href="{{ route('pacientes.show', $paciente) }}" class="text-blue-600 hover:underline">Ver</a>
                                    <a href="{{ route('pacientes.edit', $paciente) }}" class="text-amber-600 hover:underline">Editar</a>
                                    @if (auth()->user()->isAdmin())
                                        <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" class="inline"
                                              onsubmit="return confirm('¿Eliminar este paciente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">No hay pacientes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tarjetas: solo visibles en móvil --}}
            <div class="md:hidden space-y-3">
                @forelse ($pacientes as $paciente)
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <img src="{{ $paciente->fotoUrl() }}" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                            <div class="min-w-0">
                                <p class="font-medium text-gray-800 truncate">{{ $paciente->nombre }} {{ $paciente->apellido }}</p>
                                <p class="text-xs text-gray-400">{{ $paciente->edad ? $paciente->edad . ' años' : 'Edad no registrada' }}</p>
                            </div>
                        </div>
                        <dl class="text-sm text-gray-600 space-y-1 mb-3">
                            <div class="flex justify-between"><dt class="text-gray-400">Teléfono</dt><dd>{{ $paciente->telefono ?? '—' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-400">Email</dt><dd class="truncate ml-2">{{ $paciente->email ?? '—' }}</dd></div>
                        </dl>
                        <div class="flex items-center gap-4 text-sm pt-3 border-t border-gray-100">
                            <a href="{{ route('pacientes.show', $paciente) }}" class="text-blue-600">Ver</a>
                            <a href="{{ route('pacientes.edit', $paciente) }}" class="text-amber-600">Editar</a>
                            @if (auth()->user()->isAdmin())
                                <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar este paciente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm p-6 text-center text-gray-400">
                        No hay pacientes registrados.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $pacientes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
