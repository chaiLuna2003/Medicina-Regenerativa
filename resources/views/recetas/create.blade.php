<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-[#0D3B7F]">
                    Expediente clínico
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Elaborar receta médica
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Registra las indicaciones médicas correspondientes a esta consulta.
                </p>
            </div>

            <a
                href="{{ route('citas.show', $cita) }}"
                class="inline-flex items-center justify-center rounded-xl border
                       border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold
                       text-gray-700 transition hover:bg-gray-50"
            >
                Volver a la cita
            </a>
        </div>
    </x-slot>

    @php
        $paciente = $cita->paciente;
        $medico = $cita->medico;
        $signoVital = $cita->signoVital;

        /*
         * Admite tanto el campo apellido como los campos
         * apellido_paterno y apellido_materno.
         */
        $nombrePaciente = trim(
            ($paciente?->nombre ?? '') . ' ' .
            ($paciente?->apellido_paterno ?? $paciente?->apellido ?? '') . ' ' .
            ($paciente?->apellido_materno ?? '')
        );

        $nombreMedico = trim(
            ($medico?->nombre ?? '') . ' ' .
            ($medico?->apellido_paterno ?? '') . ' ' .
            ($medico?->apellido_materno ?? '')
        );

        /*
         * Si el perfil médico obtiene su nombre desde User,
         * se utiliza como alternativa.
         */
        if ($nombreMedico === '') {
            $nombreMedico = $medico?->user?->name ?? '';
        }

        $fechaCita = $cita->fecha
            ? \Carbon\Carbon::parse($cita->fecha)
                ->locale('es')
                ->translatedFormat('d \d\e F \d\e Y')
            : 'No disponible';

        $horaCita = $cita->hora
            ? \Carbon\Carbon::parse($cita->hora)->format('h:i A')
            : 'No disponible';
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Errores generales --}}
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                    <div class="flex items-start gap-3">
                        <div class="rounded-full bg-red-100 p-2 text-red-700">
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
                                    d="M12 9v3m0 4h.01M10.29 3.86l-8.82
                                       15.28A2 2 0 003.2 22h17.6a2 2 0
                                       001.73-2.86L13.71 3.86a2 2 0
                                       00-3.42 0z"
                                />
                            </svg>
                        </div>

                        <div>
                            <p class="font-semibold text-red-900">
                                No fue posible guardar la receta
                            </p>

                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Resumen de la consulta --}}
            <section class="relative overflow-hidden rounded-2xl bg-[#0D3B7F]
                            p-6 text-white shadow-sm">
                <div class="absolute -right-16 -top-16 h-52 w-52
                            rounded-full bg-blue-300/10"></div>

                <div class="absolute -bottom-20 right-32 h-48 w-48
                            rounded-full bg-emerald-300/10"></div>

                <div class="relative grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="flex items-center gap-4 md:col-span-2">
                        @if ($paciente)
                            <img
                                src="{{ $paciente->fotoUrl() }}"
                                alt="Foto de {{ $nombrePaciente }}"
                                class="h-20 w-20 shrink-0 rounded-2xl border-2
                                       border-white/30 object-cover shadow-sm"
                            >
                        @endif

                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-blue-200">
                                Paciente
                            </p>

                            <h3 class="mt-2 text-2xl font-bold">
                                {{ $nombrePaciente ?: 'Paciente no disponible' }}
                            </h3>

                            <p class="mt-1 text-sm text-blue-100">
                                Edad: {{ $paciente?->edad ?? 'No disponible' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-200">
                            Fecha y hora
                        </p>

                        <p class="mt-2 font-semibold">
                            {{ $fechaCita }}
                        </p>

                        <p class="mt-1 text-sm text-blue-100">
                            {{ $horaCita }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-200">
                            Médico responsable
                        </p>

                        <p class="mt-2 font-semibold">
                            {{ $nombreMedico !== '' ? 'Dr. '.$nombreMedico : 'No disponible' }}
                        </p>

                        <p class="mt-1 text-sm text-blue-100">
                            {{ $medico?->especialidad ?: 'Especialidad no registrada' }}
                        </p>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Formulario de receta --}}
                <section class="overflow-hidden rounded-2xl border border-gray-200
                                bg-white shadow-sm lg:col-span-2">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="text-lg font-bold text-gray-900">
                            Contenido de la receta
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Escribe el tratamiento, dosis, frecuencia, duración e
                            indicaciones para el paciente.
                        </p>
                    </div>

                    <form
                        id="form-receta"
                        method="POST"
                        action="{{ route('citas.receta.store', $cita) }}"
                        class="p-6"
                    >
                        @csrf

                        <div>
                            <div class="flex flex-col gap-2 sm:flex-row
                                        sm:items-center sm:justify-between">
                                <label
                                    for="contenido"
                                    class="text-sm font-semibold text-gray-700"
                                >
                                    Indicaciones médicas
                                    <span class="text-red-500">*</span>
                                </label>

                                <p
                                    id="contador-palabras"
                                    class="text-sm font-medium text-gray-500"
                                    aria-live="polite"
                                >
                                    0 de 2,000 palabras
                                </p>
                            </div>

                            <textarea
                                id="contenido"
                                name="contenido"
                                rows="18"
                                maxlength="50000"
                                required
                                autofocus
                                placeholder="Ejemplo:

Medicamento:
Dosis:
Vía de administración:
Frecuencia:
Duración:
Indicaciones adicionales:"
                                class="mt-3 block w-full resize-y rounded-2xl
                                       border-gray-300 text-sm leading-7 text-gray-800
                                       shadow-sm transition
                                       focus:border-[#0D3B7F]
                                       focus:ring-[#0D3B7F]"
                            >{{ old('contenido') }}</textarea>

                            @error('contenido')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <div class="mt-3 flex flex-col gap-2 text-xs text-gray-500
                                        sm:flex-row sm:items-center
                                        sm:justify-between">
                                <p>
                                    Máximo permitido: 2,000 palabras.
                                </p>

                                <p id="estado-limite">
                                    El contenido se validará antes de guardarse.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 rounded-xl border border-amber-200
                                    bg-amber-50 p-4">
                            <p class="text-sm font-semibold text-amber-900">
                                Antes de guardar
                            </p>

                            <p class="mt-1 text-sm leading-6 text-amber-800">
                                Verifica el paciente, medicamentos, dosis y duración.
                                La receta quedará vinculada permanentemente con esta cita.
                            </p>
                        </div>

                        <div class="mt-6 flex flex-col-reverse gap-3 border-t
                                    border-gray-100 pt-6 sm:flex-row
                                    sm:items-center sm:justify-end">
                            <a
                                href="{{ route('citas.show', $cita) }}"
                                class="inline-flex items-center justify-center rounded-xl
                                       border border-gray-300 bg-white px-5 py-3
                                       text-sm font-semibold text-gray-700
                                       transition hover:bg-gray-50"
                            >
                                Cancelar
                            </a>

                            <button
                                id="boton-guardar"
                                type="submit"
                                class="inline-flex items-center justify-center gap-2
                                       rounded-xl bg-[#0D3B7F] px-6 py-3
                                       text-sm font-semibold text-white shadow-sm
                                       transition hover:bg-[#082a5d]
                                       disabled:cursor-not-allowed
                                       disabled:bg-gray-400"
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
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>

                                Guardar receta
                            </button>
                        </div>
                    </form>
                </section>

                {{-- Información clínica lateral --}}
                <aside class="space-y-6">

                    {{-- Motivo --}}
                    <section class="rounded-2xl border border-gray-200
                                    bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Motivo de consulta
                        </p>

                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-700">
                            {{ $cita->motivo ?: 'No se registró un motivo.' }}
                        </p>

                        @if ($cita->notas)
                            <div class="mt-5 border-t border-gray-100 pt-5">
                                <p class="text-xs font-semibold uppercase
                                          tracking-wide text-gray-400">
                                    Notas de la cita
                                </p>

                                <p class="mt-2 whitespace-pre-line text-sm
                                          leading-6 text-gray-700">
                                    {{ $cita->notas }}
                                </p>
                            </div>
                        @endif
                    </section>

                    {{-- Signos vitales --}}
                    <section class="rounded-2xl border border-gray-200
                                    bg-white p-6 shadow-sm">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                Signos vitales
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Valoración realizada por enfermería.
                            </p>
                        </div>

                        @if ($signoVital)
                            @php
                                $signos = [
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
                                            : 'No registrada'
                                    ],
                                    [
                                        'Presión arterial',
                                        $signoVital->presion_sistolica !== null &&
                                        $signoVital->presion_diastolica !== null
                                            ? $signoVital->presion_sistolica.'/'.
                                              $signoVital->presion_diastolica.' mmHg'
                                            : 'No registrada'
                                    ],
                                    [
                                        'Frecuencia cardiaca',
                                        $signoVital->frecuencia_cardiaca !== null
                                            ? $signoVital->frecuencia_cardiaca.' lpm'
                                            : 'No registrada'
                                    ],
                                    [
                                        'Saturación de oxígeno',
                                        $signoVital->saturacion_oxigeno !== null
                                            ? $signoVital->saturacion_oxigeno.'%'
                                            : 'No registrada'
                                    ],
                                    [
                                        'Glucosa',
                                        $signoVital->glucosa !== null
                                            ? number_format(
                                                (float) $signoVital->glucosa,
                                                2
                                            ).' mg/dL'
                                            : 'No registrada'
                                    ],
                                ];
                            @endphp

                            <dl class="mt-5 space-y-3">
                                @foreach ($signos as [$etiqueta, $valor])
                                    <div class="flex items-center justify-between
                                                gap-4 rounded-xl bg-blue-50 px-4 py-3">
                                        <dt class="text-xs font-semibold text-blue-600">
                                            {{ $etiqueta }}
                                        </dt>

                                        <dd class="text-right text-sm font-bold text-blue-950">
                                            {{ $valor }}
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>

                            @if ($signoVital->observaciones)
                                <div class="mt-4 rounded-xl border border-blue-100
                                            bg-blue-50/60 p-4">
                                    <p class="text-xs font-semibold uppercase
                                              tracking-wide text-blue-600">
                                        Observaciones
                                    </p>

                                    <p class="mt-2 whitespace-pre-line text-sm
                                              leading-6 text-blue-950">
                                        {{ $signoVital->observaciones }}
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="mt-5 rounded-xl border border-dashed
                                        border-amber-300 bg-amber-50 p-4">
                                <p class="text-sm font-semibold text-amber-900">
                                    Sin signos vitales registrados
                                </p>

                                <p class="mt-1 text-sm leading-6 text-amber-700">
                                    Enfermería todavía no ha capturado la valoración
                                    correspondiente a esta cita.
                                </p>
                            </div>
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
            const estadoLimite = document.getElementById('estado-limite');
            const botonGuardar = document.getElementById('boton-guardar');
            const form = document.getElementById('form-receta');

            const maximoPalabras = 2000;

            function contarPalabras(texto) {
                const coincidencias = texto.match(
                    /[\p{L}\p{N}]+(?:['’\-][\p{L}\p{N}]+)*/gu
                );

                return coincidencias ? coincidencias.length : 0;
            }

            function actualizarContador() {
                const total = contarPalabras(contenido.value);
                const excedido = total > maximoPalabras;

                contador.textContent =
                    `${total.toLocaleString('es-MX')} de 2,000 palabras`;

                contador.classList.toggle('text-red-600', excedido);
                contador.classList.toggle('font-bold', excedido);
                contador.classList.toggle('text-gray-500', !excedido);

                botonGuardar.disabled = excedido;

                if (excedido) {
                    estadoLimite.textContent =
                        `Elimina ${total - maximoPalabras} palabra(s) para continuar.`;

                    estadoLimite.classList.add('text-red-600', 'font-semibold');
                } else {
                    estadoLimite.textContent =
                        `Puedes agregar ${maximoPalabras - total} palabra(s) más.`;

                    estadoLimite.classList.remove(
                        'text-red-600',
                        'font-semibold'
                    );
                }
            }

            contenido.addEventListener('input', actualizarContador);

            form.addEventListener('submit', (event) => {
                const total = contarPalabras(contenido.value);

                if (total > maximoPalabras) {
                    event.preventDefault();
                    contenido.focus();
                    actualizarContador();
                }
            });

            actualizarContador();
        });
    </script>
</x-app-layout>