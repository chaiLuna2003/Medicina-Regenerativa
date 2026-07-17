<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $medicos->nombre ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('nombre') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido paterno</label>
    <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno', $medicos->apellido_paterno ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('apellido_paterno') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido materno</label>
    <input type="text" name="apellido_materno" value="{{ old('apellido_materno', $medicos->apellido_materno ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('apellido_materno') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
    <input type="text" name="especialidad" value="{{ old('especialidad', $medicos->especialidad ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('especialidad') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Cédula profesional</label>
    <input type="text" name="cedula" value="{{ old('cedula', $medicos->cedula ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('cedula') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
    <input type="text" name="telefono" value="{{ old('telefono', $medicos->telefono ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('telefono') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
    <input type="email" name="correo" value="{{ old('correo', $medicos->correo ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('correo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Consultorio</label>
    <input type="text" name="consultorio" value="{{ old('consultorio', $medicos->consultorio ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('consultorio') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-6 flex items-center gap-2">
    <input type="hidden" name="status" value="0">
    <input type="checkbox" id="status" name="status" value="1"
           {{ old('status', $medicos->status ?? true) ? 'checked' : '' }}
           class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
    <label for="status" class="text-sm font-medium text-gray-700">Médico activo</label>
</div>