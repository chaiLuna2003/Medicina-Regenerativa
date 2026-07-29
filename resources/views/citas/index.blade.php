<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Agenda de citas
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Consulta y administra las citas registradas.
                </p>
            </div>

            <a
                href="{{ route('citas.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-[#0D3B7F]
                       px-5 py-2.5 text-sm font-semibold text-white transition
                       hover:bg-[#082a5d]">
                Registrar nueva cita
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Mensaje de éxito --}}
            @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                @if ($citas->isEmpty())
                <div class="px-6 py-16 text-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        No hay citas registradas
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        Registra la primera cita para comenzar a construir la agenda.
                    </p>

                    <a
                        href="{{ route('citas.create') }}"
                        class="mt-6 inline-flex rounded-xl bg-[#0D3B7F]
                                   px-5 py-2.5 text-sm font-semibold text-white
                                   transition hover:bg-[#082a5d]">
                        Registrar cita
                    </a>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Fecha y hora
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Paciente
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Médico
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Motivo
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Estado
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($citas as $cita)
                            @php
                            $estadoClases = match ($cita->estado) {
                            'confirmada' => 'bg-green-100 text-green-700',
                            'en_espera' => 'bg-amber-100 text-amber-700',
                            'en_consulta' => 'bg-blue-100 text-blue-700',
                            'finalizada' => 'bg-gray-100 text-gray-700',
                            'cancelada' => 'bg-red-100 text-red-700',
                            default => 'bg-indigo-100 text-indigo-700',
                            };

                            $estadoTexto = match ($cita->estado) {
                            'en_espera' => 'En espera',
                            'en_consulta' => 'En consulta',
                            default => ucfirst($cita->estado),
                            };
                            @endphp

                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="font-semibold text-gray-900">
                                        {{ $cita->fecha->format('d/m/Y') }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="font-medium text-gray-900">
                                        {{ $cita->paciente?->nombre }}
                                        {{ $cita->paciente?->apellido }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="font-medium text-gray-900">
                                        Dr.
                                        {{ $cita->medico?->nombre }}
                                        {{ $cita->medico?->apellido_paterno }}
                                    </p>

                                    @if ($cita->medico?->especialidad)
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $cita->medico->especialidad }}
                                    </p>
                                    @endif
                                </td>

                                <td class="max-w-xs px-6 py-4 text-sm text-gray-600">
                                    {{ $cita->motivo }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $estadoClases }}">
                                        {{ $estadoTexto }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a
                                        href="{{ route('citas.edit', $cita) }}"
                                        title="Editar cita"
                                        class="inline-flex items-center gap-2 rounded-lg border border-[#0D3B7F]
               px-3 py-2 text-sm font-semibold text-[#0D3B7F]
               transition hover:bg-[#0D3B7F] hover:text-white
               focus:outline-none focus:ring-2 focus:ring-[#0D3B7F]
               focus:ring-offset-2">
                                        <svg
                                            class="h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.8"
                                            stroke="currentColor">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1
                   2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897
                   1.13L6 18l.8-2.685a4.5 4.5 0 0 1
                   1.13-1.897l8.932-8.931ZM16.862 4.487
                   19.5 7.125" />
                                        </svg>

                                        Editar
                                    </a>
                                </td>


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $citas->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>