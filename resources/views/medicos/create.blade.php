<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('medicos.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo médico</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('medicos.store') }}" method="POST"
                  class="bg-white p-6 sm:p-8 rounded-xl shadow-sm">
                @csrf
                @include('medicos._form')

                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium shadow-sm">
                        Guardar médico
                    </button>
                    <a href="{{ route('medicos.index') }}" class="text-gray-600 hover:bg-gray-100 rounded-lg px-5 py-2.5 text-sm font-medium">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
