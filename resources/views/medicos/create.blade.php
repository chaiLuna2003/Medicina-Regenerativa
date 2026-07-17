<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo médico</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('medicos.store') }}" method="POST"
                  class="bg-white p-6 rounded-lg shadow">
                @csrf
                @include('medicos._form')

                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Guardar
                    </button>
                    <a href="{{ route('medicos.index') }}" class="text-gray-600 hover:underline px-4 py-2 text-sm">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>