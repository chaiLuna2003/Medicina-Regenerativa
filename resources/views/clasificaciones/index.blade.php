<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900">Clasificaciones de pacientes</h1>
        <p class="mt-1 text-sm text-slate-500">Pacientes registrados por clasificación. Una persona puede aparecer en varias categorías.</p>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('clasificaciones.index') }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <label for="clasificacion" class="block text-sm font-semibold text-slate-700">Clasificación del paciente</label>
            <p class="mt-1 text-sm text-slate-500">Selecciona una clasificación para consultar sus pacientes. El número entre paréntesis indica cuántos hay registrados.</p>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                <select id="clasificacion" name="clasificacion" class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:max-w-md">
                    <option value="">Selecciona una clasificación</option>
                    @foreach (\App\Models\Pacientes::CLASIFICACIONES as $clave => $nombre)
                        <option value="{{ $clave }}" @selected($seleccionada === $clave)>{{ $nombre }} ({{ $totales[$clave] }})</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Consultar</button>
                @if ($seleccionada)
                    <a href="{{ route('clasificaciones.index') }}" class="text-center text-sm font-semibold text-slate-600 hover:text-slate-900">Limpiar</a>
                @endif
            </div>
        </form>

        @if ($seleccionada)
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-5">
                    <h2 class="font-semibold text-slate-900">{{ \App\Models\Pacientes::CLASIFICACIONES[$seleccionada] }}: {{ $pacientes->total() }} {{ $pacientes->total() === 1 ? 'paciente' : 'pacientes' }}</h2>
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
