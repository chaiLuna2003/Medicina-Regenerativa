<script>
    let modalClinicoActivo = null;
    let disparadorModalClinico = null;

    function abrirModalClinico(nombre, disparador = null) {
        const modal = document.querySelector(
            `[data-modal-clinico-panel="${nombre}"]`
        );

        if (!modal) {
            return;
        }

        if (
            modalClinicoActivo &&
            modalClinicoActivo !== modal
        ) {
            cerrarModalClinico(
                modalClinicoActivo,
                false
            );
        }

        modalClinicoActivo = modal;
        disparadorModalClinico = disparador;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        document.dispatchEvent(
            new CustomEvent(
                'modal-clinico:abierto', {
                    detail: {
                        nombre: nombre,
                        modal: modal
                    }
                }
            )
        );  

        const botonCerrar = modal.querySelector(
            '[data-cerrar-modal-clinico]'
        );

        if (botonCerrar) {
            window.setTimeout(
                () => botonCerrar.focus(),
                0
            );
        }
    }

    function cerrarModalClinico(
        modal = modalClinicoActivo,
        devolverFoco = true
    ) {
        if (!modal) {
            return;
        }

        modal.classList.add('hidden');

        if (modal === modalClinicoActivo) {
            modalClinicoActivo = null;
            document.body.classList.remove(
                'overflow-hidden'
            );
        }

        if (
            devolverFoco &&
            disparadorModalClinico
        ) {
            disparadorModalClinico.focus();
        }

        disparadorModalClinico = null;
    }

    document.addEventListener(
        'click',
        function(event) {
            const disparador = event.target.closest(
                '[data-modal-clinico]'
            );

            if (disparador) {
                abrirModalClinico(
                    disparador.dataset.modalClinico,
                    disparador
                );

                return;
            }

            const botonCerrar = event.target.closest(
                '[data-cerrar-modal-clinico]'
            );

            if (!botonCerrar) {
                return;
            }

            const modal = botonCerrar.closest(
                '[data-modal-clinico-panel]'
            );

            cerrarModalClinico(modal);
        }
    );

    document.addEventListener(
        'keydown',
        function(event) {
            if (
                event.key === 'Escape' &&
                modalClinicoActivo
            ) {
                cerrarModalClinico();
            }
        }
    );
</script>