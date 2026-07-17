<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Médicos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-emerald-100 text-emerald-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('medicos.create') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    + Nuevo médico
                </a>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Nombre</th>
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
                                <td class="px-4 py-3">{{ $medico->nombre }} {{ $medico->apellido_paterno }}</td>
                                <td class="px-4 py-3">{{ $medico->especialidad }}</td>
                                <td class="px-4 py-3">{{ $medico->cedula }}</td>
                                <td class="px-4 py-3">{{ $medico->consultorio }}</td>
                                <td class="px-4 py-3">
                                    @if ($medico->status)
                                        <span class="text-emerald-600 text-xs font-medium">● Activo</span>
                                    @else
                                        <span class="text-red-600 text-xs font-medium">● Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
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

            <div class="mt-4">
                {{ $medicos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>