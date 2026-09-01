<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Pacientes
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Consulta y administra los expedientes registrados.
                </p>
            </div>

            <span class="mt-2 inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 sm:mt-0">
                {{ $pacientes->total() }}
                {{ $pacientes->total() === 1 ? 'paciente' : 'pacientes' }}
            </span>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" action="{{ route('pacientes.index') }}" class="flex w-full flex-col gap-2 sm:flex-row lg:max-w-2xl">
                        <div class="relative min-w-0 flex-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.6-5.4a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                            </svg>

                            <input
                                type="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Buscar por nombre o apellido"
                                class="block w-full rounded-xl border-slate-300 py-2.5 pl-10 pr-4 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-900">
                            Buscar
                        </button>

                        @if (request('search'))
                            <a
                                href="{{ route('pacientes.index') }}"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800">
                                Limpiar
                            </a>
                        @endif
                    </form>

                    <a
                        href="{{ route('pacientes.create') }}"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nuevo paciente
                    </a>
                </div>
            </div>

            <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left text-sm">
                        <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-4">Paciente</th>
                                <th class="px-5 py-4">Información</th>
                                <th class="px-5 py-4">Contacto</th>
                                <th class="px-5 py-4">Estado</th>
                                <th class="px-5 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($pacientes as $paciente)
                                <tr class="transition hover:bg-slate-50/70">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <img
                                                src="{{ $paciente->fotoUrl() }}"
                                                alt="Foto de {{ $paciente->nombre }} {{ $paciente->apellido }}"
                                                class="h-11 w-11 shrink-0 rounded-xl border border-slate-200 bg-slate-100 object-cover">

                                            <div class="min-w-0">
                                                <a
                                                    href="{{ route('pacientes.show', $paciente) }}"
                                                    class="block truncate font-semibold text-slate-900 transition hover:text-blue-700">
                                                    {{ $paciente->nombre }} {{ $paciente->apellido }}
                                                </a>

                                                <span
                                                    class="mt-1 inline-flex rounded-full border px-2 py-0.5 text-[11px] font-semibold"
                                                    style="background-color: {{ $paciente->categoria_estilo['fondo'] }}; color: {{ $paciente->categoria_estilo['texto'] }}; border-color: {{ $paciente->categoria_estilo['borde'] }};">
                                                    {{ $paciente->categoria_texto }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-700">
                                            {{ $paciente->edad ?? 'Edad no disponible' }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $paciente->sexo_texto }}
                                            @if ($paciente->tipo_sangre)
                                                · Sangre {{ $paciente->tipo_sangre }}
                                            @endif
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="text-slate-700">
                                            {{ $paciente->telefono ?: 'Sin teléfono' }}
                                        </p>
                                        <p class="mt-1 max-w-56 truncate text-xs text-slate-500">
                                            {{ $paciente->email ?: 'Sin correo electrónico' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $paciente->status ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $paciente->status ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $paciente->status ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('pacientes.show', $paciente) }}"
                                                class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                                                Ver ficha
                                            </a>

                                            <a
                                                href="{{ route('pacientes.edit', $paciente) }}"
                                                class="inline-flex items-center rounded-lg px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                                                Editar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                                            </svg>
                                        </div>
                                        <p class="mt-3 font-medium text-slate-700">No hay pacientes registrados</p>
                                        <p class="mt-1 text-sm text-slate-400">Crea un paciente o modifica la búsqueda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-3 md:hidden">
                @forelse ($pacientes as $paciente)
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="p-4">
                            <div class="flex items-start gap-3">
                                <img
                                    src="{{ $paciente->fotoUrl() }}"
                                    alt="Foto de {{ $paciente->nombre }} {{ $paciente->apellido }}"
                                    class="h-14 w-14 shrink-0 rounded-xl border border-slate-200 bg-slate-100 object-cover">

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <h3 class="truncate font-semibold text-slate-900">
                                                {{ $paciente->nombre }} {{ $paciente->apellido }}
                                            </h3>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $paciente->edad ?? 'Edad no disponible' }} · {{ $paciente->sexo_texto }}
                                            </p>
                                        </div>

                                        <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $paciente->status ? 'bg-emerald-500' : 'bg-slate-400' }}" title="{{ $paciente->status ? 'Activo' : 'Inactivo' }}"></span>
                                    </div>

                                    <span
                                        class="mt-2 inline-flex rounded-full border px-2 py-0.5 text-[11px] font-semibold"
                                        style="background-color: {{ $paciente->categoria_estilo['fondo'] }}; color: {{ $paciente->categoria_estilo['texto'] }}; border-color: {{ $paciente->categoria_estilo['borde'] }};">
                                        {{ $paciente->categoria_texto }}
                                    </span>
                                </div>
                            </div>

                            <dl class="mt-4 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-3 text-sm">
                                <div>
                                    <dt class="text-xs text-slate-400">Teléfono</dt>
                                    <dd class="mt-0.5 font-medium text-slate-700">{{ $paciente->telefono ?: 'No registrado' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-400">Correo electrónico</dt>
                                    <dd class="mt-0.5 truncate font-medium text-slate-700">{{ $paciente->email ?: 'No registrado' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="flex border-t border-slate-100 bg-slate-50/60">
                            <a
                                href="{{ route('pacientes.show', $paciente) }}"
                                class="flex flex-1 items-center justify-center py-3 text-sm font-semibold text-blue-700 transition hover:bg-blue-50">
                                Ver ficha
                            </a>
                            <div class="w-px bg-slate-200"></div>
                            <a
                                href="{{ route('pacientes.edit', $paciente) }}"
                                class="flex flex-1 items-center justify-center py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Editar
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 shadow-sm">
                        No hay pacientes registrados.
                    </div>
                @endforelse
            </div>

            @if ($pacientes->hasPages())
                <div class="mt-6">
                    {{ $pacientes->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>