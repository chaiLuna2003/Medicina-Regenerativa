<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $paciente->nombre ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('nombre') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
    <input type="text" name="apellido" value="{{ old('apellido', $paciente->apellido ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('apellido') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de nacimiento</label>
    <input type="date" name="fecha_nacimiento"
           value="{{ old('fecha_nacimiento', isset($paciente) ? $paciente->fecha_nacimiento->format('Y-m-d') : '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('fecha_nacimiento') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
    <input type="text" name="telefono" value="{{ old('telefono', $paciente->telefono ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('telefono') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
    <input type="email" name="email" value="{{ old('email', $paciente->email ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
    <textarea name="notas" rows="4"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">{{ old('notas', $paciente->notas ?? '') }}</textarea>
    @error('notas') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>