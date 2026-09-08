<script>
    function abrirModal(
        idModal,
        idPrimerCampo = null
    ) {
        const modal = document.getElementById(idModal);

        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');

        document.body.style.overflow = 'hidden';

        if (idPrimerCampo) {
            document
                .getElementById(idPrimerCampo)
                ?.focus();
        }
    }

    function cerrarModal(idModal) {
        const modal = document.getElementById(idModal);

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');

        document.body.style.overflow = '';
    }

    function abrirModalDatosGenerales() {
        abrirModal(
            'modal-datos-generales',
            'modal_nombre'
        );
    }

    function cerrarModalDatosGenerales() {
        cerrarModal('modal-datos-generales');
    }

    function abrirModalContacto() {
        abrirModal(
            'modal-contacto',
            'modal_telefono'
        );
    }

    function cerrarModalContacto() {
        cerrarModal('modal-contacto');
    }

    function abrirModalNotas() {
        abrirModal(
            'modal-notas',
            'modal_notas'
        );
    }

    function cerrarModalNotas() {
        cerrarModal('modal-notas');
    }

    const modalesEnfermeria = [
        {
            id: 'modal-datos-generales',
            cerrar: cerrarModalDatosGenerales,
        },
        {
            id: 'modal-contacto',
            cerrar: cerrarModalContacto,
        },
        {
            id: 'modal-notas',
            cerrar: cerrarModalNotas,
        },
    ];

    modalesEnfermeria.forEach(function(configuracion) {
        const modal = document.getElementById(
            configuracion.id
        );

        modal?.addEventListener(
            'click',
            function(evento) {
                if (evento.target === modal) {
                    configuracion.cerrar();
                }
            }
        );
    });

    document.addEventListener(
        'keydown',
        function(evento) {
            if (evento.key !== 'Escape') {
                return;
            }

            modalesEnfermeria.forEach(function(configuracion) {
                configuracion.cerrar();
            });
        }
    );
</script>