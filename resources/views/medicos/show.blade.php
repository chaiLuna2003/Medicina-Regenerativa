<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dr(a). {{ $medicos->nombre }} {{ $medicos->apellido_paterno }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow space-y-3">
                <p><span class="font-medium text-gray-600">Nombre completo:</span>
                    {{ $medicos->nombre }} {{ $medicos->apellido_paterno }} {{ $medicos->apellido_materno }}</p>
                <p><span class="font-medium text-gray-600">Especialidad:</span> {{ $medicos->especialidad }}</p>
                <p><span class="font-medium text-gray-600">Cédula:</span> {{ $medicos->cedula }}</p>
                <p><span class="font-medium text-gray-600">Teléfono:</span> {{ $medicos->telefono }}</p>
                <p><span class="font-medium text-gray-600">Correo:</span> {{ $medicos->correo }}</p>
                <p><span class="font-medium text-gray-600">Consultorio:</span> {{ $medicos->consultorio }}</p>
                <p>
                    <span class="font-medium text-gray-600">Estatus:</span>
                    @if ($medicos->status)
                        <span class="text-emerald-600 font-medium">Activo</span>
                    @else
                        <span class="text-red-600 font-medium">Inactivo</span>
                    @endif
                </p>
            </div>

            <a href="{{ route('medicos.index') }}" class="inline-block mt-4 text-emerald-700 hover:underline">
                ← Volver al listado
            </a>
        </div>
    </div>
</x-app-layout>