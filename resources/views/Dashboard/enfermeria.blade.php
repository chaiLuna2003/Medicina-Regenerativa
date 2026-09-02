<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-emerald-600">
                    Panel de enfermería
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Buenos días, {{ auth()->user()->name }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">

                <x-hoja-diaria-button class="w-full sm:w-auto" />

                <a
                    href="{{ route('signos-vitales.index') }}"
                    class="inline-flex w-full items-center justify-center
               gap-2 rounded-xl border border-gray-300
               bg-white px-5 py-3 text-sm font-semibold
               text-gray-700 shadow-sm transition
               hover:border-gray-400 hover:bg-gray-50
               focus:outline-none focus:ring-2
               focus:ring-gray-900 focus:ring-offset-2
               sm:w-auto">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7
                   a2 2 0 01-2-2V5a2 2 0 012-2
                   h5.586a1 1 0 01.707.293
                   l3.414 3.414A1 1 0 0117 7.414V19
                   a2 2 0 01-2 2z" />
                    </svg>

                    Historial de signos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- Mensajes del sistema --}}
            @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
            @endif

            @if (session('info'))
            <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm font-medium text-blue-700">
                {{ session('info') }}
            </div>
            @endif

            {{-- Indicadores --}}
            <section>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    {{-- Citas activas --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Citas activas
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $citasHoy->count() }}
                                </p>
                            </div>

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Citas no canceladas para hoy
                        </p>
                    </article>

                    {{-- Pendientes --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Pendientes
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $citasPendientes }}
                                </p>
                            </div>

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Citas sin signos vitales
                        </p>
                    </article>

                    {{-- Valoraciones realizadas --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Valoraciones realizadas
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $valoracionesRealizadas }}
                                </p>
                            </div>

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Signos vitales registrados hoy
                        </p>
                    </article>

                    {{-- Canceladas --}}
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Canceladas
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ $citasCanceladas }}
                                </p>
                            </div>

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Citas canceladas durante el día
                        </p>
                    </article>

                </div>
            </section>

            {{-- Próxima valoración --}}
            <section>
                @if ($proximaCita)
                <article class="relative overflow-hidden rounded-2xl bg-gray-900 p-6 text-white shadow-sm">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-emerald-400/10"></div>
                    <div class="absolute -bottom-16 right-24 h-40 w-40 rounded-full bg-blue-400/10"></div>

                    <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/10 text-emerald-300">
                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-emerald-300">
                                    Próxima valoración
                                </p>

                                <h3 class="mt-1 text-xl font-bold">
                                    {{ trim(
                                            ($proximaCita->paciente?->nombre ?? '') . ' ' .
                                            ($proximaCita->paciente?->apellido_paterno ?? $proximaCita->paciente?->apellido ?? '')
                                        ) ?: 'Paciente no disponible' }}
                                </h3>

                                <p class="mt-2 text-sm text-gray-300">
                                    {{ \Carbon\Carbon::parse($proximaCita->hora)->format('H:i') }}
                                    · Dr. {{ trim(
                                            ($proximaCita->medico?->nombre ?? '') . ' ' .
                                            ($proximaCita->medico?->apellido_paterno ?? '')
                                        ) ?: 'No asignado' }}
                                </p>

                                @if ($proximaCita->motivo)
                                <p class="mt-1 text-sm text-gray-400">
                                    {{ $proximaCita->motivo }}
                                </p>
                                @endif
                            </div>
                        </div>
<button
    type="button"
    data-abrir-modal-signos
    data-cita-id="{{ $proximaCita->id }}"
    data-url="{{
        route('signos-vitales.store', $proximaCita)
    }}"
    data-paciente="{{
        trim(
            ($proximaCita->paciente?->nombre ?? '')
            . ' '
            . (
                $proximaCita->paciente?->apellido_paterno
                ?? $proximaCita->paciente?->apellido
                ?? ''
            )
            . ' '
            . (
                $proximaCita->paciente?->apellido_materno
                ?? ''
            )
        ) ?: 'Paciente no disponible'
    }}"
    class="inline-flex shrink-0 items-center
           justify-center gap-2 rounded-xl
           bg-emerald-500 px-5 py-3 text-sm
           font-semibold text-white shadow-sm
           transition hover:bg-emerald-400
           focus:outline-none focus:ring-2
           focus:ring-emerald-400 focus:ring-offset-2
           focus:ring-offset-gray-900">

    <svg
        class="h-5 w-5"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24">

        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4v16m8-8H4" />
    </svg>

    Registrar signos vitales
</button>
                    </div>
                </article>
                @else
                <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-bold text-emerald-900">
                                No hay valoraciones próximas
                            </h3>

                            <p class="mt-1 text-sm text-emerald-700">
                                No quedan citas futuras pendientes de signos vitales para hoy.
                            </p>
                        </div>
                    </div>
                </article>
                @endif
            </section>

            {{-- Valoraciones organizadas por prioridad --}}
            <div class="space-y-6">
            @if ($pendientesProximas->isNotEmpty())
                {{-- Próximas pendientes --}}
                <section
                    class="overflow-hidden rounded-2xl border
               border-blue-200 bg-white shadow-sm">

                    <div
                        class="flex flex-col gap-3 border-b
                   border-blue-100 bg-blue-50/60
                   px-6 py-5 sm:flex-row
                   sm:items-center sm:justify-between">

                        <div>
                            <p
                                class="text-xs font-bold uppercase
                           tracking-wide text-blue-600">
                                Próximas
                            </p>

                            <h3 class="mt-1 text-lg font-bold text-gray-900">
                                Valoraciones pendientes
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Ordenadas desde el horario más próximo.
                            </p>
                        </div>

                        <span
                            class="inline-flex w-fit items-center
                       rounded-full bg-blue-100 px-3 py-1.5
                       text-sm font-bold text-blue-700">

                            {{ $pendientesProximas->count() }}
                            {{ $pendientesProximas->count() === 1
                    ? 'paciente'
                    : 'pacientes' }}
                        </span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($pendientesProximas as $cita)
                        @include(
                        'Dashboard.partials.fila-valoracion',
                        [
                        'cita' => $cita,
                        'tipoValoracion' => 'proxima',
                        ]
                        )
                        @empty
                        <div class="px-6 py-12 text-center">
                            <div
                                class="mx-auto flex h-12 w-12
                               items-center justify-center
                               rounded-xl bg-blue-50 text-blue-500">

                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <p class="mt-4 font-semibold text-gray-900">
                                No hay valoraciones próximas
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                No quedan citas futuras pendientes para hoy.
                            </p>
                        </div>
                        @endforelse
                    </div>
                </section>
                @endif
                {{-- Pendientes atrasadas --}}
                <section
                    class="overflow-hidden rounded-2xl border
               border-red-200 bg-white shadow-sm">

                    <div
                        class="flex flex-col gap-3 border-b
                   border-red-100 bg-red-50/60
                   px-6 py-5 sm:flex-row
                   sm:items-center sm:justify-between">

                        <div>
                            <p
                                class="text-xs font-bold uppercase
                           tracking-wide text-red-600">
                                Requieren atención
                            </p>

                            <h3 class="mt-1 text-lg font-bold text-gray-900">
                                Valoraciones atrasadas
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Su horario ya pasó y todavía no tienen signos vitales.
                            </p>
                        </div>

                        <span
                            class="inline-flex w-fit items-center
                       rounded-full bg-red-100 px-3 py-1.5
                       text-sm font-bold text-red-700">

                            {{ $pendientesAtrasadas->count() }}
                            {{ $pendientesAtrasadas->count() === 1
                    ? 'paciente'
                    : 'pacientes' }}
                        </span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($pendientesAtrasadas as $cita)
                        @include(
                        'Dashboard.partials.fila-valoracion',
                        [
                        'cita' => $cita,
                        'tipoValoracion' => 'atrasada',
                        ]
                        )
                        @empty
                        <div class="px-6 py-12 text-center">
                            <div
                                class="mx-auto flex h-12 w-12
                               items-center justify-center
                               rounded-xl bg-emerald-50
                               text-emerald-600">

                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <p class="mt-4 font-semibold text-gray-900">
                                No hay pacientes atrasados
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Todas las valoraciones vencidas están atendidas.
                            </p>
                        </div>
                        @endforelse
                    </div>
                </section>

                {{-- Valoraciones realizadas --}}
                <section
                    class="overflow-hidden rounded-2xl border
               border-emerald-200 bg-white shadow-sm">

                    <div
                        class="flex flex-col gap-3 border-b
                   border-emerald-100 bg-emerald-50/60
                   px-6 py-5 sm:flex-row
                   sm:items-center sm:justify-between">

                        <div>
                            <p
                                class="text-xs font-bold uppercase
                           tracking-wide text-emerald-600">
                                Completadas
                            </p>

                            <h3 class="mt-1 text-lg font-bold text-gray-900">
                                Valoraciones realizadas
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Signos vitales registrados durante el día.
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <span
                                class="inline-flex w-fit items-center
                           rounded-full bg-emerald-100
                           px-3 py-1.5 text-sm font-bold
                           text-emerald-700">

                                {{ $valoracionesRealizadasLista->count() }}
                                {{ $valoracionesRealizadasLista->count() === 1
                        ? 'paciente'
                        : 'pacientes' }}
                            </span>

                            <a
                                href="{{ route('signos-vitales.index') }}"
                                class="text-sm font-semibold text-emerald-700
                           transition hover:text-emerald-800">
                                Ver historial
                            </a>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($valoracionesRealizadasLista as $cita)
                        @include(
                        'Dashboard.partials.fila-valoracion',
                        [
                        'cita' => $cita,
                        'tipoValoracion' => 'realizada',
                        ]
                        )
                        @empty
                        <div class="px-6 py-12 text-center">
                            <div
                                class="mx-auto flex h-12 w-12
                               items-center justify-center
                               rounded-xl bg-gray-100 text-gray-400">

                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 2m6-2
                                   a9 9 0 11-18 0
                                   9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <p class="mt-4 font-semibold text-gray-900">
                                Aún no hay valoraciones realizadas
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Los registros completados aparecerán aquí.
                            </p>
                        </div>
                        @endforelse
                    </div>
                </section>
            </div>

        </div>
    </div>
    @include('Dashboard.modals.registrar-signos-vitales')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById(
                'modal-signos-vitales'
            );

            const formulario = document.getElementById(
                'form-modal-signos'
            );

            if (!modal || !formulario) {
                return;
            }

            const citaIdInput = document.getElementById(
                'modal-cita-id'
            );

            const nombrePaciente = document.getElementById(
                'modal-nombre-paciente'
            );

            const pacienteNombreInput = document.getElementById(
                'modal-paciente-input'
            );

            const pesoInput = document.getElementById(
                'modal-peso'
            );

            const estaturaInput = document.getElementById(
                'modal-estatura'
            );

            const imcValor = document.getElementById(
                'modal-imc-valor'
            );

            const imcClasificacion = document.getElementById(
                'modal-imc-clasificacion'
            );

            const observaciones = document.getElementById(
                'modal-observaciones'
            );

            const contadorObservaciones = document.getElementById(
                'modal-contador-observaciones'
            );

            const botonGuardar = document.getElementById(
                'modal-boton-guardar'
            );

            const textoGuardar = document.getElementById(
                'modal-texto-guardar'
            );

            let elementoAnterior = null;

            const estilosImc = {
                bajo: [
                    'bg-blue-50',
                    'text-blue-700',
                ],
                normal: [
                    'bg-emerald-50',
                    'text-emerald-700',
                ],
                sobrepeso: [
                    'bg-amber-50',
                    'text-amber-700',
                ],
                obesidad: [
                    'bg-red-50',
                    'text-red-700',
                ],
                vacio: [
                    'bg-gray-200',
                    'text-gray-600',
                ],
            };

            function aplicarEstiloImc(estilo) {
                imcClasificacion.className =
                    'rounded-full px-2.5 py-1 ' +
                    'text-xs font-semibold';

                imcClasificacion.classList.add(
                    ...estilosImc[estilo]
                );
            }

            function actualizarImc() {
                const peso = Number.parseFloat(
                    pesoInput.value
                );

                const estaturaCentimetros = Number.parseFloat(
                    estaturaInput.value
                );

                if (
                    !Number.isFinite(peso) ||
                    !Number.isFinite(estaturaCentimetros) ||
                    peso <= 0 ||
                    estaturaCentimetros <= 0
                ) {
                    imcValor.textContent = '—';
                    imcClasificacion.textContent =
                        'Sin calcular';

                    aplicarEstiloImc('vacio');

                    return;
                }

                const estaturaMetros =
                    estaturaCentimetros / 100;

                const imc =
                    peso / (estaturaMetros * estaturaMetros);

                imcValor.textContent = imc.toFixed(2);

                if (imc < 18.5) {
                    imcClasificacion.textContent =
                        'Peso bajo';

                    aplicarEstiloImc('bajo');
                } else if (imc < 25) {
                    imcClasificacion.textContent =
                        'Rango normal';

                    aplicarEstiloImc('normal');
                } else if (imc < 30) {
                    imcClasificacion.textContent =
                        'Sobrepeso';

                    aplicarEstiloImc('sobrepeso');
                } else {
                    imcClasificacion.textContent =
                        'Obesidad';

                    aplicarEstiloImc('obesidad');
                }
            }

            function actualizarContador() {
                contadorObservaciones.textContent =
                    observaciones.value.length;
            }

            function limpiarFormulario() {
                formulario
                    .querySelectorAll(
                        'input:not([type="hidden"]), textarea'
                    )
                    .forEach((campo) => {
                        campo.value = '';
                    });

                const resumenErrores = modal.querySelector(
                    '[role="alert"]'
                );

                if (resumenErrores) {
                    resumenErrores.classList.add('hidden');
                }

                botonGuardar.disabled = false;
                textoGuardar.textContent =
                    'Guardar signos vitales';

                actualizarImc();
                actualizarContador();
            }

            function abrirModal(boton, conservarDatos = false) {
                elementoAnterior = boton;

                if (!conservarDatos) {
                    limpiarFormulario();
                }

                if (boton) {
                    citaIdInput.value =
                        boton.dataset.citaId;

                    formulario.action =
                        boton.dataset.url;

                    nombrePaciente.textContent =
                        boton.dataset.paciente;

                    if (pacienteNombreInput) {
                        pacienteNombreInput.value =
                            boton.dataset.paciente;
                    }
                }

                if (
                    !boton &&
                    conservarDatos &&
                    pacienteNombreInput?.value
                ) {
                    nombrePaciente.textContent =
                        pacienteNombreInput.value;
                }

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                window.setTimeout(() => {
                    pesoInput.focus();
                }, 100);

                actualizarImc();
                actualizarContador();
            }

            function cerrarModal() {
                modal.classList.add('hidden');
                document.body.classList.remove(
                    'overflow-hidden'
                );

                if (elementoAnterior) {
                    elementoAnterior.focus();
                }
            }

            document
                .querySelectorAll('[data-abrir-modal-signos]')
                .forEach((boton) => {
                    boton.addEventListener('click', () => {
                        abrirModal(boton);
                    });
                });

            modal
                .querySelectorAll('[data-cerrar-modal-signos]')
                .forEach((boton) => {
                    boton.addEventListener(
                        'click',
                        cerrarModal
                    );
                });

            document.addEventListener('keydown', (event) => {
                if (
                    event.key === 'Escape' &&
                    !modal.classList.contains('hidden')
                ) {
                    cerrarModal();
                }
            });

            pesoInput.addEventListener(
                'input',
                actualizarImc
            );

            estaturaInput.addEventListener(
                'input',
                actualizarImc
            );

            observaciones.addEventListener(
                'input',
                actualizarContador
            );

            formulario.addEventListener('submit', (event) => {
                if (!formulario.checkValidity()) {
                    event.preventDefault();
                    formulario.reportValidity();

                    return;
                }

                const confirmado = window.confirm(
                    '¿Confirmas que los datos capturados ' +
                    'son correctos?'
                );

                if (!confirmado) {
                    event.preventDefault();

                    return;
                }

                botonGuardar.disabled = true;
                textoGuardar.textContent =
                    'Guardando valoración...';
            });

            const citaConErrores = @json(
                old('modal_cita_id')
            );

            if (citaConErrores) {
                const botonCita = document.querySelector(
                    '[data-abrir-modal-signos]' +
                    '[data-cita-id="' +
                    citaConErrores +
                    '"]'
                );

                abrirModal(botonCita, true);
            }
        });
    </script>
</x-app-layout>