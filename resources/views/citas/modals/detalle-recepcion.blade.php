@php
$datosCitasRecepcion = $citas->mapWithKeys(
function ($cita) {
$nombrePaciente = trim(
($cita->paciente?->nombre ?? '')
. ' '
. ($cita->paciente?->apellido ?? '')
);

$nombreMedico = trim(
($cita->medico?->nombre ?? '')
. ' '
. ($cita->medico?->apellido_paterno ?? '')
. ' '
. ($cita->medico?->apellido_materno ?? '')
);

return [
$cita->id => [
'id' => $cita->id,

'paciente' => $nombrePaciente
?: 'Paciente no disponible',

'medico' => $nombreMedico
?: 'Médico no asignado',

'especialidad' =>
$cita->medico?->especialidad
?: 'Medicina general',

'motivo' => $cita->motivo_texto,

'fecha' => $cita->fecha
->locale('es')
->translatedFormat(
'd \d\e F \d\e Y'
),

'hora' => \Carbon\Carbon::parse(
$cita->hora
)->format('h:i A'),

'hora_fin' => $cita->hora_fin
->format('h:i A'),

'duracion' =>
($cita->duracion_minutos ?? 15)
. ' minutos',

'foto' => $cita->paciente
? $cita->paciente->fotoUrl()
: asset('images/default.webp'),

'estado' => $cita->estado_actual,

'estado_texto' => match (
$cita->estado_actual
) {
'confirmada' => 'Confirmada',
'en_espera' => 'En espera',
'en_curso',
'en_consulta' => 'En consulta',
'finalizada' => 'Finalizada',
'cancelada' => 'Cancelada',
default => 'Programada',
},

'modalidad' => $cita->modalidad,

'modalidad_texto' => match (
$cita->modalidad
) {
'videoconsulta' =>
'Videollamada',

'telefonica' =>
'Telefónica',

'fuera_instalaciones' =>
'Fuera de las instalaciones',

default =>
'Consultorio u oficina',
},

'direccion' =>
$cita->direccion_cita,

'notas' =>
$cita->notas,

'alergias' =>
$cita->paciente?->alergias,

'telefono' =>
$cita->paciente?->telefono,

'ficha_url' =>
$cita->paciente
? route(
'pacientes.show',
$cita->paciente
)
: null,

'editar_url' =>
route('citas.edit', $cita),

'detalle_url' =>
route('citas.show', $cita),

'citas_url' =>
route('citas.index', [
'buscar' => $nombrePaciente,
]),
],
];
}
);
@endphp
@once
<style>
    @keyframes alergias-destacadas {

        0%,
        100% {
            background-color: #FDECEC;

            box-shadow:
                inset 0 0 0 0 rgba(168, 72, 72, 0);
        }

        50% {
            background-color: #F4B6B6;

            box-shadow:
                inset 5px 0 0 rgba(168, 72, 72, 0.85);
        }
    }

    .alergias-activas {
        animation:
            alergias-destacadas 1.8s ease-in-out infinite;
    }

    .alergias-activas:hover,
    .alergias-activas:focus-within {
        animation-play-state: paused;
    }

    @media (prefers-reduced-motion: reduce) {
        .alergias-activas {
            animation: none;
        }
    }
</style>
@endonce
{{-- Modal de detalle de una cita para recepción --}}
<div
    id="modal-detalle-cita"
    class="fixed inset-0 z-[60] hidden
           items-center justify-center
           bg-slate-950/60 p-4 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    aria-labelledby="detalle-cita-titulo">

    <div
        class="flex max-h-[92vh] w-full max-w-4xl
               flex-col overflow-hidden rounded-2xl
               bg-white shadow-2xl">

        {{-- Encabezado --}}
        <header
            class="flex items-start justify-between gap-4
           bg-[#315F9F] px-6 py-5 text-white">

            <div>
                <p
                    id="detalle-cita-encabezado"
                    class="text-sm font-semibold text-[#EAF2FF]">
                    Información de la cita
                </p>

                <h3
                    id="detalle-cita-titulo"
                    class="mt-1 text-xl font-bold">
                    Detalle de la cita
                </h3>
            </div>

            <button
                id="cerrar-modal-detalle-cita"
                type="button"
                class="flex h-9 w-9 items-center justify-center
           rounded-lg text-2xl transition
           hover:bg-[#AFCBFA]/30"
                aria-label="Cerrar detalle de la cita">
                &times;
            </button>
        </header>

        {{-- El contenido real se agregará en el siguiente paso --}}
        <div class="overflow-y-auto p-6">
            <div class="grid gap-6 md:grid-cols-[160px_minmax(0,1fr)]">

                {{-- Fotografía --}}
                <img
                    id="detalle-cita-foto"
                    src=""
                    data-foto-default="{{ asset('images/default.webp') }}"
                    alt="Fotografía del paciente"
                    class="h-40 w-40 rounded-2xl
               border border-[#AFCBFA]
               bg-[#EAF2FF] object-cover">

                {{-- Información principal --}}
                <div class="min-w-0">
                    <p
                        class="text-xs font-bold uppercase
                   tracking-wider text-[#315F9F]">
                        Paciente
                    </p>

                    <h4
                        id="detalle-cita-paciente"
                        class="mt-1 text-2xl font-bold text-slate-800">
                    </h4>

                    <p
                        id="detalle-cita-medico"
                        class="mt-2 text-sm font-semibold
                   italic text-slate-500">
                    </p>

                    <div
                        class="mt-6 grid gap-3
                   sm:grid-cols-2 lg:grid-cols-3">

                        <div class="rounded-xl bg-[#EAF2FF] p-4">
                            <p
                                class="text-xs font-bold uppercase
                           tracking-wide text-[#315F9F]">
                                Horario
                            </p>

                            <p
                                id="detalle-cita-horario"
                                class="mt-1 text-sm font-semibold
                           text-slate-700">
                            </p>
                        </div>

                        <div class="rounded-xl bg-[#F2EDFC] p-4">
                            <p
                                class="text-xs font-bold uppercase
                           tracking-wide text-[#684B9D]">
                                Duración
                            </p>

                            <p
                                id="detalle-cita-duracion"
                                class="mt-1 text-sm font-semibold
                           text-slate-700">
                            </p>
                        </div>

                        <div class="rounded-xl bg-[#EAF7F0] p-4">
                            <p
                                class="text-xs font-bold uppercase
                           tracking-wide text-[#347557]">
                                Especialidad
                            </p>

                            <p
                                id="detalle-cita-especialidad"
                                class="mt-1 text-sm font-semibold
                           text-slate-700">
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Estado y modalidad --}}
            <div class="mt-6 grid gap-4 sm:grid-cols-2">

                <div
                    id="detalle-cita-estado-contenedor"
                    class="rounded-xl p-4">

                    <p class="text-xs font-bold uppercase tracking-wide">
                        Estado de la cita
                    </p>

                    <p
                        id="detalle-cita-estado"
                        class="mt-1 text-sm font-bold">
                    </p>
                </div>

                <div class="rounded-xl bg-[#F2EDFC] p-4">
                    <p
                        class="text-xs font-bold uppercase
                   tracking-wide text-[#684B9D]">
                        Modalidad
                    </p>

                    <p
                        id="detalle-cita-modalidad"
                        class="mt-1 text-sm font-semibold text-slate-700">
                    </p>
                </div>
            </div>

            {{-- Dirección para atención externa --}}
            <div
                id="detalle-cita-direccion-contenedor"
                class="mt-4 hidden rounded-xl
           border border-[#AFCBFA]
           bg-[#EAF2FF] p-4">

                <p
                    class="text-xs font-bold uppercase
               tracking-wide text-[#315F9F]">
                    Lugar de atención
                </p>

                <p
                    id="detalle-cita-direccion"
                    class="mt-1 text-sm text-slate-700">
                </p>
            </div>

            {{-- Alergias --}}
            <div
                id="detalle-cita-alergias-contenedor"
                class="mt-4 rounded-xl
           border border-[#F4B6B6]
           bg-[#FDECEC] p-4">

                <p
                    class="text-xs font-bold uppercase
               tracking-wide text-[#A84848]">
                    Alergias
                </p>

                <p
                    id="detalle-cita-alergias"
                    class="mt-1 text-sm font-semibold text-[#A84848]">
                </p>
            </div>

            {{-- Notas --}}
            <div class="mt-4">
                <p class="text-sm font-bold text-slate-800">
                    Notas de la cita
                </p>

                <p
                    id="detalle-cita-notas"
                    class="mt-2 min-h-20 whitespace-pre-line
               rounded-xl border border-slate-200
               bg-slate-50 p-4 text-sm text-slate-600">
                </p>
            </div>
            {{-- Acciones de la cita --}}
            <div
                class="mt-6 grid gap-3
           sm:grid-cols-2 lg:grid-cols-4">

                <a
                    id="detalle-cita-ficha"
                    href="#"
                    class="inline-flex items-center justify-center
               rounded-xl bg-[#315F9F] px-4 py-3
               text-sm font-bold text-white transition
               hover:bg-[#274D82]">
                    Ficha del paciente
                </a>

                <a
                    id="detalle-cita-editar"
                    href="#"
                    class="inline-flex items-center justify-center
               rounded-xl border border-[#AFCBFA]
               bg-[#EAF2FF] px-4 py-3
               text-sm font-bold text-[#315F9F]
               transition hover:bg-[#AFCBFA]">
                    Modificar cita
                </a>

                <a
                    id="detalle-cita-todas"
                    href="#"
                    class="inline-flex items-center justify-center
               rounded-xl bg-[#684B9D] px-4 py-3
               text-sm font-bold text-white transition
               hover:bg-[#543D80]">
                    Todas sus citas
                </a>

                <a
                    id="detalle-cita-detalle"
                    href="#"
                    class="inline-flex items-center justify-center
               rounded-xl border border-slate-300
               bg-white px-4 py-3
               text-sm font-bold text-slate-700
               transition hover:bg-slate-100">
                    Detalle de la cita
                </a>
                
            </div>
            <a
                id="detalle-cita-whatsapp"
                href="#"
                target="_blank"
                rel="noopener noreferrer"
                class="mt-3 hidden w-full items-center
           justify-center rounded-xl
           border border-[#A9D8BE]
           bg-[#EAF7F0] px-4 py-3
           text-sm font-bold text-[#347557]
           transition hover:bg-[#A9D8BE]/50">

                Recordar cita por WhatsApp

                <span
                    id="detalle-cita-telefono"
                    class="ml-1">
                </span>
            </a>
        </div>
    </div>
</div>
<script id="datos-citas-recepcion" type="application/json">
    @json($datosCitasRecepcion)
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modalDetalleCita =
            document.getElementById(
                'modal-detalle-cita'
            );

        const botonCerrarDetalle =
            document.getElementById(
                'cerrar-modal-detalle-cita'
            );

        const tarjetasCita =
            document.querySelectorAll(
                '.abrir-modal-detalle-cita'
            );

        const datosCitas =
            JSON.parse(
                document.getElementById(
                    'datos-citas-recepcion'
                ).textContent
            );

        const estilosEstado = {
            confirmada: 'rounded-xl bg-[#EAF7F0] p-4 text-[#347557]',

            en_espera: 'rounded-xl bg-[#FDECEC] p-4 text-[#A84848]',

            en_curso: 'rounded-xl bg-[#EAF2FF] p-4 text-[#315F9F]',

            en_consulta: 'rounded-xl bg-[#EAF2FF] p-4 text-[#315F9F]',

            finalizada: 'rounded-xl bg-slate-100 p-4 text-slate-600',

            cancelada: 'rounded-xl bg-[#FDECEC] p-4 text-[#A84848]',

            programada: 'rounded-xl bg-[#F2EDFC] p-4 text-[#684B9D]',
        };

        /**
         * Abrir el modal de detalle.
         */
        function abrirModalDetalle(evento) {
            evento.preventDefault();

            const citaId =
                evento.currentTarget.dataset.citaId;

            const cita = datosCitas[citaId];

            if (!cita) {
                return;
            }

            document.getElementById(
                    'detalle-cita-encabezado'
                ).textContent =
                `${cita.fecha} · ${cita.hora}`;

            document.getElementById(
                    'detalle-cita-titulo'
                ).textContent =
                cita.motivo;

            document.getElementById(
                    'detalle-cita-paciente'
                ).textContent =
                cita.paciente;

            document.getElementById(
                    'detalle-cita-medico'
                ).textContent =
                `Dr. ${cita.medico}`;

            document.getElementById(
                    'detalle-cita-horario'
                ).textContent =
                `${cita.hora} – ${cita.hora_fin}`;

            document.getElementById(
                    'detalle-cita-duracion'
                ).textContent =
                cita.duracion;

            document.getElementById(
                    'detalle-cita-especialidad'
                ).textContent =
                cita.especialidad;

            const fotografia =
                document.getElementById(
                    'detalle-cita-foto'
                );

            fotografia.onerror = () => {
                fotografia.onerror = null;

                fotografia.src =
                    fotografia.dataset.fotoDefault;
            };

            fotografia.src =
                cita.foto ||
                fotografia.dataset.fotoDefault;

            fotografia.alt =
                `Fotografía de ${cita.paciente}`;

            const estadoContenedor =
                document.getElementById(
                    'detalle-cita-estado-contenedor'
                );

            estadoContenedor.className =
                estilosEstado[cita.estado] ||
                estilosEstado.programada;

            document.getElementById(
                    'detalle-cita-estado'
                ).textContent =
                cita.estado_texto;

            document.getElementById(
                    'detalle-cita-modalidad'
                ).textContent =
                cita.modalidad_texto;

            const direccionContenedor =
                document.getElementById(
                    'detalle-cita-direccion-contenedor'
                );

            const mostrarDireccion =
                Boolean(cita.direccion);

            direccionContenedor.classList.toggle(
                'hidden',
                !mostrarDireccion
            );

            document.getElementById(
                    'detalle-cita-direccion'
                ).textContent =
                cita.direccion || '';

            const alergiasRegistradas =
                String(cita.alergias || '').trim();

            const alergiasContenedor =
                document.getElementById(
                    'detalle-cita-alergias-contenedor'
                );

            alergiasContenedor.classList.toggle(
                'alergias-activas',
                alergiasRegistradas.length > 0
            );

            document.getElementById(
                    'detalle-cita-alergias'
                ).textContent =
                alergiasRegistradas ||
                'Sin alergias registradas.';

            document.getElementById(
                    'detalle-cita-notas'
                ).textContent =
                cita.notas ||
                'Sin notas registradas.';

            const botonFicha =
                document.getElementById(
                    'detalle-cita-ficha'
                );

            botonFicha.href =
                cita.ficha_url ||
                cita.detalle_url;

            document.getElementById(
                    'detalle-cita-editar'
                ).href =
                cita.editar_url;

            document.getElementById(
                    'detalle-cita-todas'
                ).href =
                cita.citas_url;

            document.getElementById(
                    'detalle-cita-detalle'
                ).href =
                cita.detalle_url;

            const botonWhatsApp =
                document.getElementById(
                    'detalle-cita-whatsapp'
                );

            const telefonoLimpio =
                String(cita.telefono || '')
                .replace(/\D/g, '');

            const telefonoWhatsApp =
                telefonoLimpio.length === 10 ?
                `52${telefonoLimpio}` :
                telefonoLimpio;

            const mensajeWhatsApp =
                encodeURIComponent(
                    `Hola ${cita.paciente}, ` +
                    `le recordamos su cita del ` +
                    `${cita.fecha} a las ${cita.hora}.`
                );

            const mostrarWhatsApp =
                telefonoWhatsApp.length > 0;

            botonWhatsApp.classList.toggle(
                'hidden',
                !mostrarWhatsApp
            );

            botonWhatsApp.classList.toggle(
                'flex',
                mostrarWhatsApp
            );

            botonWhatsApp.href =
                mostrarWhatsApp ?
                `https://wa.me/${telefonoWhatsApp}` +
                `?text=${mensajeWhatsApp}` :
                '#';

            document.getElementById(
                    'detalle-cita-telefono'
                ).textContent =
                mostrarWhatsApp ?
                `(${cita.telefono})` :
                '';

            modalDetalleCita.classList.remove(
                'hidden'
            );

            modalDetalleCita.classList.add(
                'flex'
            );

            document.body.classList.add(
                'overflow-hidden'
            );

            botonCerrarDetalle.focus();
        }

        /**
         * Cerrar el modal y restaurar la página.
         */
        function cerrarModalDetalle() {
            modalDetalleCita.classList.add(
                'hidden'
            );

            modalDetalleCita.classList.remove(
                'flex'
            );

            document.body.classList.remove(
                'overflow-hidden'
            );
        }

        tarjetasCita.forEach(tarjeta => {
            tarjeta.addEventListener(
                'click',
                abrirModalDetalle
            );

            tarjeta.addEventListener(
                'keydown',
                evento => {
                    if (
                        evento.key === 'Enter' ||
                        evento.key === ' '
                    ) {
                        abrirModalDetalle(evento);
                    }
                }
            );
        });

        botonCerrarDetalle.addEventListener(
            'click',
            cerrarModalDetalle
        );

        /*
         * Cerrar cuando se pulsa el fondo oscuro.
         */
        modalDetalleCita.addEventListener(
            'click',
            evento => {
                if (evento.target === modalDetalleCita) {
                    cerrarModalDetalle();
                }
            }
        );

        /*
         * Cerrar con la tecla Escape.
         */
        document.addEventListener(
            'keydown',
            evento => {
                if (
                    evento.key === 'Escape' &&
                    !modalDetalleCita.classList
                    .contains('hidden')
                ) {
                    cerrarModalDetalle();
                }
            }
        );
    });
</script>