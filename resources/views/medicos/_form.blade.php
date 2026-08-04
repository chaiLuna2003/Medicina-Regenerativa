<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Datos personales</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

<div>
    <label
        for="user_id"
        class="mb-2 block text-sm font-semibold text-gray-700"
    >
        Cuenta de acceso
    </label>

    <select
        id="user_id"
        name="user_id"
        required
        class="block w-full rounded-xl border-gray-300 shadow-sm
               focus:border-[#0D3B7F] focus:ring-[#0D3B7F]"
    >
        <option value="">Selecciona una cuenta con rol médico</option>

        @foreach ($usuariosMedicos as $usuario)
            <option
                value="{{ $usuario->id }}"
                @selected(old('user_id') == $usuario->id)
            >
                {{ $usuario->name }} — {{ $usuario->email }}
            </option>
        @endforeach
    </select>

    @error('user_id')
        <p class="mt-2 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror

    @if ($usuariosMedicos->isEmpty())
        <p class="mt-2 text-sm text-amber-600">
            No hay cuentas médicas disponibles. Primero crea un usuario
            con rol médico desde Usuarios y roles.
        </p>
    @endif
</div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $medicos->nombre ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('nombre') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
        <input type="text" name="especialidad" value="{{ old('especialidad', $medicos->especialidad ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('especialidad') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Apellido paterno</label>
        <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno', $medicos->apellido_paterno ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('apellido_paterno') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Apellido materno</label>
        <input type="text" name="apellido_materno" value="{{ old('apellido_materno', $medicos->apellido_materno ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('apellido_materno') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Datos profesionales</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Cédula profesional</label>
        <input type="text" name="cedula" value="{{ old('cedula', $medicos->cedula ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('cedula') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Consultorio</label>
        <input type="text" name="consultorio" value="{{ old('consultorio', $medicos->consultorio ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('consultorio') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Contacto</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $medicos->telefono ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('telefono') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
        <input type="email" name="correo" value="{{ old('correo', $medicos->correo ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('correo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mb-6 flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-3">
    <input type="hidden" name="status" value="0">
    <input type="checkbox" id="status" name="status" value="1"
           {{ old('status', $medicos->status ?? true) ? 'checked' : '' }}
           class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
    <label for="status" class="text-sm font-medium text-gray-700">Médico activo</label>
</div>
