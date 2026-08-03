@php
    $roles = [
        'admin' => 'Administrador',
        'medico' => 'Médico',
        'enfermero' => 'Enfermero',
        'recepcionista' => 'Recepcionista',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Usuarios y roles
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Controla quién puede acceder al sistema.
                </p>
            </div>

            <a
                href="{{ route('usuarios.create') }}"
                class="mt-3 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:mt-0"
            >
                + Nuevo usuario
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Buscador y filtro --}}
            <form
                method="GET"
                action="{{ route('usuarios.index') }}"
                class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_220px_auto]"
            >
                <input
                    type="search"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Buscar por nombre o correo..."
                    class="rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                >

                <select
                    name="role"
                    class="rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Todos los roles</option>

                    @foreach ($roles as $value => $label)
                        <option
                            value="{{ $value }}"
                            @selected(request('role') === $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <button
                    type="submit"
                    class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700"
                >
                    Filtrar
                </button>
            </form>

            {{-- Tabla para escritorio --}}
            <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Usuario</th>
                            <th class="px-5 py-3">Rol</th>
                            <th class="px-5 py-3">Estado</th>
                            <th class="px-5 py-3">Registro</th>
                            <th class="px-5 py-3 text-right">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($usuarios as $usuario)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <p class="font-medium text-slate-900">
                                        {{ $usuario->name }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $usuario->email }}
                                    </p>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        {{ $roles[$usuario->role] ?? ucfirst($usuario->role) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $usuario->status ? 'text-emerald-700' : 'text-red-700' }}">
                                        <span class="h-2 w-2 rounded-full {{ $usuario->status ? 'bg-emerald-500' : 'bg-red-500' }}"></span>

                                        {{ $usuario->status ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-slate-500">
                                    {{ $usuario->created_at->format('d/m/Y') }}
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a
                                        href="{{ route('usuarios.edit', $usuario) }}"
                                        class="font-medium text-blue-600 hover:underline"
                                    >
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="5"
                                    class="px-5 py-10 text-center text-slate-400"
                                >
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tarjetas para teléfonos --}}
            <div class="space-y-3 md:hidden">
                @forelse ($usuarios as $usuario)
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">
                                    {{ $usuario->name }}
                                </p>

                                <p class="mt-0.5 break-all text-xs text-slate-500">
                                    {{ $usuario->email }}
                                </p>
                            </div>

                            <span
                                title="{{ $usuario->status ? 'Activo' : 'Inactivo' }}"
                                class="h-2.5 w-2.5 shrink-0 rounded-full {{ $usuario->status ? 'bg-emerald-500' : 'bg-red-500' }}"
                            ></span>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                            <span class="text-xs font-medium text-blue-700">
                                {{ $roles[$usuario->role] ?? ucfirst($usuario->role) }}
                            </span>

                            <a
                                href="{{ route('usuarios.edit', $usuario) }}"
                                class="text-sm font-medium text-blue-600"
                            >
                                Editar
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl bg-white p-8 text-center text-slate-400">
                        No se encontraron usuarios.
                    </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            {{ $usuarios->links() }}
        </div>
    </div>
</x-app-layout>