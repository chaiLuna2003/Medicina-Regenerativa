<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900">Clasificaciones de pacientes</h1>
        <p class="mt-1 text-sm text-slate-500">Pacientes registrados por clasificación. Una persona puede aparecer en varias categorías.</p>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach (\App\Models\Pacientes::CLASIFICACIONES as $clave => $nombre)
                <a href="{{ route('clasificaciones.index', ['clasificacion' => $clave]) }}"
                   @class([
                       'rounded-xl border p-4 shadow-sm transition hover:border-emerald-400',
                       'border-emerald-500 bg-emerald-50' => $seleccionada === $clave,
                       'border-slate-200 bg-white' => $seleccionada !== $clave,
                   ])>
                    <span class="block text-sm font-semibold text-slate-700">{{ $nombre }}</span>
                    <span class="mt-2 block text-2xl font-bold text-slate-900">{{ $totales[$clave] }}</span>
                </a>
            @endforeach
        </div>

        @if ($seleccionada)
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-5">
                    <h2 class="font-semibold text-slate-900">{{ \App\Models\Pacientes::CLASIFICACIONES[$seleccionada] }}: {{ $pacientes->total() }} {{ $pacientes->total() === 1 ? 'paciente' : 'pacientes' }}</h2>
                    <a href="{{ route('clasificaciones.index') }}" class="text-sm font-semibold text-emerald-700">Quitar filtro</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($pacientes as $paciente)
                        <li class="p-4 sm:px-5">
                            <a class="font-medium text-emerald-700 hover:underline" href="{{ route('pacientes.show', $paciente) }}">
                                {{ $paciente->nombre }} {{ $paciente->apellido }}
                            </a>
                        </li>
                    @empty
                        <li class="p-5 text-sm text-slate-500">No hay pacientes en esta clasificación.</li>
                    @endforelse
                </ul>
                @if ($pacientes->hasPages())
                    <div class="border-t border-slate-100 p-5">{{ $pacientes->links() }}</div>
                @endif
            </section>
        @endif
    </div>
</x-app-layout>
