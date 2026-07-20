<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Edad</label>
    <input type="number" name="edad" min="0" max="120"
           value="{{ old('edad', $pacientes->edad ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('edad') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">Foto de perfil</label>

    <div class="flex items-center gap-4">
        <img id="preview-foto"
             src="{{ isset($pacientes) ? $pacientes->fotoUrl() : asset('images/avatar-default.png') }}"
             class="w-16 h-16 rounded-full object-cover border border-gray-200">

        <input type="file" name="foto" accept="image/*" capture="environment"
               onchange="document.getElementById('preview-foto').src = URL.createObjectURL(this.files[0])"
               class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                      file:bg-emerald-50 file:text-emerald-700 file:text-sm file:font-medium
                      hover:file:bg-emerald-100">
    </div>
    @error('foto') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $pacientes->nombre ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('nombre') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
    <input type="text" name="apellido" value="{{ old('apellido', $pacientes->apellido ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('apellido') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de nacimiento</label>
    <input type="date" name="fecha_nacimiento"
           value="{{ old('fecha_nacimiento', isset($pacientes) ? $pacientes->fecha_nacimiento?->format('Y-m-d') : '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('fecha_nacimiento') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
    <input type="text" name="telefono" value="{{ old('telefono', $pacientes->telefono ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('telefono') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
    <input type="email" name="email" value="{{ old('email', $pacientes->email ?? '') }}"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
    @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
    <textarea name="notas" rows="4"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">{{ old('notas', $pacientes->notas ?? '') }}</textarea>
    @error('notas') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>