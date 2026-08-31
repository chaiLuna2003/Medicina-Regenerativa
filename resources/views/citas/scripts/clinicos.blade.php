<script>
    let modalClinicoActivo = null;
    let disparadorModalClinico = null;

    const selectorElementosEnfocables = [
        'a[href]',
        'button:not([disabled])',
        'input:not([disabled]):not([type="hidden"])',
        'select:not([disabled])',
        'textarea:not([disabled])',
        '[tabindex]:not([tabindex="-1"])'
    ].join(',');

    function elementosEnfocablesModal(
        modal
    ) {
        if (!modal) {
            return [];
        }

        return Array.from(
            modal.querySelectorAll(
                selectorElementosEnfocables
            )
        ).filter(
            function(elemento) {
                return elemento.offsetParent !== null
                    && elemento.getAttribute(
                        'aria-hidden'
                    ) !== 'true';
            }
        );
    }

    function enfocarInicioModal(
        modal
    ) {
        if (!modal) {
            return;
        }

        const focoPreferido =
            modal.querySelector(
                '[data-modal-clinico-focus]'
            );

        const botonCerrar =
            modal.querySelector(
                'button[data-cerrar-modal-clinico]'
            );

        const elementos =
            elementosEnfocablesModal(modal);

        const destino =
            focoPreferido
            || botonCerrar
            || elementos[0]
            || modal;

        if (destino === modal) {
            modal.setAttribute(
                'tabindex',
                '-1'
            );
        }

        window.setTimeout(
            function() {
                destino.focus({
                    preventScroll: true
                });
            },
            0
        );
    }

    function abrirModalClinico(
        nombre,
        disparador = null
    ) {
        const modal = document.querySelector(
            `[data-modal-clinico-panel="${nombre}"]`
        );

        if (!modal) {
            return;
        }

        if (
            modalClinicoActivo
            && modalClinicoActivo !== modal
        ) {
            cerrarModalClinico(
                modalClinicoActivo,
                false
            );
        }

        modalClinicoActivo = modal;

        disparadorModalClinico =
            disparador instanceof HTMLElement
                ? disparador
                : null;

        modal.classList.remove('hidden');

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );

        document.dispatchEvent(
            new CustomEvent(
                'modal-clinico:abierto',
                {
                    detail: {
                        nombre: nombre,
                        modal: modal
                    }
                }
            )
        );

        enfocarInicioModal(modal);
    }

    function cerrarModalClinico(
        modal = modalClinicoActivo,
        devolverFoco = true
    ) {
        if (!modal) {
            return;
        }

        modal.classList.add('hidden');

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        if (modal === modalClinicoActivo) {
            modalClinicoActivo = null;

            document.body.classList.remove(
                'overflow-hidden'
            );
        }

        const disparador =
            disparadorModalClinico;

        disparadorModalClinico = null;

        if (
            devolverFoco
            && disparador
            && disparador.isConnected
            && disparador.offsetParent !== null
        ) {
            window.setTimeout(
                function() {
                    disparador.focus({
                        preventScroll: true
                    });
                },
                0
            );
        }
    }

    function mantenerFocoDentroModal(
        event
    ) {
        if (
            event.key !== 'Tab'
            || !modalClinicoActivo
        ) {
            return;
        }

        const elementos =
            elementosEnfocablesModal(
                modalClinicoActivo
            );

        if (elementos.length === 0) {
            event.preventDefault();

            modalClinicoActivo.setAttribute(
                'tabindex',
                '-1'
            );

            modalClinicoActivo.focus();

            return;
        }

        const primero = elementos[0];

        const ultimo =
            elementos[
                elementos.length - 1
            ];

        const elementoActivo =
            document.activeElement;

        if (
            event.shiftKey
            && (
                elementoActivo === primero
                || !modalClinicoActivo.contains(
                    elementoActivo
                )
            )
        ) {
            event.preventDefault();
            ultimo.focus();

            return;
        }

        if (
            !event.shiftKey
            && (
                elementoActivo === ultimo
                || !modalClinicoActivo.contains(
                    elementoActivo
                )
            )
        ) {
            event.preventDefault();
            primero.focus();
        }
    }

    document.addEventListener(
        'click',
        function(event) {
            const disparador =
                event.target.closest(
                    '[data-modal-clinico]'
                );

            if (disparador) {
                abrirModalClinico(
                    disparador.dataset
                        .modalClinico,
                    disparador
                );

                return;
            }

            const botonCerrar =
                event.target.closest(
                    '[data-cerrar-modal-clinico]'
                );

            if (!botonCerrar) {
                return;
            }

            const modal =
                botonCerrar.closest(
                    '[data-modal-clinico-panel]'
                );

            cerrarModalClinico(modal);
        }
    );

    document.addEventListener(
        'keydown',
        function(event) {
            if (
                event.key === 'Escape'
                && modalClinicoActivo
            ) {
                event.preventDefault();

                cerrarModalClinico();

                return;
            }

            mantenerFocoDentroModal(
                event
            );
        }
    );
</script>