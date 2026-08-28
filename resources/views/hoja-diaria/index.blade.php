<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-3">

            <a
                href="{{ route('dashboard') }}"
                aria-label="Volver al panel"
                class="rounded-lg p-2 text-slate-400
                       transition hover:bg-slate-100
                       hover:text-slate-700">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Hoja diaria
                </h2>

                <p class="mt-0.5 text-sm text-slate-500">
                    Relación de pacientes programados
                </p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-6 sm:py-10">

        <div class="mx-auto max-w-2xl px-4 sm:px-6">

            <div
                class="overflow-hidden rounded-2xl
                       border border-slate-200
                       bg-white shadow-sm">

                {{-- Encabezado de la tarjeta --}}
                <div
                    class="flex items-start gap-4
                           border-b border-slate-100
                           px-5 py-5 sm:px-7 sm:py-6">

                    <div
                        class="flex h-11 w-11 shrink-0
                               items-center justify-center
                               rounded-xl bg-blue-50
                               text-[#0D3B7F]">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7
                                   a2 2 0 01-2-2V5a2 2 0 012-2
                                   h5.586a1 1 0 01.707.293
                                   l3.414 3.414A1 1 0 0117 7.414V19
                                   a2 2 0 01-2 2z" />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-lg font-semibold text-slate-900">
                            Generar documento
                        </h1>

                        <p
                            class="mt-1 text-sm leading-5
                                   text-slate-500">
                            Selecciona los datos que deseas incluir
                            en la hoja diaria.
                        </p>
                    </div>
                </div>

                <form
                    action="{{ route('hoja-diaria.pdf') }}"
                    method="GET"
                    target="_blank"
                    class="p-5 sm:p-7">

                    <div class="grid gap-5 sm:grid-cols-2">

                        {{-- Fecha --}}
                        <div>
                            <label
                                for="fecha-hoja-diaria"
                                class="block text-sm font-medium
                                       text-slate-700">
                                Fecha
                            </label>

                            <input
                                id="fecha-hoja-diaria"
                                type="date"
                                name="fecha"
                                value="{{ old(
                                    'fecha',
                                    $fechaSeleccionada->format('Y-m-d')
                                ) }}"
                                required
                                class="mt-2 block w-full rounded-xl
                                       border-slate-300 px-3 py-2.5
                                       text-sm text-slate-900 shadow-sm
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]">

                            @error('fecha')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Médico autenticado --}}
                        @if ($medicoAutenticado)

                        <div>
                            <p class="block text-sm font-medium text-slate-700">
                                Médico
                            </p>

                            <div
                                class="mt-2 min-h-[42px] rounded-xl
                                           border border-slate-200
                                           bg-slate-50 px-3 py-2.5">

                                <p
                                    class="truncate text-sm font-medium
                                               text-slate-800">
                                    Dr.
                                    {{ $medicoAutenticado->nombre }}
                                    {{ $medicoAutenticado->apellido_paterno }}
                                </p>
                            </div>

                            <p class="mt-1 text-xs text-slate-400">
                                Solo se incluirán tus citas.
                            </p>
                        </div>

                        @else

                        {{-- Selector de médico --}}
                        <div>
                            <label
                                for="medico-hoja-diaria"
                                class="block text-sm font-medium
                                           text-slate-700">
                                Médico
                            </label>

                            <select
                                id="medico-hoja-diaria"
                                name="medico_id"
                                class="mt-2 block w-full rounded-xl
                                           border-slate-300 px-3 py-2.5
                                           text-sm text-slate-900 shadow-sm
                                           focus:border-[#0D3B7F]
                                           focus:ring-[#0D3B7F]">

                                <option value="">
                                    Todos los médicos
                                </option>

                                @foreach ($medicos as $medico)
                                <option
                                    value="{{ $medico->id }}"
                                    @selected(
                                    (string) old( 'medico_id' ,
                                    request('medico_id')
                                    )===(string) $medico->id
                                    )>

                                    Dr. {{ $medico->nombre }}
                                    {{ $medico->apellido_paterno }}

                                    @if ($medico->especialidad)
                                    — {{ $medico->especialidad }}
                                    @endif
                                </option>
                                @endforeach
                            </select>

                            @error('medico_id')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        @endif
                    </div>

                    {{-- Nota informativa --}}
                    <div
                        class="mt-6 flex items-start gap-3
                               rounded-xl bg-slate-50 px-4 py-3">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mt-0.5 h-4 w-4 shrink-0 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01
                                   M21 12a9 9 0 11-18 0
                                   9 9 0 0118 0z" />
                        </svg>

                        <p class="text-xs leading-5 text-slate-500">
                            El PDF incluirá todas las citas ordenadas por hora.
                            Las canceladas aparecerán identificadas para conservar
                            el control completo de la agenda.
                        </p>
                    </div>

                    {{-- Acciones --}}
                    <div
                        class="mt-7 flex flex-col-reverse gap-3
                               border-t border-slate-100 pt-5
                               sm:flex-row sm:justify-end">

                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex w-full items-center
                                   justify-center rounded-xl
                                   border border-slate-300 bg-white
                                   px-5 py-2.5 text-sm font-semibold
                                   text-slate-700 transition
                                   hover:bg-slate-50
                                   sm:w-auto">
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="inline-flex w-full items-center
                                   justify-center gap-2 rounded-xl
                                   bg-[#0D3B7F] px-5 py-2.5
                                   text-sm font-semibold text-white
                                   shadow-sm transition
                                   hover:bg-[#082a5d]
                                   focus:outline-none focus:ring-2
                                   focus:ring-[#0D3B7F]/30
                                   sm:w-auto">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 16v-8m0 8-3-3m3 3 3-3
                                       M5 20h14" />
                            </svg>

                            Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>