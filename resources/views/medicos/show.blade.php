<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('medicos.index') }}" class="text-slate-400 hover:text-slate-700" aria-label="Volver al listado">←</a>
                <div>
                    <h1 class="text-xl font-semibold text-slate-900">Ficha del médico</h1>
                    <p class="mt-1 text-sm text-slate-500">Información de la cuenta y perfil profesional.</p>
                </div>
            </div>
            <a href="{{ route('medicos.edit', $medicos) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Editar médico</a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-10">
        <div class="mx-auto max-w-4xl space-y-5 px-4 sm:px-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex flex-col gap-4 border-b border-slate-100 pb-6 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $medicos->user?->name ?? trim($medicos->nombre.' '.$medicos->apellido_paterno.' '.$medicos->apellido_materno) }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $medicos->especialidad }}</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-1.5 text-sm font-medium {{ $medicos->status ? 'text-emerald-700' : 'text-red-700' }}">
                        <span class="h-2 w-2 rounded-full {{ $medicos->status ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                        {{ $medicos->status ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <dl class="mt-6 grid gap-6 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Cédula profesional</dt><dd class="mt-1 font-medium text-slate-900">{{ $medicos->cedula }}</dd></div>
                    <div><dt class="text-slate-500">Consultorio</dt><dd class="mt-1 font-medium text-slate-900">{{ $medicos->consultorio }}</dd></div>
                    <div><dt class="text-slate-500">Teléfono profesional</dt><dd class="mt-1 font-medium text-slate-900">{{ $medicos->telefono }}</dd></div>
                    <div><dt class="text-slate-500">Correo de acceso</dt><dd class="mt-1 break-all font-medium text-slate-900">{{ $medicos->user?->email ?? 'Sin cuenta vinculada' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Dirección profesional</dt><dd class="mt-1 whitespace-pre-line font-medium text-slate-900">{{ $medicos->direccion ?: 'No registrada' }}</dd></div>
                </dl>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="text-base font-semibold text-slate-900">Universidad de procedencia</h2>
                <div class="mt-4 flex flex-wrap items-center gap-5">
                    @if ($medicos->universidad?->logo_path && is_file(public_path($medicos->universidad->logo_path)))
                        <img src="{{ asset($medicos->universidad->logo_path) }}" alt="Logotipo de {{ $medicos->universidad->nombre }}" class="max-h-20 max-w-32 object-contain">
                    @endif
                    <p class="text-sm font-medium text-slate-800">{{ $medicos->universidad?->nombre ?? 'No registrada' }}</p>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
