<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta
        name="description"
        content="Acceso al sistema de gestión clínica de Medicina Regenerativa."
    >

    <meta name="theme-color" content="#0f766e">

    <title>Iniciar sesión | {{ config('app.name', 'Medicina Regenerativa') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700"
        rel="stylesheet"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: "Instrument Sans", sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-white text-slate-900 antialiased">

    <main class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">

        {{-- PANEL VISUAL IZQUIERDO --}}
        <section
            class="relative hidden min-h-screen overflow-hidden bg-teal-900 lg:flex lg:flex-col lg:justify-between"
            aria-label="Información de la plataforma"
        >
            {{-- Decoraciones --}}
            <div
                class="pointer-events-none absolute -left-32 -top-32 h-[420px] w-[420px] rounded-full bg-teal-400/20 blur-3xl"
            ></div>

            <div
                class="pointer-events-none absolute -bottom-44 -right-32 h-[520px] w-[520px] rounded-full bg-emerald-300/20 blur-3xl"
            ></div>

            <div
                class="pointer-events-none absolute right-[10%] top-[16%] h-64 w-64 rounded-full border border-white/10"
            ></div>

            <div
                class="pointer-events-none absolute right-[14%] top-[20%] h-48 w-48 rounded-full border border-white/10"
            ></div>

            {{-- Logo --}}
            <div class="relative z-10 p-10 xl:p-14">
                <a
                    href="{{ url('/') }}"
                    class="inline-flex items-center gap-3 rounded-xl text-white outline-none focus-visible:ring-4 focus-visible:ring-white/30"
                    aria-label="Volver al inicio"
                >
                    <span
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15 text-white backdrop-blur-md"
                    >
                        <svg
                            class="h-7 w-7"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M12 4V20M4 12H20"
                                stroke="currentColor"
                                stroke-width="2.2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>

                    <span>
                        <strong class="block text-base font-bold tracking-tight">
                            Medicina Regenerativa
                        </strong>

                        <span class="mt-0.5 block text-[11px] font-semibold uppercase tracking-[0.18em] text-teal-100/70">
                            Gestión clínica
                        </span>
                    </span>
                </a>
            </div>

            {{-- Contenido principal --}}
            <div class="relative z-10 px-10 pb-10 xl:px-14 xl:pb-14">
                <div class="max-w-2xl">

                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold text-teal-50 backdrop-blur-md"
                    >
                        <span class="h-2 w-2 rounded-full bg-emerald-300"></span>

                        Plataforma clínica segura
                    </span>

                    <h1
                        class="mt-7 max-w-xl text-4xl font-bold leading-[1.08] tracking-[-0.045em] text-white xl:text-5xl"
                    >
                        Información médica clara para una atención más humana.
                    </h1>

                    <p class="mt-6 max-w-lg text-base leading-7 text-teal-50/70">
                        Accede de forma segura a pacientes, citas y expedientes
                        clínicos desde una plataforma diseñada para facilitar el
                        trabajo de todo el equipo médico.
                    </p>

                    {{-- Tarjeta visual --}}
                    <div
                        class="mt-10 max-w-xl rounded-2xl border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur-xl"
                    >
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-teal-800"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M12 22C16.4 20 20 16.5 20 11V5L12 2L4 5V11C4 16.5 7.6 20 12 22Z"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linejoin="round"
                                        />

                                        <path
                                            d="M9 12L11 14L15 9"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <strong class="block text-sm font-bold text-white">
                                        Acceso protegido
                                    </strong>

                                    <span class="mt-1 block text-xs text-teal-100/60">
                                        Controlado mediante usuarios y roles
                                    </span>
                                </div>
                            </div>

                            <span
                                class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-200"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-emerald-300 ring-4 ring-emerald-300/10"
                                ></span>

                                Activo
                            </span>
                        </div>
                    </div>

                    {{-- Características --}}
                    <div class="mt-8 grid max-w-xl grid-cols-3 gap-3">
                        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                            <svg
                                class="h-5 w-5 text-teal-200"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M16 21V19C16 16.8 14.2 15 12 15H6C3.8 15 2 16.8 2 19V21"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />

                                <circle
                                    cx="9"
                                    cy="7"
                                    r="4"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                />
                            </svg>

                            <span class="mt-3 block text-xs font-semibold text-white">
                                Pacientes
                            </span>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                            <svg
                                class="h-5 w-5 text-teal-200"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M8 2V6M16 2V6M3 10H21"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />

                                <path
                                    d="M5 4H19C20.1 4 21 4.9 21 6V20H3V6C3 4.9 3.9 4 5 4Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="mt-3 block text-xs font-semibold text-white">
                                Citas
                            </span>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                            <svg
                                class="h-5 w-5 text-teal-200"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M14 2H6C4.9 2 4 2.9 4 4V20H20V8L14 2Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />

                                <path
                                    d="M14 2V8H20M8 13H16M8 17H13"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="mt-3 block text-xs font-semibold text-white">
                                Expedientes
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pie del panel --}}
            <div
                class="relative z-10 flex items-center justify-between border-t border-white/10 px-10 py-6 text-xs text-teal-100/50 xl:px-14"
            >
                <span>Medicina Regenerativa</span>
                <span>Uso autorizado</span>
            </div>
        </section>

        {{-- PANEL DERECHO: LOGIN --}}
        <section class="relative flex min-h-screen items-center justify-center bg-white px-5 py-10 sm:px-8 lg:px-12">

            {{-- Decoración móvil --}}
            <div
                class="pointer-events-none absolute -right-32 -top-32 h-72 w-72 rounded-full bg-teal-100/70 blur-3xl lg:hidden"
            ></div>

            <div class="relative z-10 w-full max-w-md">

                {{-- Logo móvil --}}
                <a
                    href="{{ url('/') }}"
                    class="mb-12 inline-flex items-center gap-3 rounded-xl outline-none focus-visible:ring-4 focus-visible:ring-teal-600/20 lg:hidden"
                    aria-label="Volver al inicio"
                >
                    <span
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-700 text-white shadow-lg shadow-teal-700/20"
                    >
                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M12 4V20M4 12H20"
                                stroke="currentColor"
                                stroke-width="2.2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>

                    <span>
                        <strong class="block text-sm font-bold text-slate-900">
                            Medicina Regenerativa
                        </strong>

                        <span class="block text-[10px] font-semibold uppercase tracking-[0.15em] text-slate-500">
                            Gestión clínica
                        </span>
                    </span>
                </a>

                {{-- Regresar --}}
                <a
                    href="{{ url('/') }}"
                    class="mb-8 hidden w-fit items-center gap-2 text-sm font-semibold text-slate-500 transition-colors hover:text-teal-700 lg:inline-flex"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="M19 12H5M11 18L5 12L11 6"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                    Volver al inicio
                </a>

                {{-- Encabezado --}}
                <div>
                    <span class="text-sm font-bold text-teal-700">
                        Bienvenido de nuevo
                    </span>

                    <h2
                        class="mt-2 text-3xl font-bold tracking-[-0.035em] text-slate-950 sm:text-4xl"
                    >
                        Inicia sesión
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-slate-500 sm:text-base">
                        Ingresa tus credenciales para acceder al sistema clínico.
                    </p>
                </div>

                {{-- Estado de sesión --}}
                @if (session('status'))
                    <div
                        class="mt-7 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800"
                        role="status"
                    >
                        <svg
                            class="mt-0.5 h-5 w-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <path
                                d="M8 12L11 15L16 9"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Formulario --}}
                <form
                    method="POST"
                    action="{{ route('login') }}"
                    class="mt-8 space-y-6"
                >
                    @csrf

                    {{-- Correo --}}
                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Correo electrónico
                        </label>

                        <div class="relative">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />

                                    <path
                                        d="M22 6L12 13L2 6"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </div>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="nombre@correo.com"
                                class="block min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50/70 py-3 pl-12 pr-4 text-sm text-slate-900 outline-none transition-all placeholder:text-slate-400 hover:border-slate-300 focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-600/10"
                            >
                        </div>

                        @error('email')
                            <p class="mt-2 flex items-center gap-2 text-sm text-red-600">
                                <svg
                                    class="h-4 w-4 shrink-0"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    />

                                    <path
                                        d="M12 8V12M12 16H12.01"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </svg>

                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Contraseña --}}
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-4">
                            <label
                                for="password"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Contraseña
                            </label>

                            @if (Route::has('password.request'))
                                <a
                                    href="{{ route('password.request') }}"
                                    class="text-xs font-semibold text-teal-700 transition-colors hover:text-teal-900 hover:underline"
                                >
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        <div class="relative">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <rect
                                        x="4"
                                        y="10"
                                        width="16"
                                        height="11"
                                        rx="2"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />

                                    <path
                                        d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </div>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="Ingresa tu contraseña"
                                class="block min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50/70 py-3 pl-12 pr-12 text-sm text-slate-900 outline-none transition-all placeholder:text-slate-400 hover:border-slate-300 focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-600/10"
                            >

                            <button
                                id="toggle-password"
                                type="button"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition-colors hover:text-teal-700 focus:outline-none focus-visible:text-teal-700"
                                aria-label="Mostrar contraseña"
                                aria-pressed="false"
                            >
                                {{-- Mostrar --}}
                                <svg
                                    id="eye-open"
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M2 12C3.8 7.8 7.4 5 12 5C16.6 5 20.2 7.8 22 12C20.2 16.2 16.6 19 12 19C7.4 19 3.8 16.2 2 12Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="3"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />
                                </svg>

                                {{-- Ocultar --}}
                                <svg
                                    id="eye-closed"
                                    class="hidden h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M3 3L21 21M10.6 10.6C10.2 11 10 11.5 10 12C10 13.1 10.9 14 12 14C12.5 14 13 13.8 13.4 13.4"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />

                                    <path
                                        d="M9.9 5.2C10.6 5.1 11.3 5 12 5C16.6 5 20.2 7.8 22 12C21.3 13.5 20.4 14.8 19.3 15.9M6.2 6.2C4.3 7.5 2.9 9.5 2 12C3.8 16.2 7.4 19 12 19C13.6 19 15.1 18.6 16.4 17.9"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>
                        </div>

                        @error('password')
                            <p class="mt-2 flex items-center gap-2 text-sm text-red-600">
                                <svg
                                    class="h-4 w-4 shrink-0"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    />

                                    <path
                                        d="M12 8V12M12 16H12.01"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </svg>

                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Recordarme --}}
                    <div>
                        <label
                            for="remember_me"
                            class="inline-flex cursor-pointer items-center gap-3"
                        >
                            <input
                                id="remember_me"
                                name="remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-teal-700 shadow-sm focus:ring-2 focus:ring-teal-600/30"
                            >

                            <span class="text-sm text-slate-600">
                                Mantener mi sesión iniciada
                            </span>
                        </label>
                    </div>

                    {{-- Botón --}}
                    <button
                        type="submit"
                        class="inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-xl bg-teal-700 px-6 text-sm font-bold text-white shadow-xl shadow-teal-700/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-teal-800 hover:shadow-2xl focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-600/30"
                    >
                        Iniciar sesión

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M5 12H19M13 6L19 12L13 18"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </button>
                </form>

                {{-- Registro --}}
                @if (Route::has('register'))
                    <div class="mt-8 border-t border-slate-200 pt-7 text-center">
                        <p class="text-sm text-slate-500">
                            ¿No tienes una cuenta?

                            <a
                                href="{{ route('register') }}"
                                class="font-bold text-teal-700 transition-colors hover:text-teal-900 hover:underline"
                            >
                                Regístrate
                            </a>
                        </p>
                    </div>
                @endif

                <p class="mt-8 text-center text-xs leading-5 text-slate-400">
                    Al ingresar confirmas que eres personal autorizado para
                    utilizar la plataforma.
                </p>
            </div>
        </section>
    </main>

    {{-- Mostrar u ocultar contraseña --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('toggle-password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            if (!passwordInput || !toggleButton) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                const passwordIsHidden = passwordInput.type === 'password';

                passwordInput.type = passwordIsHidden ? 'text' : 'password';

                eyeOpen.classList.toggle('hidden', passwordIsHidden);
                eyeClosed.classList.toggle('hidden', !passwordIsHidden);

                toggleButton.setAttribute(
                    'aria-label',
                    passwordIsHidden
                        ? 'Ocultar contraseña'
                        : 'Mostrar contraseña'
                );

                toggleButton.setAttribute(
                    'aria-pressed',
                    passwordIsHidden ? 'true' : 'false'
                );
            });
        });
    </script>

</body>
</html>