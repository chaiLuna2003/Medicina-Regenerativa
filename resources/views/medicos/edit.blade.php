<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('medicos.index') }}" class="text-slate-400 hover:text-slate-700" aria-label="Volver al listado">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Editar médico</h2>
                <p class="mt-1 text-sm text-slate-500">Actualiza especialidad, universidad y datos profesionales.</p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-10">
        <div class="mx-auto max-w-2xl px-4 sm:px-6">
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">Revisa los campos marcados antes de continuar.</div>
            @endif
            <form action="{{ route('medicos.update', $medicos) }}" method="POST"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                @csrf
                @method('PUT')
                <div class="mb-7 rounded-xl border border-blue-100 bg-blue-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Médico</p>
                    <p class="mt-1 font-medium text-slate-900">{{ $medicos->user?->name ?? $medicos->nombre }}</p>
                    <p class="mt-0.5 text-sm text-slate-500">Registrado el {{ $medicos->created_at?->format('d/m/Y') ?? '—' }}</p>
                </div>
                @include('medicos._form', [
                'medicos' => $medicos,
                'universidades' => $universidades,
                ])

                <div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
                    <a href="{{ route('medicos.index') }}" class="rounded-lg px-5 py-2.5 text-center text-sm font-medium text-slate-600 hover:bg-slate-100">
                        Cancelar
                    </a>
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
