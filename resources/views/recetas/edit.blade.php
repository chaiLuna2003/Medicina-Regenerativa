<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-[#0D3B7F]">Expediente clínico</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Editar receta médica
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Actualiza las indicaciones médicas de esta consulta.
                </p>
            </div>

            <a
                href="{{ route('recetas.show', $receta) }}"
                class="inline-flex items-center justify-center rounded-xl border border-gray-300
                       bg-white px-5 py-2.5 text-sm font-semibold text-gray-700
                       transition hover:bg-gray-50"
            >
                Volver a la receta
            </a>
        </div>
    </x-slot>

    @php
        $cita = $receta->cita;
        $paciente = $cita->paciente;
        $medico = $cita->medico;
        $signoVital = $cita->signoVital;
        $nombrePaciente = trim(($paciente?->nombre ?? '').' '.($paciente?->apellido ?? ''));
        $nombreMedico = trim(
            ($medico?->nombre ?? '').' '.
            ($medico?->apellido_paterno ?? '').' '.
            ($medico?->apellido_materno ?? '')
        );

        if ($nombreMedico === '') {
            $nombreMedico = $medico?->user?->name ?? '';
        }
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                    <p class="font-semibold text-red-900">No fue posible actualizar la receta.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="relative overflow-hidden rounded-2xl bg-[#0D3B7F] p-6 text-white shadow-sm">
                <div class="absolute -right-16 -top-16 h-52 w-52 rounded-full bg-blue-300/10"></div>
                <div class="relative grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="flex items-center gap-4 md:col-span-2">
                        <img
                            src="{{ $paciente->fotoUrl() }}"
                            alt="Foto de {{ $nombrePaciente }}"
                            class="h-20 w-20 shrink-0 rounded-2xl border-2 border-white/30 object-cover"
                        >
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-blue-200">Paciente</p>
                            <h3 class="mt-2 text-2xl font-bold">{{ $nombrePaciente }}</h3>
                            <p class="mt-1 text-sm text-blue-100">Edad: {{ $paciente->edad ?: 'No disponible' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-200">Fecha y hora</p>
                        <p class="mt-2 font-semibold">
                            {{ $cita->fecha->locale('es')->translatedFormat('d \d\e F \d\e Y') }}
                        </p>
                        <p class="mt-1 text-sm text-blue-100">
                            {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-200">Médico responsable</p>
                        <p class="mt-2 font-semibold">{{ $nombreMedico !== '' ? 'Dr. '.$nombreMedico : 'No disponible' }}</p>
                        <p class="mt-1 text-sm text-blue-100">
                            {{ $medico?->especialidad ?: 'Especialidad no registrada' }}
                        </p>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-3">
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="text-lg font-bold text-gray-900">Contenido de la receta</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Corrige el tratamiento, dosis, frecuencia, duración o indicaciones.
                        </p>
                    </div>

                    <form
                        id="form-receta"
                        method="POST"
                        action="{{ route('recetas.update', $receta) }}"
                        class="p-6"
                    >
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <label for="contenido" class="text-sm font-semibold text-gray-700">
                                Indicaciones médicas <span class="text-red-500">*</span>
                            </label>
                            <p id="contador-palabras" class="text-sm font-medium text-gray-500" aria-live="polite"></p>
                        </div>

                        <textarea
                            id="contenido"
                            name="contenido"
                            rows="18"
                            maxlength="50000"
                            required
                            autofocus
                            class="mt-3 block w-full resize-y rounded-2xl border-gray-300 text-sm
                                   leading-7 text-gray-800 shadow-sm focus:border-[#0D3B7F]
                                   focus:ring-[#0D3B7F]"
                        >{{ old('contenido', $receta->contenido) }}</textarea>

                        @error('contenido')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="mt-3 flex flex-col gap-2 text-xs text-gray-500 sm:flex-row sm:justify-between">
                            <p>Máximo permitido: 2,000 palabras.</p>
                            <p id="estado-limite"></p>
                        </div>

                        <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end">
                            <a
                                href="{{ route('recetas.show', $receta) }}"
                                class="inline-flex items-center justify-center rounded-xl border border-gray-300
                                       bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                            >
                                Cancelar
                            </a>
                            <button
                                id="boton-guardar"
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-[#0D3B7F]
                                       px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#082a5d]
                                       disabled:cursor-not-allowed disabled:bg-gray-400"
                            >
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Motivo de consulta</p>
                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-700">
                            {{ $cita->motivo ?: 'No se registró un motivo.' }}
                        </p>
                    </section>

                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900">Signos vitales</h3>
                        @if ($signoVital)
                            <dl class="mt-4 space-y-3">
                                <div class="flex justify-between gap-4 rounded-xl bg-blue-50 px-4 py-3">
                                    <dt class="text-xs font-semibold text-blue-600">Presión arterial</dt>
                                    <dd class="text-sm font-bold text-blue-950">
                                        {{ $signoVital->presion_sistolica }}/{{ $signoVital->presion_diastolica }} mmHg
                                    </dd>
                                </div>
                                <div class="flex justify-between gap-4 rounded-xl bg-blue-50 px-4 py-3">
                                    <dt class="text-xs font-semibold text-blue-600">Temperatura</dt>
                                    <dd class="text-sm font-bold text-blue-950">{{ $signoVital->temperatura }} °C</dd>
                                </div>
                                <div class="flex justify-between gap-4 rounded-xl bg-blue-50 px-4 py-3">
                                    <dt class="text-xs font-semibold text-blue-600">Saturación</dt>
                                    <dd class="text-sm font-bold text-blue-950">{{ $signoVital->saturacion_oxigeno }}%</dd>
                                </div>
                            </dl>
                        @else
                            <p class="mt-4 rounded-xl border border-dashed border-amber-300 bg-amber-50 p-4 text-sm text-amber-800">
                                Esta cita no tiene signos vitales registrados.
                            </p>
                        @endif
                    </section>
                </aside>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contenido = document.getElementById('contenido');
            const contador = document.getElementById('contador-palabras');
            const estado = document.getElementById('estado-limite');
            const boton = document.getElementById('boton-guardar');
            const formulario = document.getElementById('form-receta');
            const maximo = 2000;

            const contar = (texto) => {
                const palabras = texto.match(/[\p{L}\p{N}]+(?:['’\-][\p{L}\p{N}]+)*/gu);
                return palabras ? palabras.length : 0;
            };

            const actualizar = () => {
                const total = contar(contenido.value);
                const excedido = total > maximo;

                contador.textContent = `${total.toLocaleString('es-MX')} de 2,000 palabras`;
                contador.classList.toggle('text-red-600', excedido);
                contador.classList.toggle('font-bold', excedido);
                boton.disabled = excedido;

                estado.textContent = excedido
                    ? `Elimina ${total - maximo} palabra(s) para continuar.`
                    : `Puedes agregar ${maximo - total} palabra(s) más.`;
                estado.classList.toggle('text-red-600', excedido);
                estado.classList.toggle('font-semibold', excedido);
            };

            contenido.addEventListener('input', actualizar);
            formulario.addEventListener('submit', (event) => {
                if (contar(contenido.value) > maximo) {
                    event.preventDefault();
                    contenido.focus();
                    actualizar();
                }
            });

            actualizar();
        });
    </script>
</x-app-layout>