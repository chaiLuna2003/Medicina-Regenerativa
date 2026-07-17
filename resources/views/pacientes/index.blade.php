<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pacientes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-emerald-100 text-emerald-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('pacientes.create') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    + Nuevo paciente
                </a>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Apellido</th>
                            <th class="px-4 py-3">Teléfono</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($pacientes as $paciente)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $paciente->nombre }}</td>
                                <td class="px-4 py-3">{{ $paciente->apellido }}</td>
                                <td class="px-4 py-3">{{ $paciente->telefono ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $paciente->email ?? '—' }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('pacientes.show', $paciente) }}" class="text-blue-600 hover:underline">Ver</a>
                                    <a href="{{ route('pacientes.edit', $paciente) }}" class="text-amber-600 hover:underline">Editar</a>
                                    <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Eliminar este paciente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
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

            <div class="mt-4">
                {{ $pacientes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>