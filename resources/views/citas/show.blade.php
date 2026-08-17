<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-[#0D3B7F]">
                    Detalle de la cita
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    {{ $cita->paciente?->nombre }}
                    {{ $cita->paciente?->apellido }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Consulta la información completa de la cita.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                {{-- Volver --}}
                <a
                    href="{{ route('citas.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border
               border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold
               text-gray-700 transition hover:bg-gray-50">
                    Volver a la agendas
                </a>

                {{-- Acciones exclusivas de administración y recepción --}}
                @if (in_array(auth()->user()->role, ['admin', 'recepcionista'], true))
                <a
                    href="{{ route('citas.edit', $cita) }}"
                    class="inline-flex items-center justify-center rounded-xl
                               bg-[#0D3B7F] px-5 py-2.5 text-sm font-semibold
                               text-white transition hover:bg-[#082a5d]">
                    Editar cita
                </a>

                <button
                    type="button"
                    onclick="abrirModalEstudios()"
                    class="inline-flex items-center justify-center rounded-xl
                   bg-emerald-600 px-5 py-2.5 text-sm font-semibold
                   text-white transition hover:bg-emerald-700">
                    Agregar estudios
                </button>

                @endif
                @if (auth()->user()->role === 'medico')
    @if ($cita->receta)
        <a
            href="{{ route('recetas.show', $cita->receta) }}"
            class="inline-flex w-full items-center justify-center rounded-xl
                   border border-[#0D3B7F] bg-white px-4 py-2.5
                   text-sm font-semibold text-[#0D3B7F]
                   transition hover:bg-[#0D3B7F] hover:text-white"
        >
            Ver receta
        </a>
    @else
        <a
            href="{{ route('citas.receta.create', $cita) }}"
            class="inline-flex w-full items-center justify-center rounded-xl
                   bg-[#0D3B7F] px-4 py-2.5
                   text-sm font-semibold text-white
                   transition hover:bg-[#082a5d]"
        >
            Crear receta
        </a>
    @endif
@endif
            </div>

        </div>
    </x-slot>

    @php
        $usuario = auth()->user();
        $esAdministrador = $usuario->role === 'admin';
        $esMedico = $usuario->role === 'medico';

        /*
         * Comprueba que el usuario autenticado sea el usuario
         * asociado con el médico asignado a esta cita.
         */
        $esMedicoResponsable = $esMedico
            && (int) $cita->medico?->user_id === (int) $usuario->id;

        $receta = $cita->receta;

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

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Información principal --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200
                            bg-white shadow-sm lg:col-span-2">

                    <div class="border-b border-gray-200 px-6 py-5">
                        <div class="flex flex-col gap-3 sm:flex-row
                                    sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    Información de la cita
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Fecha, horario y motivo de atención.
                                </p>
                            </div>

                            <span class="inline-flex w-fit rounded-full px-3 py-1
                                         text-xs font-semibold {{ $estadoClases }}">
                                {{ $estadoTexto }}
                            </span>
                        </div>
                    </div>

                    <div class="grid gap-6 p-6 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Fecha
                            </p>

                            <p class="mt-2 font-semibold text-gray-900">
                                {{ $cita->fecha->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Hora
                            </p>

                            <p class="mt-2 font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Motivo
                            </p>

                            <p class="mt-2 text-gray-700">
                                {{ $cita->motivo }}
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Notas adicionales
                            </p>

                            <p class="mt-2 whitespace-pre-line text-gray-700">
                                {{ $cita->notas ?: 'No se agregaron notas para esta cita.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Signos vitales --}}
                    <div class="border-t border-gray-200 px-6 py-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                Signos vitales
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Datos registrados por el personal de enfermería.
                            </p>
                        </div>

                        @if ($cita->signoVital)
                            @php
                                $signoVital = $cita->signoVital;

                                $pastillasSignosVitales = [
                                    [
                                        'Peso',
                                        number_format((float) $signoVital->peso, 2).' kg'
                                    ],
                                    [
                                        'Estatura',
                                        number_format((float) $signoVital->estatura, 2).' cm'
                                    ],
                                    [
                                        'IMC',
                                        $signoVital->imc !== null
                                            ? number_format((float) $signoVital->imc, 2)
                                            : 'No disponible'
                                    ],
                                    [
                                        'Temperatura',
                                        $signoVital->temperatura !== null
                                            ? number_format(
                                                (float) $signoVital->temperatura,
                                                1
                                            ).' °C'
                                            : 'No disponible'
                                    ],
                                    [
                                        'Presión arterial',
                                        $signoVital->presion_sistolica !== null
                                        && $signoVital->presion_diastolica !== null
                                            ? $signoVital->presion_sistolica.'/'.
                                              $signoVital->presion_diastolica.' mmHg'
                                            : 'No disponible'
                                    ],
                                    [
                                        'Frecuencia cardiaca',
                                        $signoVital->frecuencia_cardiaca !== null
                                            ? $signoVital->frecuencia_cardiaca.' lpm'
                                            : 'No disponible'
                                    ],
                                    [
                                        'Frecuencia respiratoria',
                                        $signoVital->frecuencia_respiratoria !== null
                                            ? $signoVital->frecuencia_respiratoria.' rpm'
                                            : 'No disponible'
                                    ],
                                    [
                                        'Saturación de oxígeno',
                                        $signoVital->saturacion_oxigeno !== null
                                            ? $signoVital->saturacion_oxigeno.'%'
                                            : 'No disponible'
                                    ],
                                    [
                                        'Glucosa',
                                        $signoVital->glucosa !== null
                                            ? number_format(
                                                (float) $signoVital->glucosa,
                                                2
                                            ).' mg/dL'
                                            : 'No disponible'
                                    ],
                                ];
                            @endphp

                            <div class="mt-5 flex flex-wrap gap-3">
                                @foreach ($pastillasSignosVitales as [$etiqueta, $valor])
                                    <div class="inline-flex items-center gap-2 rounded-full
                                                border border-blue-200 bg-blue-50 px-4 py-2
                                                text-sm text-blue-900">
                                        <span class="font-medium text-blue-600">
                                            {{ $etiqueta }}
                                        </span>

                                        <span class="font-bold">
                                            {{ $valor }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if ($signoVital->observaciones)
                                <div class="mt-5 rounded-xl border border-blue-100
                                            bg-blue-50/60 p-4">
                                    <p class="text-xs font-semibold uppercase
                                              tracking-wide text-blue-600">
                                        Observaciones de enfermería
                                    </p>

                                    <p class="mt-2 whitespace-pre-line text-sm text-blue-950">
                                        {{ $signoVital->observaciones }}
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="mt-5 rounded-xl border border-dashed
                                        border-blue-200 bg-blue-50 px-4 py-4
                                        text-sm font-medium text-blue-700">
                                Enfermería todavía no ha registrado signos vitales
                                para esta cita.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Columna lateral --}}
                <div class="space-y-6">
                    <div>
                        @if ($cita->paciente)
                        @if ($cita->paciente)
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <div class="flex items-center gap-4">
            {{-- Foto: visible para médico y administrador --}}
            <img
                src="{{ $cita->paciente->fotoUrl() }}"
                alt="Foto de {{ $cita->paciente->nombre }}"
                class="h-16 w-16 shrink-0 rounded-full border-2 border-blue-100 object-cover shadow-sm"
            >

            <div class="min-w-0">
                {{-- Nombre: visible para médico y administrador --}}
                <h3 class="text-lg font-bold text-gray-900">
                    {{ $cita->paciente->nombre }}
                    {{ $cita->paciente->apellido }}
                </h3>

                {{-- Edad: visible para médico y administrador --}}
                <p class="mt-1 text-sm text-gray-600">
                    <span class="font-semibold">Edad:</span>
                    {{ $cita->paciente->edad }}
                </p>
            </div>
        </div>

        {{-- Información completa: solamente administrador --}}
        @if (auth()->user()->role === 'admin')
            <div class="mt-5 grid gap-4 border-t border-gray-100 pt-5 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Teléfono
                    </p>
                    <p class="mt-1 text-sm font-medium text-gray-700">
                        {{ $cita->paciente->telefono ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Correo electrónico
                    </p>
                    <p class="mt-1 break-all text-sm font-medium text-gray-700">
                        {{ $cita->paciente->email ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Fecha de nacimiento
                    </p>
                    <p class="mt-1 text-sm font-medium text-gray-700">
                        {{ $cita->paciente->fecha_nacimiento?->format('d/m/Y') ?? 'No registrada' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Estado
                    </p>
                    <p class="mt-1 text-sm font-medium text-gray-700">
                        {{ $cita->paciente->status ? 'Activo' : 'Inactivo' }}
                    </p>
                </div>
            </div>
        @endif
    </div>
@else
    <div class="rounded-xl bg-gray-50 p-4">
        <p class="text-sm text-gray-500">Paciente no disponible.</p>
    </div>
@endif
                        @else
                        <p class="text-sm text-gray-500">
                            Paciente no disponible
                        </p>
                        @endif
                    </div>

                                    <a
                                        href="{{ route('citas.receta.create', $cita) }}"
                                        class="inline-flex w-full items-center justify-center
                                               gap-2 rounded-xl bg-[#0D3B7F] px-4 py-3
                                               text-sm font-semibold text-white transition
                                               hover:bg-[#082a5d]"
                                    >
                                        Elaborar receta
                                    </a>
                                @else
                                    <div class="rounded-xl border border-dashed
                                                border-gray-300 bg-gray-50 p-4">
                                        <p class="text-sm text-gray-600">
                                            Esta cita todavía no tiene una receta registrada.
                                        </p>
                                    </div>
                                @endif

                                {{-- Administrador o médico relacionado --}}
                                <a
                                    href="{{ route(
                                        'pacientes.recetas.index',
                                        $cita->paciente
                                    ) }}"
                                    class="inline-flex w-full items-center justify-center
                                           gap-2 rounded-xl border border-gray-300 bg-white
                                           px-4 py-3 text-sm font-semibold text-gray-700
                                           transition hover:bg-gray-50"
                                >
                                    Ver historial de recetas
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Médico --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Médico asignado
                        </p>

                        <h3 class="mt-2 text-lg font-bold text-gray-900">
                            Dr. {{ $cita->medico?->nombre }}
                            {{ $cita->medico?->apellido_paterno }}
                        </h3>

                        <p class="mt-2 text-sm text-gray-600">
                            {{ $cita->medico?->especialidad ?: 'Sin especialidad registrada' }}
                        </p>
                    </div>

                    {{-- Usuario que registró la cita --}}
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Registrada por
                        </p>

                        <p class="mt-2 font-semibold text-gray-900">
                            {{ $cita->creadoPor?->name ?: 'Usuario no disponible' }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $cita->created_at->format('d/m/Y, h:i A') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
</x-app-layout>

<script>
    const modalEstudios = document.getElementById('modal-estudios');

    function abrirModalEstudios() {
        if (!modalEstudios) return;

        modalEstudios.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModalEstudios() {
        if (!modalEstudios) return;

        modalEstudios.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function mostrarArchivosSeleccionados(input) {
        const contenedor = document.getElementById(
            'lista-archivos-estudios'
        );

        if (!contenedor) return;

        contenedor.innerHTML = '';

        Array.from(input.files).forEach((archivo) => {
            const elemento = document.createElement('div');

            const megabytes =
                (archivo.size / 1024 / 1024).toFixed(2);

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

            peso.textContent = megabytes + ' MB';

            elemento.appendChild(nombre);
            elemento.appendChild(peso);

            contenedor.appendChild(elemento);
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalEstudios();
        }
    });
</script>
@if (
$errors->has('nombre')
|| $errors->has('descripcion')
|| $errors->has('fecha_estudio')
|| $errors->has('archivos')
|| $errors->has('archivos.*')
)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        abrirModalEstudios();
    });
</script>
@endif