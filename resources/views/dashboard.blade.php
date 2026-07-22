<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <span class="text-sm text-gray-500">
                {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
            </span>
        </div>
    </x-slot>

    @php
        $especialidadTop = $especialidades->sortDesc()->keys()->first();
        $especialidadTopCantidad = $especialidadTop ? $especialidades[$especialidadTop] : 0;
        $ultimoPaciente = $ultimosPacientes->first();
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Grid principal 3x3 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                {{-- 1. Bienvenida --}}
                <div class="relative overflow-hidden   rounded-2xl shadow-lg p-6 text-white flex flex-col justify-between">
                    <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-white/10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <div class="relative z-10">
                        <p class="text-emerald-50 text-sm">Bienvenido de nuevo</p>
                        <h3 class="text-2xl font-bold mt-1">{{ explode(' ', auth()->user()->name)[0] }} 👋</h3>
                    </div>
                    <p class="relative z-10 text-emerald-50 text-sm mt-6">
                        Panel de <span class="font-semibold text-white">Medicina Regenerativa</span>
                    </p>
                </div>

                {{-- 2. Pacientes registrados --}}
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div class="bg-emerald-50 text-emerald-600 rounded-xl p-3 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                            +{{ $pacientesNuevos }} / 7 días
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-bold text-gray-800">{{ $totalPacientes }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">Pacientes registrados</p>
                    </div>
                </div>

                {{-- 3. Médicos activos --}}
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div class="bg-blue-50 text-blue-600 rounded-xl p-3 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M5 6a2 2 0 012-2h10a2 2 0 012 2v14l-3-2-3 2-3-2-3 2V6z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                            de {{ $totalMedicos }} totales
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-bold text-gray-800">{{ $medicosActivos }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">Médicos activos</p>
                    </div>
                </div>

                {{-- 4. Especialidades activas --}}
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div class="bg-amber-50 text-amber-600 rounded-xl p-3 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4 2 2 0 000-4zm0 10v2m0-2a2 2 0 100-4 2 2 0 000 4zm-6-4h2m-2 0a2 2 0 100-4 2 2 0 000 4zm12 0h2m-2 0a2 2 0 100-4 2 2 0 000 4z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-bold text-gray-800">{{ $especialidades->count() }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">Especialidades activas</p>
                    </div>
                </div>

                {{-- 5. Especialidad más común --}}
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div class="bg-rose-50 text-rose-600 rounded-xl p-3 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xl font-bold text-gray-800 truncate">{{ $especialidadTop ?? 'Sin datos' }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            @if ($especialidadTop)
                                Especialidad más común · {{ $especialidadTopCantidad }} médico(s)
                            @else
                                Especialidad más común
                            @endif
                        </p>
                    </div>
                </div>

                {{-- 6. Último paciente registrado --}}
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div class="bg-violet-50 text-violet-600 rounded-xl p-3 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        @if ($ultimoPaciente)
                            <p class="text-xl font-bold text-gray-800 truncate">{{ $ultimoPaciente->nombre }} {{ $ultimoPaciente->apellido }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">Registrado {{ $ultimoPaciente->created_at->diffForHumans() }}</p>
                        @else
                            <p class="text-xl font-bold text-gray-800">Sin registros</p>
                            <p class="text-sm text-gray-500 mt-0.5">Último paciente registrado</p>
                        @endif
                    </div>
                </div>

                {{-- 7. Tu perfil --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-lg font-semibold shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        <span class="inline-block mt-1 text-xs font-medium text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full capitalize">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                </div>

                {{-- 8. Médicos por especialidad (mini gráfico) --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Médicos por especialidad</h3>
                    @if ($especialidades->isEmpty())
                        <p class="text-sm text-gray-400">No hay médicos activos registrados.</p>
                    @else
                        <div class="space-y-2.5">
                            @foreach ($especialidades->take(3) as $especialidad => $cantidad)
                                <div>
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span class="truncate pr-2">{{ $especialidad }}</span>
                                        <span class="font-medium shrink-0">{{ $cantidad }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2 rounded-full"
                                             style="width: {{ $medicosActivos > 0 ? ($cantidad / $medicosActivos * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- 9. Accesos rápidos --}}
                <div class="bg-gray-900 rounded-2xl shadow-sm p-6 flex flex-col justify-between">
                    <h3 class="text-sm font-semibold text-gray-200 mb-4">Accesos rápidos</h3>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('pacientes.create') }}"
                           class="inline-flex items-center justify-between gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            + Nuevo paciente
                        </a>
                        <a href="{{ route('pacientes.index') }}"
                           class="inline-flex items-center justify-between gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Ver pacientes
                        </a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('medicos.create') }}"
                               class="inline-flex items-center justify-between gap-2 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                + Nuevo médico
                            </a>
                            <a href="{{ route('medicos.index') }}"
                               class="inline-flex items-center justify-between gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Ver médicos
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Últimos pacientes registrados --}}
            <div class="mt-6 bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Últimos pacientes registrados</h3>
                    <a href="{{ route('pacientes.index') }}" class="text-xs text-emerald-600 hover:underline">Ver todos</a>
                </div>
                <ul class="divide-y divide-gray-100">
                    @forelse ($ultimosPacientes as $paciente)
                        <li class="px-6 py-3 flex items-center gap-3 hover:bg-gray-50 transition-colors">
                            <img src="{{ $paciente->fotoUrl() }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $paciente->nombre }} {{ $paciente->apellido }}</p>
                                <p class="text-xs text-gray-400">{{ $paciente->created_at->diffForHumans() }}</p>
                            </div>
                            <a href="{{ route('pacientes.show', $paciente) }}" class="text-xs text-blue-600 hover:underline shrink-0">Ver</a>
                        </li>
                    @empty
                        <li class="px-6 py-6 text-center text-sm text-gray-400">Aún no hay pacientes registrados.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
