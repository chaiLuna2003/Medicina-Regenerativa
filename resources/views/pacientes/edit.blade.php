@extends('layouts.app')

@section('title', 'Editar paciente')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar paciente</h1>

    <form action="{{ route('pacientes.update', $paciente) }}" method="POST"
          class="bg-white p-6 rounded-lg shadow max-w-lg">
        @csrf
        @method('PUT')
        @include('pacientes._form')

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Actualizar
            </button>
            <a href="{{ route('pacientes.index') }}"
               class="text-gray-600 hover:underline px-4 py-2 text-sm">
                Cancelar
            </a>
        </div>
    </form>
@endsection