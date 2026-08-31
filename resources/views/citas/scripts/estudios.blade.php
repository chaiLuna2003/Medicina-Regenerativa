<script>
    function abrirModalEstudios(disparador = null) {
        const botonApertura =
            disparador instanceof HTMLElement
                ? disparador
                : document.querySelector(
                    '[data-abrir-estudios]'
                );

        abrirModalClinico(
            'estudios',
            botonApertura
        );
    }

    function cerrarModalEstudios() {
        const modal = document.getElementById(
            'modal-estudios'
        );

        if (!modal || modal.classList.contains('hidden')) {
            return;
        }

        cerrarModalClinico(modal);
    }

    function mostrarArchivosSeleccionados(input) {
        const contenedor = document.getElementById(
            'lista-archivos-estudios'
        );

        if (!contenedor) {
            return;
        }

        contenedor.replaceChildren();

        Array.from(input.files ?? []).forEach(function (archivo) {
            const elemento = document.createElement('div');

            elemento.className =
                'flex min-w-0 flex-col gap-1 ' +
                'rounded-lg border border-gray-200 ' +
                'bg-white px-3 py-2 text-sm';

            const nombre = document.createElement('span');

            nombre.className =
                'min-w-0 font-medium text-gray-700 ' +
                '[overflow-wrap:anywhere]';

            nombre.textContent = archivo.name;

            const peso = document.createElement('span');

            peso.className = 'text-xs text-gray-600';

            peso.textContent =
                (archivo.size / 1024 / 1024).toFixed(2)
                + ' MB';

            elemento.appendChild(nombre);
            elemento.appendChild(peso);
            contenedor.appendChild(elemento);
        });
    }
</script>

@if ($errors->getBag('estudiosCita')->any())
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {
                abrirModalEstudios();
            }
        );
    </script>
@endif