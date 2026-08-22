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
    {{-- Contraseña --}}
    <div>
        <label
            for="password"
            class="mb-1.5 block text-sm font-medium
                   text-slate-700"
        >
            Contraseña

            @isset($usuario)
                <span class="font-normal text-slate-400">
                    (opcional)
                </span>
            @endisset
        </label>

        <div class="relative">
            <input
                id="password"
                name="password"
                type="password"
                {{ isset($usuario) ? '' : 'required' }}
                autocomplete="new-password"
                class="w-full rounded-lg border-slate-300
                       pr-11 text-sm shadow-sm
                       focus:border-blue-500
                       focus:ring-blue-500"
            >

            <button
                type="button"
                class="alternar-password absolute inset-y-0
                       right-0 flex w-11 items-center
                       justify-center text-slate-400
                       transition hover:text-slate-700"
                data-target="password"
                aria-label="Mostrar contraseña"
                aria-pressed="false"
            >
                {{-- Ojo abierto --}}
                <svg
                    class="icono-mostrar h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"
                    />
                    <circle cx="12" cy="12" r="2.75" />
                </svg>

                {{-- Ojo cerrado --}}
                <svg
                    class="icono-ocultar hidden h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 4.4A10.8 10.8 0 0 1 12 4.2c6 0 9.75 7.8 9.75 7.8a18.7 18.7 0 0 1-2.3 3.25M6.2 6.2C3.7 8 2.25 12 2.25 12S6 19.8 12 19.8a9.8 9.8 0 0 0 4.1-.9"
                    />
                </svg>
            </button>
        </div>

        @error('password')
            <p class="mt-1.5 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Confirmación --}}
    <div>
        <label
            for="password_confirmation"
            class="mb-1.5 block text-sm font-medium
                   text-slate-700"
        >
            Confirmar contraseña
        </label>

        <div class="relative">
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                {{ isset($usuario) ? '' : 'required' }}
                autocomplete="new-password"
                class="w-full rounded-lg border-slate-300
                       pr-11 text-sm shadow-sm
                       focus:border-blue-500
                       focus:ring-blue-500"
            >

            <button
                type="button"
                class="alternar-password absolute inset-y-0
                       right-0 flex w-11 items-center
                       justify-center text-slate-400
                       transition hover:text-slate-700"
                data-target="password_confirmation"
                aria-label="Mostrar confirmación de contraseña"
                aria-pressed="false"
            >
                <svg
                    class="icono-mostrar h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"
                    />
                    <circle cx="12" cy="12" r="2.75" />
                </svg>

                <svg
                    class="icono-ocultar hidden h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 4.4A10.8 10.8 0 0 1 12 4.2c6 0 9.75 7.8 9.75 7.8a18.7 18.7 0 0 1-2.3 3.25M6.2 6.2C3.7 8 2.25 12 2.25 12S6 19.8 12 19.8a9.8 9.8 0 0 0 4.1-.9"
                    />
                </svg>
            </button>
        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const botones =
            document.querySelectorAll(
                '.alternar-password'
            );

        botones.forEach(boton => {
            boton.addEventListener('click', () => {
                const campo =
                    document.getElementById(
                        boton.dataset.target
                    );

                if (!campo) {
                    return;
                }

                const mostrar =
                    campo.type === 'password';

                campo.type =
                    mostrar ? 'text' : 'password';

                boton.querySelector(
                    '.icono-mostrar'
                )?.classList.toggle(
                    'hidden',
                    mostrar
                );

                boton.querySelector(
                    '.icono-ocultar'
                )?.classList.toggle(
                    'hidden',
                    !mostrar
                );

                boton.setAttribute(
                    'aria-pressed',
                    String(mostrar)
                );

                boton.setAttribute(
                    'aria-label',
                    mostrar
                        ? 'Ocultar contraseña'
                        : 'Mostrar contraseña'
                );
            });
        });
    });
</script>