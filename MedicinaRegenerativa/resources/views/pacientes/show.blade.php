@extends('layouts.app')

@section('title', 'Detalle del paciente')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        {{ $pacientes->nombre }} {{ $pacientes->apellido }}
    </h1>

    <div class="bg-white p-6 rounded-lg shadow max-w-lg space-y-3">
        <p><span class="font-medium text-gray-600">Fecha de nacimiento:</span>
    {{ $pacientes->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</p>
        <p><span class="font-medium text-gray-600">Teléfono:</span>
            {{ $pacientes->telefono ?? '—' }}</p>
        <p><span class="font-medium text-gray-600">Email:</span>
            {{ $pacientes->email ?? '—' }}</p>
        <p><span class="font-medium text-gray-600">Notas:</span><br>
            {{ $pacientes->notas ?? '—' }}</p>
    </div>

    <a href="{{ route('pacientes.index') }}" class="inline-block mt-4 text-emerald-700 hover:underline">
        ← Volver al listado
    </a>
@endsection