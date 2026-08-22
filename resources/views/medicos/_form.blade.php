{{-- Datos personales --}}
<h3
    class="mb-3 text-sm font-semibold uppercase
           tracking-wide text-gray-500"
>
    Datos personales
</h3>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
    @if (isset($medicos))
        {{-- Edición: información obtenida de users --}}
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
        {{-- Creación: selección de cuenta --}}
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
                <option
                    value=""
                    data-nombre=""
                    data-correo=""
                >
                    Selecciona una cuenta con rol médico
                </option>

                @foreach ($usuariosMedicos as $usuario)
                    <option
                        value="{{ $usuario->id }}"
                        data-nombre="{{ $usuario->name }}"
                        data-correo="{{ $usuario->email }}"
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

        {{-- Datos automáticos de la cuenta seleccionada --}}
        <div
            id="datos-cuenta-medica"
            class="hidden sm:col-span-2"
        >
            <div
                class="grid grid-cols-1 gap-4 rounded-xl
                       border border-blue-100 bg-blue-50
                       p-4 sm:grid-cols-2"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                               tracking-wide text-blue-600"
                    >
                        Nombre completo
                    </p>

                    <p
                        id="nombre-cuenta-medica"
                        class="mt-1 font-semibold text-gray-900"
                    ></p>
                </div>

                <div>
                    <p
                        class="text-xs font-semibold uppercase
                               tracking-wide text-blue-600"
                    >
                        Correo de acceso
                    </p>

                    <p
                        id="correo-cuenta-medica"
                        class="mt-1 break-all font-semibold
                               text-gray-900"
                    ></p>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Datos profesionales --}}
<h3
    class="mb-3 text-sm font-semibold uppercase
           tracking-wide text-gray-500"
>
    Datos profesionales
</h3>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
    {{-- Especialidad --}}
    <div>
        <label
            for="especialidad"
            class="mb-1 block text-sm font-medium
                   text-gray-700"
        >
            Especialidad
        </label>

        <input
            id="especialidad"
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

    {{-- Cédula --}}
    <div>
        <label
            for="cedula"
            class="mb-1 block text-sm font-medium
                   text-gray-700"
        >
            Cédula profesional
        </label>

        <input
            id="cedula"
            type="text"
            name="cedula"
            value="{{ old(
                'cedula',
                $medicos->cedula ?? ''
            ) }}"
            required
            inputmode="numeric"
            autocomplete="off"
            class="w-full rounded-lg border-gray-300
                   shadow-sm focus:border-emerald-500
                   focus:ring-emerald-500"
        >

        @error('cedula')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Universidad --}}
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

    {{-- Consultorio --}}
    <div>
        <label
            for="consultorio"
            class="mb-1 block text-sm font-medium
                   text-gray-700"
        >
            Consultorio
        </label>

        <input
            id="consultorio"
            type="text"
            name="consultorio"
            value="{{ old(
                'consultorio',
                $medicos->consultorio ?? ''
            ) }}"
            required
            class="w-full rounded-lg border-gray-300
                   shadow-sm focus:border-emerald-500
                   focus:ring-emerald-500"
        >

        @error('consultorio')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Dirección --}}
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
            class="w-full resize-y rounded-lg
                   border-gray-300 shadow-sm
                   focus:border-emerald-500
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

{{-- Contacto --}}
<h3
    class="mb-3 text-sm font-semibold uppercase
           tracking-wide text-gray-500"
>
    Contacto
</h3>

<div class="mb-6 grid grid-cols-1 gap-4">
    <div>
        <label
            for="telefono"
            class="mb-1 block text-sm font-medium
                   text-gray-700"
        >
            Teléfono profesional
        </label>

        <input
            id="telefono"
            type="text"
            name="telefono"
            value="{{ old(
                'telefono',
                $medicos->telefono ?? ''
            ) }}"
            required
            inputmode="tel"
            autocomplete="tel"
            class="w-full rounded-lg border-gray-300
                   shadow-sm focus:border-emerald-500
                   focus:ring-emerald-500"
        >

        @error('telefono')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>
</div>

{{-- Estado --}}
<div
    class="mb-6 flex items-center gap-3 rounded-lg
           bg-gray-50 px-4 py-3"
>
    <input
        type="hidden"
        name="status"
        value="0"
    >

    <input
        id="status"
        type="checkbox"
        name="status"
        value="1"
        @checked(
            old(
                'status',
                $medicos->status ?? true
            )
        )
        class="rounded border-gray-300
               text-emerald-600
               focus:ring-emerald-500"
    >

    <label
        for="status"
        class="text-sm font-medium text-gray-700"
    >
        Médico activo
    </label>
</div>

{{-- Vista previa de la cuenta durante la creación --}}
@if (! isset($medicos))
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            () => {
                const selector =
                    document.getElementById('user_id');

                const contenedor =
                    document.getElementById(
                        'datos-cuenta-medica'
                    );

                const nombre =
                    document.getElementById(
                        'nombre-cuenta-medica'
                    );

                const correo =
                    document.getElementById(
                        'correo-cuenta-medica'
                    );

                if (
                    !selector
                    || !contenedor
                    || !nombre
                    || !correo
                ) {
                    return;
                }

                function actualizarCuenta() {
                    const opcion =
                        selector.options[
                            selector.selectedIndex
                        ];

                    if (!opcion || !opcion.value) {
                        nombre.textContent = '';
                        correo.textContent = '';

                        contenedor.classList.add(
                            'hidden'
                        );

                        return;
                    }

                    nombre.textContent =
                        opcion.dataset.nombre ?? '';

                    correo.textContent =
                        opcion.dataset.correo ?? '';

                    contenedor.classList.remove(
                        'hidden'
                    );
                }

                selector.addEventListener(
                    'change',
                    actualizarCuenta
                );

                actualizarCuenta();
            }
        );
    </script>
@endif