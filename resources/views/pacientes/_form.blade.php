@php
    $edicionLimitadaRecepcion = isset($pacientes)
        && auth()->user()->isRecepcionista();
@endphp

@unless ($edicionLimitadaRecepcion)
<div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 mb-6 pb-6 border-b border-gray-100">
    <img id="preview-foto"
         src="{{ isset($pacientes) ? $pacientes->fotoUrl() : asset('images/default.webp') }}"
         class="w-20 h-20 rounded-full object-cover border border-gray-200 shrink-0">

    <div class="w-full">
        <label class="block text-sm font-medium text-gray-700 mb-1">Foto de perfil</label>
        <input type="file" name="foto" accept="image/*" capture="environment"
               onchange="document.getElementById('preview-foto').src = URL.createObjectURL(this.files[0])"
               class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                      file:bg-emerald-50 file:text-emerald-700 file:text-sm file:font-medium
                      hover:file:bg-emerald-100">
        @error('foto') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
</div>
@endunless

<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Datos personales</h3>
@if ($edicionLimitadaRecepcion)
<div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 p-5">
    <div class="flex items-center gap-4">
        <img src="{{ $pacientes->fotoUrl() }}"
             alt="Foto de {{ $pacientes->nombre }}"
             class="h-16 w-16 shrink-0 rounded-full border border-slate-200 object-cover">

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                Paciente
            </p>
            <p class="mt-1 text-lg font-semibold text-slate-800">
                {{ $pacientes->nombre }} {{ $pacientes->apellido }}
            </p>
            <p class="mt-1 text-sm text-slate-600">
                Nacimiento: {{ $pacientes->fecha_nacimiento?->format('d/m/Y') ?? 'No registrada' }}
                <span class="mx-1 text-slate-300">•</span>
                Edad: {{ $pacientes->edad ?? 'No disponible' }}
            </p>
        </div>
    </div>

    <p class="mt-4 text-sm text-slate-500">
        Los datos personales solo pueden ser modificados por personal autorizado.
    </p>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $pacientes->nombre ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('nombre') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
        <input type="text" name="apellido" value="{{ old('apellido', $pacientes->apellido ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('apellido') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    

    <div>
    <label
        for="fecha_nacimiento"
        class="block text-sm font-medium text-slate-700"
    >
        Fecha de nacimiento
    </label>

    <input
        type="date"
        id="fecha_nacimiento"
        name="fecha_nacimiento"
        value="{{ old(
    'fecha_nacimiento',
    isset($pacientes) && $pacientes->fecha_nacimiento
        ? $pacientes->fecha_nacimiento->format('Y-m-d')
        : ''
) }}"
        max="{{ now()->format('Y-m-d') }}"
        required
        class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm
               focus:border-blue-500 focus:ring-blue-500"
    >

    <p
        id="edad_calculada"
        class="mt-2 text-sm text-slate-500"
    >
        Selecciona la fecha para calcular la edad.
    </p>

    @error('fecha_nacimiento')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>
</div>
@endif

<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Contacto</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $pacientes->telefono ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('telefono') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $pacientes->email ?? '') }}"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
</div>

@unless ($edicionLimitadaRecepcion)
<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Notas clínicas</h3>
<div class="mb-6">
    <textarea name="notas" rows="4" placeholder="Observaciones, antecedentes, etc."
              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">{{ old('notas', $pacientes->notas ?? '') }}</textarea>
    @error('notas') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>
@endunless

@unless ($edicionLimitadaRecepcion)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fechaInput = document.getElementById('fecha_nacimiento');
        const edadTexto = document.getElementById('edad_calculada');

        if (!fechaInput || !edadTexto) {
            return;
        }

        function calcularEdad() {
    if (!fechaInput.value) {
        edadTexto.textContent =
            'Selecciona la fecha para calcular la edad.';

        edadTexto.className = 'mt-2 text-sm text-slate-500';
        return;
    }

    const [anio, mes, dia] = fechaInput.value
        .split('-')
        .map(Number);

    const nacimiento = new Date(anio, mes - 1, dia);
    const hoy = new Date();

    nacimiento.setHours(0, 0, 0, 0);
    hoy.setHours(0, 0, 0, 0);

    if (nacimiento > hoy) {
        edadTexto.textContent =
            'La fecha de nacimiento no puede ser futura.';

        edadTexto.className =
            'mt-2 text-sm font-medium text-red-600';

        return;
    }

    let anios = hoy.getFullYear() - nacimiento.getFullYear();

    const aunNoCumple =
        hoy.getMonth() < nacimiento.getMonth() ||
        (
            hoy.getMonth() === nacimiento.getMonth() &&
            hoy.getDate() < nacimiento.getDate()
        );

    if (aunNoCumple) {
        anios--;
    }

    let descripcion;

    if (anios >= 1) {
        descripcion = anios === 1
            ? '1 año'
            : `${anios} años`;
    } else {
        let meses =
            (hoy.getFullYear() - nacimiento.getFullYear()) * 12 +
            hoy.getMonth() -
            nacimiento.getMonth();

        if (hoy.getDate() < nacimiento.getDate()) {
            meses--;
        }

        if (meses >= 1) {
            descripcion = meses === 1
                ? '1 mes'
                : `${meses} meses`;
        } else {
            const milisegundosPorDia = 1000 * 60 * 60 * 24;

            const dias = Math.floor(
                (hoy - nacimiento) / milisegundosPorDia
            );

            if (dias >= 14) {
                const semanas = Math.floor(dias / 7);

                descripcion = semanas === 1
                    ? '1 semana'
                    : `${semanas} semanas`;
            } else if (dias >= 1) {
                descripcion = dias === 1
                    ? '1 día de nacido'
                    : `${dias} días de nacido`;
            } else {
                descripcion = 'Recién nacido';
            }
        }
    }

    edadTexto.textContent = `Edad calculada: ${descripcion}`;
    edadTexto.className =
        'mt-2 text-sm font-medium text-blue-600';
}

        fechaInput.addEventListener('input', calcularEdad);
        calcularEdad();
    });
</script>
@endunless
