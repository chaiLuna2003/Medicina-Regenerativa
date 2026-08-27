@if (
    request()->user()->isAdmin()
    || request()->user()->isRecepcionista()
)

    <div
        id="modal-estudios-paciente"
        class="fixed inset-0 z-50 hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-estudios-paciente">

        {{-- Fondo --}}
        <div
            class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
            onclick="cerrarModalEstudiosPaciente()">
        </div>

        {{-- Contenedor --}}
        <div class="relative flex min-h-full items-center justify-center p-3 sm:p-4">

            <div
                class="relative flex max-h-[90vh] w-full max-w-[650px]
                       flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

                {{-- Encabezado --}}
                <div
                    class="flex shrink-0 items-start justify-between
                           border-b border-gray-200 px-5 py-4">

                    <div>
                        <p
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-[#0D3B7F]">
                            Expediente clínico
                        </p>

                        <h2
                            id="titulo-modal-estudios-paciente"
                            class="mt-1 text-xl font-bold text-gray-900">
                            Agregar estudios
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $pacientes->nombre }}
                            {{ $pacientes->apellido }}
                        </p>
                    </div>

                    <button
                        type="button"
                        onclick="cerrarModalEstudiosPaciente()"
                        aria-label="Cerrar modal"
                        class="rounded-lg p-2 text-gray-400 transition
                               hover:bg-gray-100 hover:text-gray-700">
                        ✕
                    </button>
                </div>

                <form
                    action="{{ route('pacientes.estudios.store', $pacientes) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="flex min-h-0 flex-1 flex-col">

                    @csrf

                    {{-- Contenido --}}
                    <div class="min-h-0 flex-1 overflow-y-auto p-5">

                        <div class="space-y-5">

                            {{-- Paciente --}}
                            <div
                                class="rounded-xl border border-blue-100
                                       bg-blue-50 p-4">

                                <p
                                    class="text-[11px] font-semibold uppercase
                                           tracking-wide text-blue-500">
                                    Paciente
                                </p>

                                <p class="mt-1 font-bold text-blue-950">
                                    {{ $pacientes->nombre }}
                                    {{ $pacientes->apellido }}
                                </p>

                                <p class="mt-1 text-xs text-blue-700">
                                    El estudio se agregará a su expediente clínico.
                                </p>
                            </div>

                            {{-- Cita relacionada --}}
                            <div>
                                <label
                                    for="cita-estudio-paciente"
                                    class="block text-sm font-semibold text-gray-700">
                                    Cita relacionada
                                </label>

                                <select
                                    id="cita-estudio-paciente"
                                    name="cita_id"
                                    required
                                    class="mt-1.5 block w-full rounded-xl
                                           border-gray-300 px-3 py-2.5
                                           text-sm shadow-sm
                                           focus:border-[#0D3B7F]
                                           focus:ring-[#0D3B7F]">

                                    <option value="">
                                        Selecciona una cita
                                    </option>

                                    @foreach ($pacientes->citas as $cita)
                                        <option
                                            value="{{ $cita->id }}"
                                            @selected(
                                                (string) old('cita_id')
                                                === (string) $cita->id
                                            )>

                                            {{ $cita->fecha?->format('d/m/Y') ?? 'Sin fecha' }}

                                            @if ($cita->hora)
                                                ·
                                                {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                            @endif

                                            @if ($cita->medico)
                                                · Dr.
                                                {{ $cita->medico->nombre }}
                                                {{ $cita->medico->apellido_paterno }}
                                            @endif

                                            · {{ ucfirst($cita->estado) }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('cita_id')
                                    <p class="mt-1 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                                @if ($pacientes->citas->isEmpty())
                                    <p class="mt-2 text-xs font-medium text-amber-700">
                                        Este paciente todavía no tiene citas registradas.
                                        Debes crear una cita antes de cargar estudios.
                                    </p>
                                @else
                                    <p class="mt-1 text-xs text-gray-500">
                                        Selecciona la consulta con la que se relaciona
                                        el documento.
                                    </p>
                                @endif
                            </div>

                            {{-- Nombre --}}
                            <div>
                                <label
                                    for="nombre-estudio-paciente"
                                    class="block text-sm font-semibold text-gray-700">
                                    Nombre del estudio
                                </label>

                                <input
                                    id="nombre-estudio-paciente"
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

                            {{-- Fecha --}}
                            <div>
                                <label
                                    for="fecha-estudio-paciente"
                                    class="block text-sm font-semibold text-gray-700">
                                    Fecha del estudio
                                </label>

                                <input
                                    id="fecha-estudio-paciente"
                                    type="date"
                                    name="fecha_estudio"
                                    value="{{ old(
                                        'fecha_estudio',
                                        now()->format('Y-m-d')
                                    ) }}"
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

                            {{-- Descripción --}}
                            <div>
                                <label
                                    for="descripcion-estudio-paciente"
                                    class="block text-sm font-semibold text-gray-700">
                                    Descripción

                                    <span class="font-normal text-gray-400">
                                        (opcional)
                                    </span>
                                </label>

                                <textarea
                                    id="descripcion-estudio-paciente"
                                    name="descripcion"
                                    rows="3"
                                    maxlength="1000"
                                    placeholder="Detalles del estudio..."
                                    class="mt-1.5 block w-full resize-none
                                           rounded-xl border-gray-300
                                           px-3 py-2.5 text-sm shadow-sm
                                           focus:border-[#0D3B7F]
                                           focus:ring-[#0D3B7F]">{{ old('descripcion') }}</textarea>

                                @error('descripcion')
                                    <p class="mt-1 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Archivos --}}
                            <div>
                                <label
                                    for="archivos-estudios-paciente"
                                    class="block text-sm font-semibold text-gray-700">
                                    Archivos PDF
                                </label>

                                <div
                                    class="mt-1.5 rounded-xl border-2
                                           border-dashed border-gray-300
                                           bg-gray-50 p-4 text-center">

                                    <input
                                        id="archivos-estudios-paciente"
                                        type="file"
                                        name="archivos[]"
                                        accept="application/pdf,.pdf"
                                        multiple
                                        required
                                        onchange="mostrarArchivosEstudiosPaciente(this)"
                                        class="block w-full text-xs text-gray-600
                                               file:mr-3 file:rounded-lg
                                               file:border-0
                                               file:bg-[#0D3B7F]
                                               file:px-3 file:py-2
                                               file:text-xs file:font-semibold
                                               file:text-white">

                                    <p class="mt-2 text-[11px] text-gray-500">
                                        Hasta 10 PDF · Máximo 15 MB por archivo
                                    </p>
                                </div>

                                <div
                                    id="lista-archivos-estudios-paciente"
                                    class="mt-2 max-h-28 space-y-2 overflow-y-auto">
                                </div>

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

                    {{-- Acciones --}}
                    <div
                        class="flex shrink-0 flex-col-reverse gap-2
                               border-t border-gray-200 bg-gray-50
                               px-5 py-3 sm:flex-row sm:justify-end">

                        <button
                            type="button"
                            onclick="cerrarModalEstudiosPaciente()"
                            class="rounded-xl border border-gray-300
                                   bg-white px-4 py-2 text-sm font-semibold
                                   text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            @disabled($pacientes->citas->isEmpty())
                            class="rounded-xl bg-[#0D3B7F]
                                   px-4 py-2 text-sm font-semibold
                                   text-white transition
                                   hover:bg-[#082a5d]
                                   disabled:cursor-not-allowed
                                   disabled:bg-gray-300">
                            Guardar estudios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalEstudiosPaciente() {
            const modal = document.getElementById(
                'modal-estudios-paciente'
            );

            if (!modal) return;

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function cerrarModalEstudiosPaciente() {
            const modal = document.getElementById(
                'modal-estudios-paciente'
            );

            if (!modal) return;

            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function mostrarArchivosEstudiosPaciente(input) {
            const contenedor = document.getElementById(
                'lista-archivos-estudios-paciente'
            );

            if (!contenedor) return;

            contenedor.innerHTML = '';

            Array.from(input.files).forEach((archivo) => {
                const elemento = document.createElement('div');

                elemento.className =
                    'flex items-center justify-between ' +
                    'rounded-lg border border-gray-200 ' +
                    'bg-white px-3 py-2 text-sm';

                const nombre = document.createElement('span');

                nombre.className =
                    'truncate font-medium text-gray-700';

                nombre.textContent = archivo.name;

                const peso = document.createElement('span');

                peso.className =
                    'ml-3 shrink-0 text-xs text-gray-400';

                peso.textContent =
                    (archivo.size / 1024 / 1024).toFixed(2)
                    + ' MB';

                elemento.appendChild(nombre);
                elemento.appendChild(peso);

                contenedor.appendChild(elemento);
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                cerrarModalEstudiosPaciente();
            }
        });
    </script>

    @if (
        $errors->has('cita_id')
        || $errors->has('archivos')
        || $errors->has('archivos.*')
    )
        <script>
            document.addEventListener(
                'DOMContentLoaded',
                abrirModalEstudiosPaciente
            );
        </script>
    @endif

@endif