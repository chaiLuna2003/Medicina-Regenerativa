<h3
    class="mb-3 text-sm font-semibold uppercase
           tracking-wide text-gray-500"
>
    Datos personales
</h3>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
    @if (isset($medicos))
        {{-- Edición: conservar la cuenta vinculada --}}
        <input
            type="hidden"
            name="user_id"
            value="{{ $medicos->user_id }}"
        >

        {{--
            Campos heredados temporalmente.
            El controlador todavía los valida.
        --}}
        <input
            type="hidden"
            name="nombre"
            value="{{ $medicos->nombre }}"
        >

        <input
            type="hidden"
            name="apellido_paterno"
            value="{{ $medicos->apellido_paterno }}"
        >

        <input
            type="hidden"
            name="apellido_materno"
            value="{{ $medicos->apellido_materno }}"
        >

        <div>
            <label
                class="mb-1 block text-sm font-medium
                       text-gray-700"
            >
                Nombre completo
            </label>

            <input
                type="text"
                value="{{ $medicos->user?->name
                    ?? 'Nombre no disponible' }}"
                readonly
                class="w-full cursor-not-allowed rounded-lg
                       border-gray-300 bg-gray-100
                       text-gray-700 shadow-sm"
            >
        </div>

        <div>
            <label
                class="mb-1 block text-sm font-medium
                       text-gray-700"
            >
                Correo de acceso
            </label>

            <input
                type="email"
                value="{{ $medicos->user?->email
                    ?? 'Correo no disponible' }}"
                readonly
                class="w-full cursor-not-allowed rounded-lg
                       border-gray-300 bg-gray-100
                       text-gray-700 shadow-sm"
            >
        </div>
    @else
        {{-- Creación: seleccionar una cuenta médica --}}
        <div class="sm:col-span-2">
            <label
                for="user_id"
                class="mb-2 block text-sm font-semibold
                       text-gray-700"
            >
                Cuenta de acceso
            </label>

            <select
                id="user_id"
                name="user_id"
                required
                class="block w-full rounded-xl
                       border-gray-300 shadow-sm
                       focus:border-[#0D3B7F]
                       focus:ring-[#0D3B7F]"
            >
                <option value="">
                    Selecciona una cuenta con rol médico
                </option>

                @foreach ($usuariosMedicos as $usuario)
                    <option
                        value="{{ $usuario->id }}"
                        @selected(
                            old('user_id') == $usuario->id
                        )
                    >
                        {{ $usuario->name }}
                        —
                        {{ $usuario->email }}
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
                    No hay cuentas médicas disponibles.
                    Primero crea un usuario con rol médico.
                </p>
            @endif
        </div>

        {{-- Se conservan mientras ajustamos store() --}}
        <div>
            <label
                class="mb-1 block text-sm font-medium
                       text-gray-700"
            >
                Nombre
            </label>

            <input
                type="text"
                name="nombre"
                value="{{ old('nombre') }}"
                required
                class="w-full rounded-lg border-gray-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
            >

            @error('nombre')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                class="mb-1 block text-sm font-medium
                       text-gray-700"
            >
                Apellido paterno
            </label>

            <input
                type="text"
                name="apellido_paterno"
                value="{{ old('apellido_paterno') }}"
                required
                class="w-full rounded-lg border-gray-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
            >
        </div>

        <div>
            <label
                class="mb-1 block text-sm font-medium
                       text-gray-700"
            >
                Apellido materno
            </label>

            <input
                type="text"
                name="apellido_materno"
                value="{{ old('apellido_materno') }}"
                required
                class="w-full rounded-lg border-gray-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
            >
        </div>
    @endif

    <div class="sm:col-span-2">
        <label
            class="mb-1 block text-sm font-medium
                   text-gray-700"
        >
            Especialidad
        </label>

        <input
            type="text"
            name="especialidad"
            value="{{ old(
                'especialidad',
                $medicos->especialidad ?? ''
            ) }}"
            required
            class="w-full rounded-lg border-gray-300
                   shadow-sm focus:border-emerald-500
                   focus:ring-emerald-500"
        >

        @error('especialidad')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
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
    <label
        for="universidad_id"
        class="mb-1 block text-sm font-medium
               text-gray-700"
    >
        Universidad de procedencia
    </label>

    <select
        id="universidad_id"
        name="universidad_id"
        required
        class="w-full rounded-lg border-gray-300
               shadow-sm focus:border-emerald-500
               focus:ring-emerald-500"
    >
        <option value="">
            Selecciona una universidad
        </option>

        @foreach ($universidades as $universidad)
            <option
                value="{{ $universidad->id }}"
                @selected(
                    old(
                        'universidad_id',
                        $medicos->universidad_id ?? ''
                    ) == $universidad->id
                )
            >
                {{ $universidad->nombre }}

                @if ($universidad->abreviatura)
                    ({{ $universidad->abreviatura }})
                @endif
            </option>
        @endforeach
    </select>

    @error('universidad_id')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror

    @if ($universidades->isEmpty())
        <p class="mt-2 text-sm text-amber-600">
            No hay universidades activas registradas.
        </p>
    @endif
</div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Consultorio</label>
        <input type="text" name="consultorio" value="{{ old('consultorio', $medicos->consultorio ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('consultorio') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
    <label
        for="direccion"
        class="mb-1 block text-sm font-medium
               text-gray-700"
    >
        Dirección profesional
    </label>

    <textarea
        id="direccion"
        name="direccion"
        rows="3"
        class="w-full rounded-lg border-gray-300
               shadow-sm focus:border-emerald-500
               focus:ring-emerald-500"
        placeholder="Calle, número, colonia, municipio y estado"
    >{{ old(
        'direccion',
        $medicos->direccion ?? ''
    ) }}</textarea>

    @error('direccion')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
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
