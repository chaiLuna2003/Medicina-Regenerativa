@if ($puedeEditarCita)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.querySelector(
            '[data-modal-edicion-cita]'
        );

        const botonAbrir = document.querySelector(
            '[data-abrir-modal-edicion-cita]'
        );

        if (!modal || !botonAbrir) {
            return;
        }

        const formulario = modal.querySelector(
            '[data-formulario-edicion-cita]'
        );

        const medico = formulario.querySelector(
            '[name="medico_id"]'
        );

        const fecha = formulario.querySelector(
            '[name="fecha"]'
        );

        const hora = formulario.querySelector(
            '[name="hora"]'
        );

        const duracion = formulario.querySelector(
            '[name="duracion_minutos"]'
        );

        const modalidad = formulario.querySelector(
            '[name="modalidad"]'
        );

        const contenedorDireccion = modal.querySelector(
            '[data-contenedor-direccion-edicion]'
        );

        const direccion = formulario.querySelector(
            '[name="direccion_cita"]'
        );

        const mensajeHorarios = modal.querySelector(
            '[data-mensaje-horarios-edicion]'
        );

        const mensajeDuracion = modal.querySelector(
            '[data-mensaje-duracion-edicion]'
        );

        const botonesCerrar = modal.querySelectorAll(
            '[data-cerrar-modal-edicion-cita]'
        );

        const urlHorarios = @json(
            route(
                'citas.horarios-disponibles',
                [],
                false
            )
        );

        const citaId = @json($cita->id);

        let bloquesHorarios = [];
        let solicitudHorarios = null;
        let elementoConFoco = null;

        function abrirModal() {
            elementoConFoco =
                document.activeElement;

            modal.classList.remove('hidden');
            modal.setAttribute(
                'aria-hidden',
                'false'
            );

            botonAbrir.setAttribute(
                'aria-expanded',
                'true'
            );

            document.body.classList.add(
                'overflow-hidden'
            );

            cargarHorarios();

            window.setTimeout(() => {
                formulario
                    .querySelector(
                        'select, input, textarea, button'
                    )
                    ?.focus();
            }, 50);
        }

        function cerrarModal() {
            modal.classList.add('hidden');
            modal.setAttribute(
                'aria-hidden',
                'true'
            );

            botonAbrir.setAttribute(
                'aria-expanded',
                'false'
            );

            document.body.classList.remove(
                'overflow-hidden'
            );

            solicitudHorarios?.abort();

            elementoConFoco?.focus();
        }

        function actualizarDireccion() {
            const requiereDireccion =
                modalidad.value ===
                'fuera_instalaciones';

            contenedorDireccion.classList.toggle(
                'hidden',
                !requiereDireccion
            );

            direccion.required =
                requiereDireccion;

            if (!requiereDireccion) {
                direccion.value = '';
            }
        }

        function formatearHoraFinal(
            horaInicio,
            duracionMinutos
        ) {
            const [horas, minutos] =
                horaInicio
                    .split(':')
                    .map(Number);

            const fechaHora = new Date();

            fechaHora.setHours(
                horas,
                minutos + duracionMinutos,
                0,
                0
            );

            return fechaHora.toLocaleTimeString(
                'es-MX',
                {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true,
                }
            );
        }

        function generarDuraciones() {
            const horaSeleccionada =
                hora.value;

            duracion.innerHTML = '';

            if (
                !horaSeleccionada ||
                bloquesHorarios.length === 0
            ) {
                duracion.disabled = true;

                duracion.innerHTML =
                    '<option value="">' +
                    'Selecciona primero la hora' +
                    '</option>';

                return;
            }

            const indiceInicio =
                bloquesHorarios.findIndex(
                    (bloque) =>
                        bloque.hora ===
                        horaSeleccionada
                );

            if (indiceInicio === -1) {
                duracion.disabled = true;

                duracion.innerHTML =
                    '<option value="">' +
                    'Horario no disponible' +
                    '</option>';

                return;
            }

            const valorAnterior = Number(
                duracion.dataset
                    .valorAnterior || 15
            );

            for (
                let cantidad = 1;
                indiceInicio + cantidad <=
                    bloquesHorarios.length;
                cantidad++
            ) {
                const bloques =
                    bloquesHorarios.slice(
                        indiceInicio,
                        indiceInicio + cantidad
                    );

                if (
                    bloques.length !== cantidad ||
                    !bloques.every(
                        (bloque) =>
                            bloque.disponible
                    )
                ) {
                    break;
                }

                const minutos =
                    cantidad * 15;

                const opcion =
                    document.createElement(
                        'option'
                    );

                opcion.value =
                    String(minutos);

                opcion.textContent =
                    formatearHoraFinal(
                        horaSeleccionada,
                        minutos
                    ) +
                    ' — ' +
                    minutos +
                    ' minutos';

                opcion.selected =
                    minutos === valorAnterior;

                duracion.appendChild(opcion);
            }

            if (
                duracion.options.length === 0
            ) {
                duracion.disabled = true;

                duracion.innerHTML =
                    '<option value="">' +
                    'No hay duración disponible' +
                    '</option>';

                mensajeDuracion.textContent =
                    'El siguiente bloque está ocupado.';

                return;
            }

            if (!duracion.value) {
                duracion.options[0].selected =
                    true;
            }

            duracion.disabled = false;

            mensajeDuracion.textContent =
                'La cita terminará a las ' +
                formatearHoraFinal(
                    horaSeleccionada,
                    Number(duracion.value)
                ) +
                '.';
        }

        async function cargarHorarios() {
            if (
                !medico.value ||
                !fecha.value
            ) {
                hora.disabled = true;
                duracion.disabled = true;

                mensajeHorarios.textContent =
                    'Selecciona médico y fecha.';

                return;
            }

            solicitudHorarios?.abort();

            solicitudHorarios =
                new AbortController();

            const valorAnterior =
                hora.dataset.valorAnterior;

            hora.disabled = true;

            mensajeHorarios.textContent =
                'Consultando disponibilidad...';

            try {
                const url = new URL(
                    urlHorarios,
                    window.location.origin
                );

                url.searchParams.set(
                    'medico_id',
                    medico.value
                );

                url.searchParams.set(
                    'fecha',
                    fecha.value
                );

                url.searchParams.set(
                    'ignorar_cita',
                    String(citaId)
                );

                const respuesta = await fetch(
                    url,
                    {
                        headers: {
                            Accept:
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest',
                        },

                        signal:
                            solicitudHorarios.signal,
                    }
                );

                if (!respuesta.ok) {
                    throw new Error(
                        'No se pudieron cargar '
                        + 'los horarios.'
                    );
                }

                const datos =
                    await respuesta.json();

                bloquesHorarios =
                    Array.isArray(datos.horarios)
                        ? datos.horarios
                        : [];

                const disponibles =
                    bloquesHorarios.filter(
                        (bloque) =>
                            bloque.disponible
                    );

                hora.innerHTML = '';

                disponibles.forEach(
                    (bloque) => {
                        const opcion =
                            document.createElement(
                                'option'
                            );

                        opcion.value =
                            bloque.hora;

                        opcion.textContent =
                            bloque.texto;

                        opcion.selected =
                            bloque.hora ===
                            valorAnterior;

                        hora.appendChild(
                            opcion
                        );
                    }
                );

                if (
                    hora.options.length === 0
                ) {
                    hora.disabled = true;

                    hora.innerHTML =
                        '<option value="">' +
                        'No hay horarios disponibles' +
                        '</option>';

                    mensajeHorarios.textContent =
                        'La agenda está llena '
                        + 'para esta fecha.';

                    bloquesHorarios = [];

                    generarDuraciones();

                    return;
                }

                if (!hora.value) {
                    hora.options[0].selected =
                        true;
                }

                hora.disabled = false;

                mensajeHorarios.textContent =
                    'Selecciona un horario disponible.';

                generarDuraciones();
            } catch (error) {
                if (
                    error.name ===
                    'AbortError'
                ) {
                    return;
                }

                bloquesHorarios = [];
                hora.disabled = false;

                mensajeHorarios.textContent =
                    'No fue posible consultar '
                    + 'los horarios. Intenta nuevamente.';
            }
        }

        botonAbrir.addEventListener(
            'click',
            abrirModal
        );

        botonesCerrar.forEach(
            (boton) => {
                boton.addEventListener(
                    'click',
                    cerrarModal
                );
            }
        );

        modalidad.addEventListener(
            'change',
            actualizarDireccion
        );

        medico.addEventListener(
            'change',
            () => {
                hora.dataset.valorAnterior = '';
                cargarHorarios();
            }
        );

        fecha.addEventListener(
            'change',
            () => {
                hora.dataset.valorAnterior = '';
                cargarHorarios();
            }
        );

        hora.addEventListener(
            'change',
            () => {
                hora.dataset.valorAnterior =
                    hora.value;

                generarDuraciones();
            }
        );

        duracion.addEventListener(
            'change',
            () => {
                duracion.dataset.valorAnterior =
                    duracion.value;

                generarDuraciones();
            }
        );

        document.addEventListener(
            'keydown',
            (event) => {
                if (
                    event.key === 'Escape' &&
                    !modal.classList.contains(
                        'hidden'
                    )
                ) {
                    cerrarModal();
                }
            }
        );

        actualizarDireccion();

        if (
            modal.dataset.abrirAlCargar ===
            'true'
        ) {
            abrirModal();
        }
    });
</script>
@endif