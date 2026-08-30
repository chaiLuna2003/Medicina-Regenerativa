 @if (in_array(auth()->user()->role, ['admin', 'recepcionista'], true))
    <div
        id="modal-estudios"
        class="fixed inset-0 z-50 hidden"
        role="dialog"
        aria-modal="true">
        {{-- Fondo --}}
        <div
            class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
            onclick="cerrarModalEstudios()"></div>

        {{-- Centrado --}}
        <div class="relative flex min-h-full items-center justify-center p-3 sm:p-4">

            {{-- Modal --}}
            <div
                class="relative flex max-h-[90vh] w-full max-w-[600px]
                   flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

                {{-- Encabezado --}}
                <div class="flex shrink-0 items-start justify-between border-b border-gray-200 px-5 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#0D3B7F]">
                            Expediente clínico
                        </p>

                        <h2 class="mt-1 text-xl font-bold text-gray-900">
                            Agregar estudios
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $cita->paciente->nombre }}
                            {{ $cita->paciente->apellido }}
                        </p>
                    </div>

                    <button
                        type="button"
                        onclick="cerrarModalEstudios()"
                        class="rounded-lg p-2 text-gray-400 transition
                           hover:bg-gray-100 hover:text-gray-700">
                        ✕
                    </button>
                </div>

                <form
                    action="{{ route('estudios.store', $cita) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="flex min-h-0 flex-1 flex-col">
                    @csrf

                    {{-- Contenido con scroll --}}
                    <div class="min-h-0 flex-1 overflow-y-auto p-5">

                        <div class="grid gap-5 md:grid-cols-[190px_1fr]">

                            {{-- COLUMNA IZQUIERDA --}}
                            <div class="space-y-4">

                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">
                                        Información de la cita
                                    </h3>

                                    <p class="mt-1 text-xs text-gray-500">
                                        Datos asociados al estudio.
                                    </p>
                                </div>

                                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">

                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-blue-500">
                                            Paciente
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-blue-950">
                                            {{ $cita->paciente->nombre }}
                                            {{ $cita->paciente->apellido }}
                                        </p>
                                    </div>

                                    <div class="mt-4 border-t border-blue-100 pt-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-blue-500">
                                            Cita
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-blue-950">
                                            {{ $cita->fecha->format('d/m/Y') }}
                                        </p>

                                        <p class="mt-1 text-sm text-blue-900">
                                            {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                        </p>
                                    </div>

                                    <div class="mt-4 border-t border-blue-100 pt-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-blue-500">
                                            Médico
                                        </p>

                                        <p class="mt-1 text-sm font-semibold text-blue-950">
                                            Dr.
                                            {{ $cita->medico?->nombre }}
                                            {{ $cita->medico?->apellido_paterno }}
                                        </p>

                                        <p class="mt-1 text-xs text-blue-700">
                                            {{ $cita->medico?->especialidad ?? 'Sin especialidad registrada' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-amber-100 bg-amber-50 p-3">
                                    <p class="text-xs leading-relaxed text-amber-800">
                                        Los documentos quedarán asociados a esta cita y al historial del paciente.
                                    </p>
                                </div>
                            </div>

                            {{-- COLUMNA DERECHA --}}
                            <div class="space-y-4">

                                {{-- Nombre --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">
                                        Nombre del estudio
                                    </label>

                                    <input
                                        type="text"
                                        name="nombre"
                                        value="{{ old('nombre') }}"
                                        required
                                        maxlength="150"
                                        placeholder="Ej. Resonancia magnética"
                                        class="mt-1.5 block w-full rounded-xl
                                           border-gray-300 px-3 py-2.5
                                           text-sm shadow-sm
                                           focus:border-[#0D3B7F]
                                           focus:ring-[#0D3B7F]">

                                    @error('nombre')
                                    <p class="mt-1 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                {{-- Fecha y descripción --}}
                                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-1">

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Fecha del estudio
                                        </label>

                                        <input
                                            type="date"
                                            name="fecha_estudio"
                                            value="{{ old('fecha_estudio', now()->format('Y-m-d')) }}"
                                            max="{{ now()->format('Y-m-d') }}"
                                            required
                                            class="mt-1.5 block w-full rounded-xl
                                               border-gray-300 px-3 py-2.5
                                               text-sm shadow-sm
                                               focus:border-[#0D3B7F]
                                               focus:ring-[#0D3B7F]">

                                        @error('fecha_estudio')
                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Descripción
                                            <span class="font-normal text-gray-400">
                                                (opcional)
                                            </span>
                                        </label>

                                        <textarea
                                            name="descripcion"
                                            rows="2"
                                            maxlength="1000"
                                            placeholder="Detalles del estudio..."
                                            class="mt-1.5 block w-full resize-none rounded-xl
                                               border-gray-300 px-3 py-2.5
                                               text-sm shadow-sm
                                               focus:border-[#0D3B7F]
                                               focus:ring-[#0D3B7F]">{{ old('descripcion') }}</textarea>

                                        @error('descripcion')
                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Archivos --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">
                                        Archivos PDF
                                    </label>

                                    <div
                                        class="mt-1.5 rounded-xl border-2 border-dashed
                                           border-gray-300 bg-gray-50 p-4 text-center">
                                        <input
                                            id="archivos-estudios"
                                            type="file"
                                            name="archivos[]"
                                            accept="application/pdf,.pdf"
                                            multiple
                                            required
                                            onchange="mostrarArchivosSeleccionados(this)"
                                            class="block w-full text-xs text-gray-600
                                               file:mr-3 file:rounded-lg
                                               file:border-0
                                               file:bg-[#0D3B7F]
                                               file:px-3 file:py-2
                                               file:text-xs file:font-semibold
                                               file:text-white">

                                        <p class="mt-2 text-[11px] text-gray-500">
                                            Hasta 10 PDFs · Máximo 15 MB por archivo
                                        </p>
                                    </div>

                                    <div
                                        id="lista-archivos-estudios"
                                        class="mt-2 max-h-24 space-y-2 overflow-y-auto"></div>

                                    @error('archivos')
                                    <p class="mt-1 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror

                                    @error('archivos.*')
                                    <p class="mt-1 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex shrink-0 flex-col-reverse gap-2 border-t
                            border-gray-200 bg-gray-50 px-5 py-3
                            sm:flex-row sm:justify-end">

                        <button
                            type="button"
                            onclick="cerrarModalEstudios()"
                            class="rounded-xl border border-gray-300 bg-white
                               px-4 py-2 text-sm font-semibold
                               text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="rounded-xl bg-[#0D3B7F]
                               px-4 py-2 text-sm font-semibold
                               text-white transition hover:bg-[#082a5d]">
                            Guardar estudios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif