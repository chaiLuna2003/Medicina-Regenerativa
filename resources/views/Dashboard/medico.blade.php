<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-blue-600">
                    Panel médico
                </p>

                <h2 class="mt-1 text-2xl font-bold text-gray-900">
                    {{ auth()->user()->name }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Consulta tu agenda y las citas asignadas.
                </p>
            </div>

            <button
                id="activar-notificaciones"
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl
                       border border-blue-200 bg-blue-50 px-4 py-2.5
                       text-sm font-semibold text-blue-700 transition
                       hover:bg-blue-100"
            >
                <span>🔔</span>
                Activar recordatorios
            </button>
        </div>
    </x-slot>

    @php
        $citasCalendario = $citas->map(function ($cita) {
            $paciente = $cita->paciente;

            return [
                'id' => $cita->id,
                'fecha' => $cita->fecha->format('Y-m-d'),
                'hora' => \Carbon\Carbon::parse($cita->hora)->format('H:i'),
                'hora_formateada' => \Carbon\Carbon::parse($cita->hora)
                    ->format('h:i A'),
                'paciente' => trim(
                    ($paciente?->nombre ?? '') . ' ' .
                    ($paciente?->apellido ?? '')
                ),
                'edad' => $paciente?->edad,
                'telefono' => $paciente?->telefono,
                'motivo' => $cita->motivo,
                'estado' => $cita->estado_actual,
                'primera_consulta' => ($paciente?->citas_count ?? 0) <= 1,
                'tiene_signos' => $cita->signoVital !== null,
                'url' => route('citas.show', $cita),
                'signos' => $cita->signoVital ? [
                    'peso' => $cita->signoVital->peso,
                    'temperatura' => $cita->signoVital->temperatura,
                    'presion' =>
                        ($cita->signoVital->presion_sistolica ?? '—') . '/' .
                        ($cita->signoVital->presion_diastolica ?? '—'),
                    'frecuencia' =>
                        $cita->signoVital->frecuencia_cardiaca,
                    'saturacion' =>
                        $cita->signoVital->saturacion_oxigeno,
                ] : null,
            ];
        })->values();
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if ($medico === null)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">
                    <h3 class="font-bold text-amber-900">
                        Perfil médico no encontrado
                    </h3>

                    <p class="mt-2 text-sm text-amber-700">
                        Tu usuario todavía no está vinculado con un médico.
                        Solicita al administrador completar la vinculación.
                    </p>
                </div>
            @else
                <div class="grid gap-6 lg:grid-cols-[380px_minmax(0,1fr)]">

                    {{-- Columna izquierda --}}
                    <div class="space-y-6">

                        {{-- Reloj --}}
                        <section
                            class="overflow-hidden rounded-2xl
                                   bg-gradient-to-br from-[#0D3B7F]
                                   to-blue-600 p-6 text-white shadow-lg"
                        >
                            <p class="text-sm font-medium text-blue-100">
                                Hora actual
                            </p>

                            <p
                                id="reloj"
                                class="mt-3 text-4xl font-bold tracking-tight"
                            >
                                --:--:--
                            </p>

                            <p
                                id="fecha-actual"
                                class="mt-2 text-sm capitalize text-blue-100"
                            ></p>
                        </section>

                        {{-- Calendario --}}
                        <section
                            class="rounded-2xl border border-gray-200
                                   bg-white p-5 shadow-sm"
                        >
                            <div class="flex items-center justify-between">
                                <button
                                    id="mes-anterior"
                                    type="button"
                                    class="flex h-10 w-10 items-center justify-center
                                           rounded-xl border border-gray-200
                                           text-gray-600 transition hover:bg-gray-50"
                                    aria-label="Mes anterior"
                                >
                                    ←
                                </button>

                                <h3
                                    id="titulo-calendario"
                                    class="font-bold capitalize text-gray-900"
                                ></h3>

                                <button
                                    id="mes-siguiente"
                                    type="button"
                                    class="flex h-10 w-10 items-center justify-center
                                           rounded-xl border border-gray-200
                                           text-gray-600 transition hover:bg-gray-50"
                                    aria-label="Mes siguiente"
                                >
                                    →
                                </button>
                            </div>

                            <div
                                class="mt-5 grid grid-cols-7 gap-1 text-center
                                       text-xs font-semibold text-gray-400"
                            >
                                <span>Dom</span>
                                <span>Lun</span>
                                <span>Mar</span>
                                <span>Mié</span>
                                <span>Jue</span>
                                <span>Vie</span>
                                <span>Sáb</span>
                            </div>

                            <div
                                id="dias-calendario"
                                class="mt-2 grid grid-cols-7 gap-1"
                            ></div>

                            <div class="mt-5 flex flex-wrap gap-4 border-t border-gray-100 pt-4 text-xs text-gray-500">
                                <span class="flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                                    Día seleccionado
                                </span>

                                <span class="flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                    Tiene citas
                                </span>
                            </div>
                        </section>
                    </div>

                    {{-- Agenda --}}
                    <section
                        class="overflow-hidden rounded-2xl
                               border border-gray-200 bg-white shadow-sm"
                    >
                        <div class="border-b border-gray-100 px-6 py-5">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-blue-600">
                                        Cronograma
                                    </p>

                                    <h3
                                        id="fecha-seleccionada"
                                        class="mt-1 text-xl font-bold
                                               capitalize text-gray-900"
                                    ></h3>
                                </div>

                                <span
                                    id="contador-citas"
                                    class="w-fit rounded-full bg-blue-50 px-3 py-1.5
                                           text-xs font-semibold text-blue-700"
                                >
                                    0 citas
                                </span>
                            </div>
                        </div>

                        <div
                            id="lista-citas"
                            class="divide-y divide-gray-100"
                        ></div>

                        <div
                            id="sin-citas"
                            class="hidden px-6 py-20 text-center"
                        >
                            <div
                                class="mx-auto flex h-14 w-14 items-center
                                       justify-center rounded-full bg-gray-100
                                       text-2xl"
                            >
                                📅
                            </div>

                            <h4 class="mt-4 font-bold text-gray-900">
                                No hay citas programadas
                            </h4>

                            <p class="mt-2 text-sm text-gray-500">
                                No tienes pacientes asignados para esta fecha.
                            </p>
                        </div>
                    </section>
                </div>
            @endif
        </div>
    </div>

    {{-- Ventana de recordatorio --}}
    <div
        id="modal-recordatorio"
        class="fixed inset-0 z-50 hidden items-center justify-center
               bg-gray-950/50 p-4"
        role="dialog"
        aria-modal="true"
    >
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div
                class="flex h-12 w-12 items-center justify-center
                       rounded-full bg-blue-100 text-2xl"
            >
                🔔
            </div>

            <p class="mt-5 text-xs font-bold uppercase tracking-widest text-blue-600">
                Consulta próxima
            </p>

            <h3 class="mt-2 text-xl font-bold text-gray-900">
                Tu cita comienza en cinco minutos
            </h3>

            <p
                id="mensaje-recordatorio"
                class="mt-2 text-sm leading-6 text-gray-600"
            ></p>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button
                    id="cerrar-recordatorio"
                    type="button"
                    class="rounded-xl border border-gray-300 px-4 py-2.5
                           text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    Silenciar
                </button>

                <a
                    id="abrir-cita-recordatorio"
                    href="#"
                    class="rounded-xl bg-[#0D3B7F] px-4 py-2.5
                           text-center text-sm font-semibold text-white
                           hover:bg-[#082a5d]"
                >
                    Ver cita
                </a>
            </div>
        </div>
    </div>

    @if ($medico !== null)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const citas = @json($citasCalendario);

                const diasCalendario =
                    document.getElementById('dias-calendario');

                const tituloCalendario =
                    document.getElementById('titulo-calendario');

                const fechaSeleccionadaTexto =
                    document.getElementById('fecha-seleccionada');

                const listaCitas =
                    document.getElementById('lista-citas');

                const sinCitas =
                    document.getElementById('sin-citas');

                const contadorCitas =
                    document.getElementById('contador-citas');

                const modal =
                    document.getElementById('modal-recordatorio');

                const mensajeRecordatorio =
                    document.getElementById('mensaje-recordatorio');

                const abrirCitaRecordatorio =
                    document.getElementById('abrir-cita-recordatorio');

                const botonNotificaciones =
                    document.getElementById('activar-notificaciones');

                const hoy = new Date();

                let mesVisible = new Date(
                    hoy.getFullYear(),
                    hoy.getMonth(),
                    1
                );

                let fechaSeleccionada = formatearFecha(hoy);
                let audioContext = null;

                function formatearFecha(fecha) {
                    const anio = fecha.getFullYear();
                    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                    const dia = String(fecha.getDate()).padStart(2, '0');

                    return `${anio}-${mes}-${dia}`;
                }

                function escapar(texto) {
                    const div = document.createElement('div');
                    div.textContent = texto ?? '';
                    return div.innerHTML;
                }

                function actualizarReloj() {
                    const ahora = new Date();

                    document.getElementById('reloj').textContent =
                        ahora.toLocaleTimeString('es-MX', {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                        });

                    document.getElementById('fecha-actual').textContent =
                        ahora.toLocaleDateString('es-MX', {
                            weekday: 'long',
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                        });
                }

                function renderizarCalendario() {
                    diasCalendario.innerHTML = '';

                    tituloCalendario.textContent =
                        mesVisible.toLocaleDateString('es-MX', {
                            month: 'long',
                            year: 'numeric',
                        });

                    const anio = mesVisible.getFullYear();
                    const mes = mesVisible.getMonth();

                    const primerDia = new Date(anio, mes, 1).getDay();
                    const totalDias = new Date(anio, mes + 1, 0).getDate();

                    for (let i = 0; i < primerDia; i++) {
                        const espacio = document.createElement('div');
                        espacio.className = 'h-11';
                        diasCalendario.appendChild(espacio);
                    }

                    for (let dia = 1; dia <= totalDias; dia++) {
                        const fecha = new Date(anio, mes, dia);
                        const fechaFormato = formatearFecha(fecha);

                        const tieneCitas = citas.some(
                            cita => cita.fecha === fechaFormato
                        );

                        const esSeleccionado =
                            fechaFormato === fechaSeleccionada;

                        const esHoy =
                            fechaFormato === formatearFecha(hoy);

                        const boton = document.createElement('button');

                        boton.type = 'button';

                        boton.className = [
                            'relative h-11 rounded-xl text-sm font-semibold transition',
                            esSeleccionado
                                ? 'bg-[#0D3B7F] text-white'
                                : 'text-gray-700 hover:bg-blue-50',
                            esHoy && !esSeleccionado
                                ? 'ring-1 ring-blue-300'
                                : '',
                        ].join(' ');

                        boton.innerHTML = `
                            <span>${dia}</span>
                            ${tieneCitas
                                ? `<span class="absolute bottom-1.5 left-1/2
                                     h-1.5 w-1.5 -translate-x-1/2 rounded-full
                                     ${esSeleccionado
                                        ? 'bg-white'
                                        : 'bg-emerald-500'}"></span>`
                                : ''
                            }
                        `;

                        boton.addEventListener('click', () => {
                            fechaSeleccionada = fechaFormato;
                            renderizarCalendario();
                            renderizarCitas();
                        });

                        diasCalendario.appendChild(boton);
                    }
                }

                function renderizarCitas() {
                    const fecha = new Date(`${fechaSeleccionada}T12:00:00`);

                    fechaSeleccionadaTexto.textContent =
                        fecha.toLocaleDateString('es-MX', {
                            weekday: 'long',
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                        });

                    const citasDelDia = citas
                        .filter(cita => cita.fecha === fechaSeleccionada)
                        .sort((a, b) => a.hora.localeCompare(b.hora));

                    contadorCitas.textContent =
                        `${citasDelDia.length} ${
                            citasDelDia.length === 1 ? 'cita' : 'citas'
                        }`;

                    listaCitas.innerHTML = '';

                    if (citasDelDia.length === 0) {
                        listaCitas.classList.add('hidden');
                        sinCitas.classList.remove('hidden');
                        return;
                    }

                    listaCitas.classList.remove('hidden');
                    sinCitas.classList.add('hidden');

                    citasDelDia.forEach(cita => {
                        const articulo = document.createElement('article');

                        articulo.className =
                            'px-6 py-5 transition hover:bg-gray-50';

                        const signos = cita.tiene_signos
                            ? `
                                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
                                    <div class="rounded-xl bg-gray-50 px-3 py-2">
                                        <p class="text-xs text-gray-400">Peso</p>
                                        <p class="mt-1 text-sm font-bold text-gray-800">
                                            ${escapar(cita.signos.peso ?? '—')} kg
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-gray-50 px-3 py-2">
                                        <p class="text-xs text-gray-400">Presión</p>
                                        <p class="mt-1 text-sm font-bold text-gray-800">
                                            ${escapar(cita.signos.presion)}
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-gray-50 px-3 py-2">
                                        <p class="text-xs text-gray-400">Temperatura</p>
                                        <p class="mt-1 text-sm font-bold text-gray-800">
                                            ${escapar(cita.signos.temperatura ?? '—')} °C
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-gray-50 px-3 py-2">
                                        <p class="text-xs text-gray-400">Saturación</p>
                                        <p class="mt-1 text-sm font-bold text-gray-800">
                                            ${escapar(cita.signos.saturacion ?? '—')}%
                                        </p>
                                    </div>
                                </div>
                            `
                            : `
                                <p class="mt-4 inline-flex rounded-full
                                          bg-amber-50 px-3 py-1.5 text-xs
                                          font-semibold text-amber-700">
                                    Signos vitales pendientes
                                </p>
                            `;

                        articulo.innerHTML = `
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start">
                                <div class="w-24 shrink-0">
                                    <p class="text-xl font-bold text-[#0D3B7F]">
                                        ${escapar(cita.hora_formateada)}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        ${escapar(cita.estado.replaceAll('_', ' '))}
                                    </p>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-bold text-gray-900">
                                            ${escapar(cita.paciente)}
                                        </h4>

                                        <span class="rounded-full px-2.5 py-1
                                                     text-xs font-semibold
                                                     ${cita.primera_consulta
                                                        ? 'bg-violet-50 text-violet-700'
                                                        : 'bg-gray-100 text-gray-600'}">
                                            ${cita.primera_consulta
                                                ? 'Primera consulta'
                                                : 'Paciente recurrente'}
                                        </span>
                                    </div>

                                    <p class="mt-2 text-sm text-gray-600">
                                        ${escapar(cita.motivo || 'Sin motivo registrado')}
                                    </p>

                                    <p class="mt-2 text-xs text-gray-400">
                                        ${escapar(cita.edad || 'Edad no disponible')}
                                        ${cita.telefono
                                            ? ` · ${escapar(cita.telefono)}`
                                            : ''}
                                    </p>

                                    ${signos}
                                </div>

                                <a
                                    href="${cita.url}"
                                    class="inline-flex shrink-0 justify-center
                                           rounded-xl bg-[#0D3B7F] px-4 py-2.5
                                           text-sm font-semibold text-white
                                           transition hover:bg-[#082a5d]"
                                >
                                    Ver cita
                                </a>
                            </div>
                        `;

                        listaCitas.appendChild(articulo);
                    });
                }

                function reproducirSonido() {
                    audioContext ??=
                        new (window.AudioContext ||
                             window.webkitAudioContext)();

                    const inicio = audioContext.currentTime;

                    [0, 0.3, 0.6].forEach(retraso => {
                        const oscilador =
                            audioContext.createOscillator();

                        const volumen =
                            audioContext.createGain();

                        oscilador.frequency.value = 880;

                        volumen.gain.setValueAtTime(
                            0.16,
                            inicio + retraso
                        );

                        volumen.gain.exponentialRampToValueAtTime(
                            0.001,
                            inicio + retraso + 0.2
                        );

                        oscilador
                            .connect(volumen)
                            .connect(audioContext.destination);

                        oscilador.start(inicio + retraso);
                        oscilador.stop(inicio + retraso + 0.2);
                    });
                }

                async function activarRecordatorios() {
                    audioContext ??=
                        new (window.AudioContext ||
                             window.webkitAudioContext)();

                    if (audioContext.state === 'suspended') {
                        await audioContext.resume();
                    }

                    if (
                        'Notification' in window &&
                        Notification.permission === 'default'
                    ) {
                        await Notification.requestPermission();
                    }

                    localStorage.setItem(
                        'recordatorios-medicos-activos',
                        '1'
                    );

                    botonNotificaciones.innerHTML =
                        '<span>✓</span> Recordatorios activados';

                    botonNotificaciones.classList.remove(
                        'border-blue-200',
                        'bg-blue-50',
                        'text-blue-700'
                    );

                    botonNotificaciones.classList.add(
                        'border-emerald-200',
                        'bg-emerald-50',
                        'text-emerald-700'
                    );

                    revisarRecordatorios();
                }

                function revisarRecordatorios() {
                    if (
                        localStorage.getItem(
                            'recordatorios-medicos-activos'
                        ) !== '1'
                    ) {
                        return;
                    }

                    const ahora = new Date();

                    citas.forEach(cita => {
                        const fechaCita = new Date(
                            `${cita.fecha}T${cita.hora}:00`
                        );

                        const diferenciaMinutos =
                            (fechaCita.getTime() - ahora.getTime()) / 60000;

                        const clave =
                            `recordatorio-medico-${cita.id}-${cita.fecha}`;

                        if (
                            diferenciaMinutos > 4 &&
                            diferenciaMinutos <= 5 &&
                            localStorage.getItem(clave) !== '1'
                        ) {
                            localStorage.setItem(clave, '1');

                            mensajeRecordatorio.textContent =
                                `${cita.paciente} está programado a las ` +
                                `${cita.hora_formateada}.`;

                            abrirCitaRecordatorio.href = cita.url;

                            modal.classList.remove('hidden');
                            modal.classList.add('flex');

                            reproducirSonido();

                            if (
                                'Notification' in window &&
                                Notification.permission === 'granted'
                            ) {
                                const notificacion = new Notification(
                                    'Consulta próxima',
                                    {
                                        body:
                                            `${cita.paciente} tiene cita ` +
                                            `en cinco minutos.`,
                                        icon: '/favicon.ico',
                                    }
                                );

                                notificacion.onclick = () => {
                                    window.focus();
                                    window.location.href = cita.url;
                                };
                            }
                        }
                    });
                }

                document
                    .getElementById('mes-anterior')
                    .addEventListener('click', () => {
                        mesVisible = new Date(
                            mesVisible.getFullYear(),
                            mesVisible.getMonth() - 1,
                            1
                        );

                        renderizarCalendario();
                    });

                document
                    .getElementById('mes-siguiente')
                    .addEventListener('click', () => {
                        mesVisible = new Date(
                            mesVisible.getFullYear(),
                            mesVisible.getMonth() + 1,
                            1
                        );

                        renderizarCalendario();
                    });

                document
                    .getElementById('cerrar-recordatorio')
                    .addEventListener('click', () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    });

                botonNotificaciones.addEventListener(
                    'click',
                    activarRecordatorios
                );

                if (
                    localStorage.getItem(
                        'recordatorios-medicos-activos'
                    ) === '1'
                ) {
                    botonNotificaciones.innerHTML =
                        '<span>✓</span> Recordatorios activados';
                }

                actualizarReloj();
                renderizarCalendario();
                renderizarCitas();
                revisarRecordatorios();

                window.setInterval(actualizarReloj, 1000);
                window.setInterval(revisarRecordatorios, 30000);
            });
        </script>
    @endif
</x-app-layout>