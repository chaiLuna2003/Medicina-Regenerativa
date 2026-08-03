@php
    $roles = [
        'admin' => 'Administrador',
        'medico' => 'Médico',
        'enfermero' => 'Enfermero',
        'recepcionista' => 'Recepcionista',
    ];
@endphp

<div class="space-y-6">
    <div>
        <label for="name"
               class="mb-1.5 block text-sm font-medium text-slate-700">
            Nombre completo
        </label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $usuario->name ?? '') }}"
            required
            autofocus
            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >

        @error('name')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label for="email"
               class="mb-1.5 block text-sm font-medium text-slate-700">
            Correo electrónico
        </label>

        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email', $usuario->email ?? '') }}"
            required
            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >

        @error('email')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label for="role"
               class="mb-1.5 block text-sm font-medium text-slate-700">
            Rol
        </label>

        <select
            id="role"
            name="role"
            required
            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
            <option value="">Selecciona un rol</option>

            @foreach ($roles as $value => $label)
                <option
                    value="{{ $value }}"
                    @selected(old('role', $usuario->role ?? '') === $value)
                >
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('role')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="password"
                   class="mb-1.5 block text-sm font-medium text-slate-700">
                Contraseña

                @isset($usuario)
                    <span class="font-normal text-slate-400">
                        (opcional)
                    </span>
                @endisset
            </label>

            <input
                id="password"
                name="password"
                type="password"
                {{ isset($usuario) ? '' : 'required' }}
                autocomplete="new-password"
                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >

            @error('password')
                <p class="mt-1.5 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation"
                   class="mb-1.5 block text-sm font-medium text-slate-700">
                Confirmar contraseña
            </label>

            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                {{ isset($usuario) ? '' : 'required' }}
                autocomplete="new-password"
                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
        </div>
    </div>

    @isset($usuario)
        <p class="-mt-3 text-xs text-slate-500">
            Déjala vacía para conservar la contraseña actual.
        </p>
    @endisset

    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <input type="hidden" name="status" value="0">

        <label class="flex cursor-pointer items-start gap-3">
            <input
                type="checkbox"
                name="status"
                value="1"
                @checked(old('status', $usuario->status ?? true))
                class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
            >

            <span>
                <span class="block text-sm font-medium text-slate-800">
                    Cuenta activa
                </span>

                <span class="mt-0.5 block text-xs text-slate-500">
                    Si se desactiva, el usuario no podrá iniciar sesión.
                </span>
            </span>
        </label>

        @error('status')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>
</div>