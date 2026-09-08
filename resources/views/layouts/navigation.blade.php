<nav
    x-data="{ open: false }"
    class="sticky top-0 z-40 border-b border-slate-200
           bg-white/95 shadow-sm backdrop-blur">

    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            {{-- Identidad y navegación principal --}}
            <div class="flex min-w-0 items-center gap-8">

                {{-- Identidad textual --}}
                <a
                    href="{{ route('dashboard') }}"
                    class="group flex shrink-0 items-center"
                    aria-label="Ir al dashboard">

                    <span
                        class="rounded-xl bg-slate-900 px-3 py-2
                               text-sm font-black tracking-tight text-white
                               shadow-sm transition
                               group-hover:bg-emerald-700">
                        SW
                    </span>

                    <span
                        class="ml-2 text-lg font-bold tracking-tight
                               text-slate-900">
                        Médico
                    </span>
                </a>

                {{-- Navegación de escritorio --}}
                <div class="hidden items-center gap-1 sm:flex">

                    <a
                        href="{{ route('dashboard') }}"
                        @class([
                            'rounded-xl px-4 py-2 text-sm font-semibold transition',
                            'bg-emerald-50 text-emerald-700' =>
                                request()->routeIs('dashboard'),
                            'text-slate-600 hover:bg-slate-100 hover:text-slate-900' =>
                                ! request()->routeIs('dashboard'),
                        ])>
                        Dashboard
                    </a>

                    @if (
                        in_array(
                            auth()->user()->role,
                            ['admin', 'recepcionista', 'enfermero'],
                            true
                        )
                    )
                        <a
                            href="{{ route('pacientes.index') }}"
                            @class([
                                'rounded-xl px-4 py-2 text-sm font-semibold transition',
                                'bg-emerald-50 text-emerald-700' =>
                                    request()->routeIs('pacientes.*'),
                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900' =>
                                    ! request()->routeIs('pacientes.*'),
                            ])>
                            Pacientes
                        </a>
                    @endif

                    @if (auth()->user()->isAdmin())
                        <a
                            href="{{ route('medicos.index') }}"
                            @class([
                                'rounded-xl px-4 py-2 text-sm font-semibold transition',
                                'bg-emerald-50 text-emerald-700' =>
                                    request()->routeIs('medicos.*'),
                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900' =>
                                    ! request()->routeIs('medicos.*'),
                            ])>
                            Médicos
                        </a>

                        <a
                            href="{{ route('usuarios.index') }}"
                            @class([
                                'rounded-xl px-4 py-2 text-sm font-semibold transition',
                                'bg-emerald-50 text-emerald-700' =>
                                    request()->routeIs('usuarios.*'),
                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900' =>
                                    ! request()->routeIs('usuarios.*'),
                            ])>
                            Usuarios
                        </a>
                    @endif
                </div>
            </div>

            {{-- Usuario: escritorio --}}
            <div class="hidden items-center sm:flex">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="group flex items-center gap-3 rounded-xl
                                   border border-slate-200 bg-white
                                   px-3 py-2 text-left shadow-sm
                                   transition
                                   hover:border-slate-300 hover:bg-slate-50
                                   focus:outline-none focus:ring-2
                                   focus:ring-emerald-500/30">

                            <span
                                class="h-2 w-2 shrink-0 rounded-full
                                       bg-emerald-500">
                            </span>

                            <span class="min-w-0">
                                <span
                                    class="block max-w-40 truncate
                                           text-sm font-semibold
                                           text-slate-800">
                                    {{ Auth::user()->name }}
                                </span>

                                <span
                                    class="block text-xs capitalize
                                           text-slate-500">
                                    {{ Auth::user()->role }}
                                </span>
                            </span>

                            <svg
                                class="h-4 w-4 shrink-0 text-slate-400
                                       transition group-hover:text-slate-600"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true">
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0
                                       L10 10.586l3.293-3.293
                                       a1 1 0 111.414 1.414l-4 4
                                       a1 1 0 01-1.414 0l-4-4
                                       a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <form
                            method="POST"
                            action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link
                                :href="route('logout')"
                                onclick="
                                    event.preventDefault();
                                    this.closest('form').submit();
                                ">
                                {{ __('Cerrar sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Botón móvil --}}
            <div class="flex items-center sm:hidden">
                <button
                    type="button"
                    @click="open = ! open"
                    class="inline-flex h-10 w-10 items-center
                           justify-center rounded-xl
                           border border-slate-200
                           text-slate-500 transition
                           hover:bg-slate-100 hover:text-slate-800
                           focus:outline-none focus:ring-2
                           focus:ring-emerald-500/30"
                    :aria-expanded="open.toString()"
                    aria-label="Abrir menú">

                    <svg
                        x-show="! open"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>

                    <svg
                        x-show="open"
                        x-cloak
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Navegación móvil --}}
    <div
        x-show="open"
        x-cloak
        x-transition
        @click.outside="open = false"
        class="border-t border-slate-200 bg-white sm:hidden">

        <div class="space-y-1 px-4 py-4">

            <a
                href="{{ route('dashboard') }}"
                @class([
                    'block rounded-xl px-4 py-3 text-sm font-semibold transition',
                    'bg-emerald-50 text-emerald-700' =>
                        request()->routeIs('dashboard'),
                    'text-slate-600 hover:bg-slate-100 hover:text-slate-900' =>
                        ! request()->routeIs('dashboard'),
                ])>
                Dashboard
            </a>

            @if (
                in_array(
                    auth()->user()->role,
                    ['admin', 'recepcionista', 'enfermero'],
                    true
                )
            )
                <a
                    href="{{ route('pacientes.index') }}"
                    @class([
                        'block rounded-xl px-4 py-3 text-sm font-semibold transition',
                        'bg-emerald-50 text-emerald-700' =>
                            request()->routeIs('pacientes.*'),
                        'text-slate-600 hover:bg-slate-100 hover:text-slate-900' =>
                            ! request()->routeIs('pacientes.*'),
                    ])>
                    Pacientes
                </a>
            @endif

            @if (auth()->user()->isAdmin())
                <a
                    href="{{ route('medicos.index') }}"
                    @class([
                        'block rounded-xl px-4 py-3 text-sm font-semibold transition',
                        'bg-emerald-50 text-emerald-700' =>
                            request()->routeIs('medicos.*'),
                        'text-slate-600 hover:bg-slate-100 hover:text-slate-900' =>
                            ! request()->routeIs('medicos.*'),
                    ])>
                    Médicos
                </a>

                <a
                    href="{{ route('usuarios.index') }}"
                    @class([
                        'block rounded-xl px-4 py-3 text-sm font-semibold transition',
                        'bg-emerald-50 text-emerald-700' =>
                            request()->routeIs('usuarios.*'),
                        'text-slate-600 hover:bg-slate-100 hover:text-slate-900' =>
                            ! request()->routeIs('usuarios.*'),
                    ])>
                    Usuarios
                </a>
            @endif
        </div>

        {{-- Usuario: móvil --}}
        <div class="border-t border-slate-200 px-4 py-4">
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <span
                        class="h-2.5 w-2.5 shrink-0 rounded-full
                               bg-emerald-500">
                    </span>

                    <div class="min-w-0">
                        <p
                            class="truncate text-sm font-semibold
                                   text-slate-900">
                            {{ Auth::user()->name }}
                        </p>

                        <p
                            class="truncate text-xs text-slate-500">
                            {{ Auth::user()->email }}
                        </p>

                        <p
                            class="mt-1 text-xs font-semibold capitalize
                                   text-emerald-700">
                            {{ Auth::user()->role }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <a
                    href="{{ route('profile.edit') }}"
                    class="block rounded-xl px-4 py-3
                           text-sm font-semibold text-slate-600
                           transition
                           hover:bg-slate-100 hover:text-slate-900">
                    Perfil
                </a>

                <form
                    method="POST"
                    action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="block w-full rounded-xl px-4 py-3
                               text-left text-sm font-semibold
                               text-rose-600 transition
                               hover:bg-rose-50 hover:text-rose-700">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>