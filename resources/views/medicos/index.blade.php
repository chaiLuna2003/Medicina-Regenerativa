<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Médicos</h1>
                <p class="mt-1 text-sm text-slate-500">Consulta los perfiles profesionales vinculados a cuentas médicas.</p>
            </div>
            <a href="{{ route('medicos.create') }}" class="mt-3 inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:mt-0">+ Nuevo médico</a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            <form method="GET" action="{{ route('medicos.index') }}" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_220px_auto]">
                <input type="search" name="buscar" value="{{ request('buscar') }}" aria-label="Buscar médicos" placeholder="Buscar por nombre, correo, especialidad o cédula..." class="rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <select name="estado" aria-label="Filtrar por estado" class="rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                    <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                </select>
                <button type="submit" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Filtrar</button>
            </form>

            <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[850px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Médico</th>
                                <th class="px-5 py-3">Especialidad</th>
                                <th class="px-5 py-3">Universidad</th>
                                <th class="px-5 py-3">Estado</th>
                                <th class="px-5 py-3">Registro</th>
                                <th class="px-5 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($medicos as $medico)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-900">{{ $medico->user?->name ?? trim($medico->nombre.' '.$medico->apellido_paterno.' '.$medico->apellido_materno) }}</p>
                                        <p class="text-xs text-slate-500">{{ $medico->user?->email ?? 'Sin cuenta vinculada' }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $medico->especialidad }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $medico->universidad?->abreviatura ?? 'Sin universidad' }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $medico->status ? 'text-emerald-700' : 'text-red-700' }}">
                                            <span class="h-2 w-2 rounded-full {{ $medico->status ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                            {{ $medico->status ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-500">{{ $medico->created_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-5 py-4 text-right"><a href="{{ route('medicos.show', $medico) }}" class="font-medium text-blue-600 hover:underline">Ver ficha</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No se encontraron médicos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-3 md:hidden">
                @forelse ($medicos as $medico)
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900">{{ $medico->user?->name ?? trim($medico->nombre.' '.$medico->apellido_paterno.' '.$medico->apellido_materno) }}</p>
                                <p class="mt-0.5 break-all text-xs text-slate-500">{{ $medico->user?->email ?? 'Sin cuenta vinculada' }}</p>
                            </div>
                            <span title="{{ $medico->status ? 'Activo' : 'Inactivo' }}" class="h-2.5 w-2.5 shrink-0 rounded-full {{ $medico->status ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                        </div>
                        <p class="mt-3 text-sm text-slate-600">{{ $medico->especialidad }} · {{ $medico->universidad?->abreviatura ?? 'Sin universidad' }}</p>
                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                            <span class="text-xs text-slate-500">Cédula {{ $medico->cedula }}</span>
                            <a href="{{ route('medicos.show', $medico) }}" class="text-sm font-medium text-blue-600">Ver ficha</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl bg-white p-8 text-center text-slate-400">No se encontraron médicos.</div>
                @endforelse
            </div>

            {{ $medicos->links() }}
        </div>
    </div>
</x-app-layout>
