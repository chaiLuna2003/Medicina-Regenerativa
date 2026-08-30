<script>
    const modalEstudios = document.getElementById('modal-estudios');

    function abrirModalEstudios() {
        if (!modalEstudios) return;

        modalEstudios.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModalEstudios() {
        if (!modalEstudios) return;

        modalEstudios.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function mostrarArchivosSeleccionados(input) {
        const contenedor = document.getElementById(
            'lista-archivos-estudios'
        );

        if (!contenedor) return;

        contenedor.innerHTML = '';

        Array.from(input.files).forEach((archivo) => {
            const elemento = document.createElement('div');

            const megabytes =
                (archivo.size / 1024 / 1024).toFixed(2);

            elemento.className =
                'flex items-center justify-between ' +
                'rounded-lg border border-gray-200 ' +
                'bg-white px-3 py-2 text-sm';

            const nombre = document.createElement('span');

            nombre.className =
                'truncate font-medium text-gray-700';

            nombre.textContent = archivo.name;

            const peso = document.createElement('span');

            peso.className =
                'ml-3 shrink-0 text-xs text-gray-400';

            peso.textContent = megabytes + ' MB';

            elemento.appendChild(nombre);
            elemento.appendChild(peso);

            contenedor.appendChild(elemento);
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalEstudios();
        }
    });
</script>
@if (
$errors->has('nombre')
|| $errors->has('descripcion')
|| $errors->has('fecha_estudio')
|| $errors->has('archivos')
|| $errors->has('archivos.*')
)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        abrirModalEstudios();
    });
</script>
@endif