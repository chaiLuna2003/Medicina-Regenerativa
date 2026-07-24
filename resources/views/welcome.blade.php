<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta
        name="description"
        content="Sistema clínico para la gestión de pacientes, citas y expedientes médicos."
    >

    <meta name="theme-color" content="#0f766e">

    <title>{{ config('app.name', 'Medicina Regenerativa') }}</title>

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

<body class="min-h-screen bg-[#F4F8F7] text-slate-900 antialiased">

    <div class="relative flex min-h-screen flex-col overflow-hidden">

        {{-- Decoraciones de fondo --}}
        <div
            class="pointer-events-none absolute -left-40 -top-40 h-[420px] w-[420px] rounded-full bg-teal-200/40 blur-3xl"
        ></div>

        <div
            class="pointer-events-none absolute -bottom-48 -right-40 h-[480px] w-[480px] rounded-full bg-emerald-200/30 blur-3xl"
        ></div>

        {{-- HEADER --}}
        <header class="relative z-20">
            <div
                class="mx-auto flex min-h-[82px] w-full max-w-7xl items-center justify-between px-5 sm:px-8 lg:px-10"
            >
                {{-- Marca --}}
                <a
                    href="{{ url('/') }}"
                    class="group flex items-center gap-3 rounded-xl outline-none focus-visible:ring-4 focus-visible:ring-teal-600/20"
                    aria-label="Ir al inicio"
                >
                    <span
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-700 text-white shadow-lg shadow-teal-700/20 transition-transform duration-300 group-hover:-translate-y-0.5"
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
                        <strong class="block text-sm font-bold tracking-tight text-slate-900 sm:text-base">
                            Medicina Regenerativa
                        </strong>

                        <span class="hidden text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 sm:block">
                            Gestión clínica
                        </span>
                    </span>
                </a>

                {{-- Acceso superior --}}
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white/80 px-4 text-sm font-semibold text-slate-700 shadow-sm backdrop-blur-md transition-all duration-300 hover:-translate-y-0.5 hover:border-teal-200 hover:text-teal-800 hover:shadow-md focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-600/20"
                    >
                        Ir al panel

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M9 18L15 12L9 6"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white/80 px-4 text-sm font-semibold text-slate-700 shadow-sm backdrop-blur-md transition-all duration-300 hover:-translate-y-0.5 hover:border-teal-200 hover:text-teal-800 hover:shadow-md focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-600/20"
                    >
                        Iniciar sesión

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M9 18L15 12L9 6"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </a>
                @endauth
            </div>
        </header>

        {{-- CONTENIDO PRINCIPAL --}}
        <main class="relative z-10 flex flex-1 items-center">
            <div
                class="mx-auto grid w-full max-w-7xl items-center gap-14 px-5 py-14 sm:px-8 md:py-20 lg:grid-cols-[1.05fr_0.95fr] lg:gap-20 lg:px-10 lg:py-24"
            >
                {{-- CONTENIDO IZQUIERDO --}}
                <section class="text-center lg:text-left">

                    <div
                        class="mb-6 inline-flex items-center gap-3 text-xs font-bold uppercase tracking-[0.18em] text-teal-800"
                    >
                        <span class="relative flex h-3 w-3">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-teal-400 opacity-50"
                            ></span>

                            <span
                                class="relative inline-flex h-3 w-3 rounded-full border-2 border-[#F4F8F7] bg-teal-500"
                            ></span>
                        </span>

                        Plataforma clínica segura
                    </div>

                    <h1
                        class="mx-auto max-w-3xl text-4xl font-bold leading-[1.04] tracking-[-0.045em] text-slate-950 sm:text-5xl md:text-6xl lg:mx-0 lg:text-[68px]"
                    >
                        Cuidado médico con una gestión
                        <span class="text-teal-700">más humana.</span>
                    </h1>

                    <p
                        class="mx-auto mt-7 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg lg:mx-0"
                    >
                        Administra pacientes, citas y expedientes desde un mismo
                        lugar. Información clara para que tu equipo dedique más
                        tiempo a lo que realmente importa: la atención médica.
                    </p>

                    {{-- Botones --}}
                    <div
                        class="mt-9 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start"
                    >
                        @auth
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex min-h-12 items-center justify-center gap-3 rounded-xl bg-teal-700 px-6 text-sm font-bold text-white shadow-xl shadow-teal-700/20 transition-all duration-300 hover:-translate-y-1 hover:bg-teal-800 hover:shadow-2xl focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-600/30"
                            >
                                Acceder al panel

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
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex min-h-12 items-center justify-center gap-3 rounded-xl bg-teal-700 px-6 text-sm font-bold text-white shadow-xl shadow-teal-700/20 transition-all duration-300 hover:-translate-y-1 hover:bg-teal-800 hover:shadow-2xl focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-600/30"
                            >
                                Acceder al sistema

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
                            </a>
                        @endauth

                        
                    </div>

                    {{-- Características --}}
                    <ul
                        class="mt-9 flex flex-wrap justify-center gap-x-7 gap-y-3 text-sm text-slate-500 lg:justify-start"
                    >
                        <li class="flex items-center gap-2">
                            <svg
                                class="h-5 w-5 text-teal-700"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M5 12L9 16L19 6"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            Acceso por roles
                        </li>

                        <li class="flex items-center gap-2">
                            <svg
                                class="h-5 w-5 text-teal-700"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M5 12L9 16L19 6"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            Información centralizada
                        </li>
                    </ul>
                </section>

                {{-- PANEL VISUAL DERECHO --}}
                <section
                    id="plataforma"
                    class="relative mx-auto w-full max-w-xl scroll-mt-10"
                    aria-label="Módulos principales de la plataforma"
                >
                    <div
                        class="absolute inset-x-8 -bottom-8 top-12 -z-10 rounded-full bg-teal-400/20 blur-3xl"
                    ></div>

                    <div
                        class="rounded-[26px] border border-white/80 bg-white/80 p-3 shadow-[0_30px_80px_rgba(30,64,56,0.14)] backdrop-blur-xl sm:p-5"
                    >
                        {{-- Encabezado del panel --}}
                        <div
                            class="flex items-center justify-between gap-4 px-2 pb-5 pt-2"
                        >
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-800">
                                <svg
                                    class="h-5 w-5 text-teal-700"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M4 19V9M9 19V5M14 19V12M19 19V3"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </svg>

                                Vista general
                            </div>

                            <span
                                class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-emerald-500 ring-4 ring-emerald-100"
                                ></span>

                                Sistema activo
                            </span>
                        </div>

                        {{-- Tarjeta destacada --}}
                        <div
                            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-800 via-teal-700 to-emerald-600 p-6 text-white sm:p-7"
                        >
                            <div
                                class="absolute -right-12 -top-12 h-44 w-44 rounded-full border-[28px] border-white/5"
                            ></div>

                            <svg
                                class="absolute -bottom-5 -right-4 h-32 w-32 text-white/[0.06]"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M12 4V20M4 12H20"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                            </svg>

                            <div class="relative z-10">
                                <span class="text-xs font-medium text-teal-100">
                                    Atención clínica integral
                                </span>

                                <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">
                                    Todo tu equipo conectado
                                </h2>

                                <p class="mt-3 max-w-md text-sm leading-6 text-teal-50/80">
                                    Consulta la información esencial y continúa
                                    cada proceso sin perder el contexto del paciente.
                                </p>
                            </div>
                        </div>

                        {{-- Módulos --}}
                        <div class="mt-3 grid gap-3 sm:grid-cols-3">

                            {{-- Pacientes --}}
                            <article
                                class="group rounded-2xl border border-slate-200/80 bg-white p-4 transition-all duration-300 hover:-translate-y-1 hover:border-teal-200 hover:shadow-lg"
                            >
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700 transition-colors group-hover:bg-teal-700 group-hover:text-white"
                                >
                                    <svg
                                        class="h-5 w-5"
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

                                        <path
                                            d="M9 11C11.2 11 13 9.2 13 7C13 4.8 11.2 3 9 3C6.8 3 5 4.8 5 7C5 9.2 6.8 11 9 11Z"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        />

                                        <path
                                            d="M17 8V14M20 11H14"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </div>

                                <h3 class="mt-5 text-sm font-bold text-slate-800">
                                    Pacientes
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Registro organizado
                                </p>
                            </article>

                            {{-- Citas --}}
                            <article
                                class="group rounded-2xl border border-slate-200/80 bg-white p-4 transition-all duration-300 hover:-translate-y-1 hover:border-teal-200 hover:shadow-lg"
                            >
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700 transition-colors group-hover:bg-teal-700 group-hover:text-white"
                                >
                                    <svg
                                        class="h-5 w-5"
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
                                </div>

                                <h3 class="mt-5 text-sm font-bold text-slate-800">
                                    Citas
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Agenda centralizada
                                </p>
                            </article>

                            {{-- Expedientes --}}
                            <article
                                class="group rounded-2xl border border-slate-200/80 bg-white p-4 transition-all duration-300 hover:-translate-y-1 hover:border-teal-200 hover:shadow-lg"
                            >
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700 transition-colors group-hover:bg-teal-700 group-hover:text-white"
                                >
                                    <svg
                                        class="h-5 w-5"
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
                                </div>

                                <h3 class="mt-5 text-sm font-bold text-slate-800">
                                    Expedientes
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Seguimiento clínico
                                </p>
                            </article>

                        </div>
                    </div>
                </section>
            </div>
        </main>

        {{-- FOOTER --}}
        <footer class="relative z-10">
            <div
                class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-2 border-t border-slate-200/80 px-5 py-6 text-center text-xs text-slate-500 sm:flex-row sm:px-8 sm:text-left lg:px-10"
            >
                <p>
                    © {{ date('Y') }} Medicina Regenerativa
                </p>

                <p>
                    Uso exclusivo de personal autorizado
                </p>
            </div>
        </footer>

    </div>

</body>
</html>