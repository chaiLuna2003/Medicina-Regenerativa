  @if (request()->user()->isAdmin())

    {{-- ===================================================== --}}
    {{-- MODAL: DATOS GENERALES --}}
    {{-- ===================================================== --}}
    <div
        id="modal-datos-generales"
        class="fixed inset-0 z-50 hidden
               items-center justify-center
               bg-slate-950/40 px-4 py-8
               backdrop-blur-[2px]"
        aria-hidden="true">

        <div
            class="flex max-h-[90vh] w-full max-w-3xl
                   flex-col overflow-hidden rounded-2xl
                   bg-white shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-modal-datos-generales">

            {{-- Encabezado --}}
            <div
                class="flex flex-shrink-0 items-start
                       justify-between border-b
                       border-slate-100 px-6 py-5">

                <div>
                    <h3
                        id="titulo-modal-datos-generales"
                        class="text-lg font-semibold text-slate-900">
                        Editar datos generales
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Actualiza la información personal y administrativa
                        del paciente.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalDatosGenerales()"
                    class="flex h-9 w-9 items-center
                           justify-center rounded-lg
                           text-slate-400 transition
                           hover:bg-slate-100
                           hover:text-slate-700"
                    aria-label="Cerrar modal">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('pacientes.update', $pacientes) }}"
                class="flex min-h-0 flex-1 flex-col">

                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="seccion"
                    value="generales">

                {{-- Contenido desplazable --}}
                <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6">

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        {{-- Nombre --}}
                        <div>
                            <label
                                for="modal_nombre"
                                class="mb-1.5 block text-sm
                                       font-medium text-slate-700">
                                Nombre
                            </label>

                            <input
                                id="modal_nombre"
                                name="nombre"
                                type="text"
                                value="{{ old(
                                    'nombre',
                                    $pacientes->nombre
                                ) }}"
                                required
                                maxlength="255"
                                autocomplete="given-name"
                                class="block w-full rounded-xl
                                       border-slate-300 text-sm
                                       shadow-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500">

                            @error('nombre')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Apellido --}}
                        <div>
                            <label
                                for="modal_apellido"
                                class="mb-1.5 block text-sm
                                       font-medium text-slate-700">
                                Apellido
                            </label>

                            <input
                                id="modal_apellido"
                                name="apellido"
                                type="text"
                                value="{{ old(
                                    'apellido',
                                    $pacientes->apellido
                                ) }}"
                                required
                                maxlength="255"
                                autocomplete="family-name"
                                class="block w-full rounded-xl
                                       border-slate-300 text-sm
                                       shadow-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500">

                            @error('apellido')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Fecha de nacimiento --}}
                        <div>
                            <label
                                for="modal_fecha_nacimiento"
                                class="mb-1.5 block text-sm
                                       font-medium text-slate-700">
                                Fecha de nacimiento
                            </label>

                            <input
                                id="modal_fecha_nacimiento"
                                name="fecha_nacimiento"
                                type="date"
                                value="{{ old(
                                    'fecha_nacimiento',
                                    $pacientes->fecha_nacimiento
                                        ?->format('Y-m-d')
                                ) }}"
                                max="{{ now()->format('Y-m-d') }}"
                                required
                                class="block w-full rounded-xl
                                       border-slate-300 text-sm
                                       shadow-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500">

                            @error('fecha_nacimiento')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Sexo --}}
                        <div>
                            <label
                                for="modal_sexo"
                                class="mb-1.5 block text-sm
                                       font-medium text-slate-700">
                                Sexo
                            </label>

                            <select
                                id="modal_sexo"
                                name="sexo"
                                required
                                class="block w-full rounded-xl
                                       border-slate-300 text-sm
                                       shadow-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500">

                                <option value="">
                                    Selecciona una opción
                                </option>

                                <option
                                    value="masculino"
                                    @selected(
                                    old( 'sexo' ,
                                    $pacientes->sexo
                                    ) === 'masculino'
                                    )>
                                    Masculino
                                </option>

                                <option
                                    value="femenino"
                                    @selected(
                                    old( 'sexo' ,
                                    $pacientes->sexo
                                    ) === 'femenino'
                                    )>
                                    Femenino
                                </option>
                            </select>

                            @error('sexo')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Categoría --}}
                        <div class="sm:col-span-2">
                            <label
                                for="modal_categoria"
                                class="mb-1.5 block text-sm
                                       font-medium text-slate-700">
                                Categoría
                            </label>

                            <select
                                id="modal_categoria"
                                name="categoria"
                                class="block w-full rounded-xl
                                       border-slate-300 text-sm
                                       shadow-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500">

                                <option
                                    value="sin_categoria"
                                    @selected(
                                    old( 'categoria' ,
                                    $pacientes->categoria
                                    ?? 'sin_categoria'
                                    ) === 'sin_categoria'
                                    )>
                                    Sin categoría
                                </option>

                                <option
                                    value="rotarios"
                                    @selected(
                                    old( 'categoria' ,
                                    $pacientes->categoria
                                    ) === 'rotarios'
                                    )>
                                    ROTARIOS
                                </option>

                                <option
                                    value="unidem"
                                    @selected(
                                    old( 'categoria' ,
                                    $pacientes->categoria
                                    ) === 'unidem'
                                    )>
                                    UNIDEM
                                </option>

                                <option
                                    value="alumnos_cucs"
                                    @selected(
                                    old( 'categoria' ,
                                    $pacientes->categoria
                                    ) === 'alumnos_cucs'
                                    )>
                                    ALUMNOS CUCS
                                </option>

                                <option
                                    value="trabajadores"
                                    @selected(
                                    old( 'categoria' ,
                                    $pacientes->categoria
                                    ) === 'trabajadores'
                                    )>
                                    TRABAJADORES
                                </option>

                                <option
                                    value="rotarios_20"
                                    @selected(
                                    old( 'categoria' ,
                                    $pacientes->categoria
                                    ) === 'rotarios_20'
                                    )>
                                    ROTARIOS 20%
                                </option>

                                <option
                                    value="donativo"
                                    @selected(
                                    old( 'categoria' ,
                                    $pacientes->categoria
                                    ) === 'donativo'
                                    )>
                                    DONATIVO
                                </option>

                                <option
                                    value="medicos_50"
                                    @selected(
                                    old( 'categoria' ,
                                    $pacientes->categoria
                                    ) === 'medicos_50'
                                    )>
                                    MÉDICOS 50% DESC.
                                </option>

                                <option
                                    value="unidem_20"
                                    @selected(
                                    old( 'categoria' ,
                                    $pacientes->categoria
                                    ) === 'unidem_20'
                                    )>
                                    UNIDEM 20%
                                </option>
                            </select>

                            @error('categoria')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Lugar de nacimiento --}}
                        <div class="sm:col-span-2">
                            <label
                                for="modal_lugar_nacimiento"
                                class="mb-1.5 block text-sm
                                       font-medium text-slate-700">
                                Lugar de nacimiento
                            </label>

                            <input
                                id="modal_lugar_nacimiento"
                                name="lugar_nacimiento"
                                type="text"
                                value="{{ old(
                                    'lugar_nacimiento',
                                    $pacientes->lugar_nacimiento
                                ) }}"
                                maxlength="200"
                                placeholder="Ej. Ciudad de México"
                                class="block w-full rounded-xl
                                       border-slate-300 text-sm
                                       shadow-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500">

                            @error('lugar_nacimiento')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Ocupación --}}
                        <div>
                            <label
                                for="modal_ocupacion"
                                class="mb-1.5 block text-sm
                                       font-medium text-slate-700">
                                Ocupación
                            </label>

                            <input
                                id="modal_ocupacion"
                                name="ocupacion"
                                type="text"
                                value="{{ old(
                                    'ocupacion',
                                    $pacientes->ocupacion
                                ) }}"
                                maxlength="200"
                                placeholder="Ocupación"
                                class="block w-full rounded-xl
                                       border-slate-300 text-sm
                                       shadow-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500">

                            @error('ocupacion')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Religión --}}
                        <div>
                            <label
                                for="modal_religion"
                                class="mb-1.5 block text-sm
                                       font-medium text-slate-700">
                                Religión
                            </label>

                            <input
                                id="modal_religion"
                                name="religion"
                                type="text"
                                value="{{ old(
                                    'religion',
                                    $pacientes->religion
                                ) }}"
                                maxlength="150"
                                placeholder="Religión"
                                class="block w-full rounded-xl
                                       border-slate-300 text-sm
                                       shadow-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500">

                            @error('religion')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label
                                for="modal_status"
                                class="mb-1.5 block text-sm
                                       font-medium text-slate-700">
                                Estado del paciente
                            </label>

                            <select
                                id="modal_status"
                                name="status"
                                class="block w-full rounded-xl
                                       border-slate-300 text-sm
                                       shadow-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500">

                                <option
                                    value="1"
                                    @selected(
                                    (string) old( 'status' ,
                                    $pacientes->status
                                    ) === '1'
                                    )>
                                    Activo
                                </option>

                                <option
                                    value="0"
                                    @selected(
                                    (string) old( 'status' ,
                                    $pacientes->status
                                    ) === '0'
                                    )>
                                    Inactivo
                                </option>
                            </select>

                            @error('status')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Paciente finado --}}
                        <div>
                            <span
                                class="mb-1.5 block text-sm
                                       font-medium text-slate-700">
                                Situación del paciente
                            </span>

                            {{-- Permite enviar 0 cuando está desmarcado --}}
                            <input
                                type="hidden"
                                name="finado"
                                value="0">

                            <label
                                for="modal_finado"
                                class="flex min-h-[42px] cursor-pointer
                                       items-center gap-3 rounded-xl
                                       border border-slate-300
                                       px-4 py-2.5">

                                <input
                                    id="modal_finado"
                                    name="finado"
                                    type="checkbox"
                                    value="1"
                                    @checked(
                                    (bool) old( 'finado' ,
                                    $pacientes->finado
                                )
                                )
                                class="rounded border-slate-300
                                text-blue-600
                                focus:ring-blue-500">

                                <span class="text-sm text-slate-700">
                                    Marcar como finado
                                </span>
                            </label>

                            @error('finado')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                    </div>

                    {{-- Aviso --}}
                    <div
                        class="mt-5 rounded-xl border border-blue-100
                               bg-blue-50 px-4 py-3">

                        <p class="text-xs leading-5 text-blue-700">
                            La edad se recalcula automáticamente con la
                            fecha de nacimiento. El historial clínico se
                            conserva aunque el paciente quede inactivo.
                        </p>
                    </div>
                </div>

                {{-- Acciones --}}
                <div
                    class="flex flex-shrink-0 items-center
                           justify-end gap-3 border-t
                           border-slate-100 bg-slate-50
                           px-6 py-4">

                    <button
                        type="button"
                        onclick="cerrarModalDatosGenerales()"
                        class="rounded-xl border border-slate-300
                               bg-white px-4 py-2.5
                               text-sm font-semibold text-slate-700
                               transition hover:bg-slate-50">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600
                               px-5 py-2.5 text-sm
                               font-semibold text-white
                               shadow-sm transition
                               hover:bg-blue-700
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500
                               focus:ring-offset-2">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endif