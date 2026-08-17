<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-sm font-medium text-[#0D3B7F]">
                    Expediente clínico
                </p>

                <h2 class="text-xl font-bold text-gray-900">
                    Historial de estudios
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $paciente->nombre }}
                    {{ $paciente->apellido }}
                </p>
            </div>

            <a
                href="{{ url()->previous() }}"
                class="inline-flex items-center justify-center rounded-xl
                       border border-gray-300 bg-white px-5 py-2.5
                       text-sm font-semibold text-gray-700
                       transition hover:bg-gray-50"
            >
                Volver
            </a>

        </div>
    </x-slot>


    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div
                    class="mb-6 rounded-xl border border-green-200
                           bg-green-50 px-4 py-3 text-sm
                           font-medium text-green-700"
                >
                    {{ session('success') }}
                </div>
            @endif


            {{-- Resumen --}}
            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Paciente
                        </p>

                        <h3 class="mt-1 text-lg font-bold text-gray-900">
                            {{ $paciente->nombre }}
                            {{ $paciente->apellido }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Historial documental asociado a sus citas.
                        </p>
                    </div>

                    <div
                        class="inline-flex w-fit items-center rounded-xl
                               bg-blue-50 px-4 py-2"
                    >
                        <span class="text-sm font-semibold text-[#0D3B7F]">
                            {{ $estudios->total() }}
                            {{ $estudios->total() === 1 ? 'estudio' : 'estudios' }}
                        </span>
                    </div>

                </div>
            </div>


            {{-- Historial --}}
            @forelse ($estudios as $estudio)

                <article
                    class="mb-4 overflow-hidden rounded-2xl
                           border border-gray-200 bg-white shadow-sm"
                >

                    <div class="p-5 sm:p-6">

                        <div
                            class="flex flex-col gap-5
                                   md:flex-row md:items-start
                                   md:justify-between"
                        >

                            {{-- Información principal --}}
                            <div class="min-w-0 flex-1">

                                <div class="flex items-start gap-4">

                                    {{-- Icono --}}
                                    <div
                                        class="flex h-11 w-11 shrink-0
                                               items-center justify-center
                                               rounded-xl bg-red-50
                                               text-red-600"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 21h10a2 2 0 0 0 2-2V7
                                                   l-5-5H7a2 2 0 0 0-2 2v15
                                                   a2 2 0 0 0 2 2z"
                                            />
                                        </svg>
                                    </div>


                                    <div class="min-w-0">

                                        <p
                                            class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-[#0D3B7F]"
                                        >
                                            {{ $estudio->fecha_estudio->format('d/m/Y') }}
                                        </p>

                                        <h3
                                            class="mt-1 text-lg font-bold
                                                   text-gray-900"
                                        >
                                            {{ $estudio->nombre }}
                                        </h3>

                                        <p
                                            class="mt-1 truncate text-sm
                                                   text-gray-500"
                                        >
                                            {{ $estudio->archivo_original }}
                                        </p>

                                    </div>
                                </div>


                                @if ($estudio->descripcion)
                                    <p
                                        class="mt-4 max-w-3xl text-sm
                                               leading-6 text-gray-600"
                                    >
                                        {{ $estudio->descripcion }}
                                    </p>
                                @endif


                                {{-- Metadata --}}
                                <div
                                    class="mt-5 grid gap-3
                                           sm:grid-cols-2 lg:grid-cols-3"
                                >

                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <p
                                            class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-gray-400"
                                        >
                                            Médico
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-semibold
                                                   text-gray-700"
                                        >
                                            @if ($estudio->cita?->medico)
                                                Dr.
                                                {{ $estudio->cita->medico->nombre }}
                                                {{ $estudio->cita->medico->apellido_paterno }}
                                            @else
                                                No disponible
                                            @endif
                                        </p>
                                    </div>


                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <p
                                            class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-gray-400"
                                        >
                                            Cita
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-semibold
                                                   text-gray-700"
                                        >
                                            {{ $estudio->cita?->fecha?->format('d/m/Y') ?? 'No disponible' }}
                                        </p>
                                    </div>


                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <p
                                            class="text-xs font-semibold
                                                   uppercase tracking-wide
                                                   text-gray-400"
                                        >
                                            Cargado por
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-semibold
                                                   text-gray-700"
                                        >
                                            {{ $estudio->subidoPor?->name ?? 'No disponible' }}
                                        </p>
                                    </div>

                                </div>
                            </div>


                            {{-- Acciones --}}
                            <div
    class="flex shrink-0 gap-2
           md:flex-col"
>
    <a
        href="{{ route('estudios.archivo', $estudio) }}"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center justify-center
               rounded-xl bg-[#0D3B7F]
               px-4 py-2 text-sm font-semibold
               text-white transition
               hover:bg-[#082a5d]"
    >
        Ver PDF
    </a>

    <a
        href="{{ route('estudios.descargar', $estudio) }}"
        class="inline-flex items-center justify-center
               rounded-xl border border-gray-300
               bg-white px-4 py-2 text-sm
               font-semibold text-gray-700
               transition hover:bg-gray-50"
    >
        Descargar
    </a>
</div>

                        </div>
                    </div>

                </article>

            @empty

                <div
                    class="rounded-2xl border border-dashed
                           border-gray-300 bg-white px-6 py-14
                           text-center"
                >

                    <div
                        class="mx-auto flex h-12 w-12
                               items-center justify-center
                               rounded-full bg-gray-100
                               text-gray-400"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7
                                   a2 2 0 0 1-2-2V5
                                   a2 2 0 0 1 2-2h5
                                   l5 5v11a2 2 0 0 1-2 2z"
                            />
                        </svg>
                    </div>

                    <h3 class="mt-4 font-bold text-gray-900">
                        Sin estudios registrados
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Este paciente todavía no tiene documentos
                        de estudios asociados a sus citas.
                    </p>

                </div>

            @endforelse


            {{-- Paginación --}}
            @if ($estudios->hasPages())
                <div class="mt-6">
                    {{ $estudios->links() }}
                </div>
            @endif

        </div>
    </div>

</x-app-layout>