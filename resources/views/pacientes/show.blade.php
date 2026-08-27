<x-app-layout>

    <x-slot name="header">
        @include(
        'pacientes.sections.encabezado'
        )
    </x-slot>

    @include(
    'pacientes.sections.mensajes-sistema'
    )

    <div class="py-8">
        <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

                {{-- ========================================================= --}}
                {{-- COLUMNA IZQUIERDA --}}
                {{-- ========================================================= --}}
                <aside class="space-y-4 lg:col-span-4">

                    @include(
                    'pacientes.sections.perfil'
                    )

                    @include(
                    'pacientes.sections.datos-generales'
                    )

                    @include(
                    'pacientes.sections.contacto'
                    )

                    @include(
                    'pacientes.sections.notas'
                    )

                </aside>

                {{-- ========================================================= --}}
                {{-- COLUMNA DERECHA --}}
                {{-- ========================================================= --}}

                <main class="space-y-4 lg:col-span-8">

                    @include(
                    'pacientes.sections.historia-clinica'
                    )

                    @include(
                    'pacientes.sections.antecedentes-heredofamiliares'
                    )


                    @include(
                    'pacientes.sections.antecedentes-personales-patologicos'
                    )

                    @include(
                    'pacientes.sections.antecedentes-personales-no-patologicos'
                    )

                    {{-- ===================================================== --}}
                    {{-- ANTECEDENTES GINECOOBSTÉTRICOS --}}
                    {{-- Solo se muestran para pacientes femeninas --}}
                    {{-- ===================================================== --}}

                    @if ($pacientes->sexo === 'femenino')
                    @include(
                    'pacientes.partials.ginecoobstetricos'
                    )
                    @endif

                    @include(
                    'pacientes.sections.habitos-alimenticios'
                    )

                    @include(
                    'pacientes.sections.exploraciones-fisicas'
                    )

                    @include(
                    'pacientes.sections.resumen-clinico'
                    )

                    @include(
                    'pacientes.sections.historial-citas'
                    )


                    @if (
                    request()->user()->isAdmin()
                    || request()->user()->isMedico()
                    )

                    @include(
                    'pacientes.sections.estudios-clinicos'
                    )

                    @include(
                    'pacientes.sections.recetas-medicas'
                    )

                    @endif


                    @include(
                    'pacientes.sections.signos-vitales'
                    )
                </main>
            </div>
        </div>
    </div>



    @include(
    'pacientes.modals.contacto'
    )

    @include(
    'pacientes.modals.datos-generales'
    )


    @include(
    'pacientes.modals.notas'
    )

@include(
    'pacientes.modals.historia-clinica'
)   

    @php
    $heredofamiliaresModal = $pacientes
    ->historiaClinica
    ?->antecedentesHeredofamiliares;

    $valoresHeredofamiliaresModal =
    $heredofamiliaresModal?->antecedentes
    ?? [];
    @endphp

    @if (
    request()->user()->isAdmin()
    || request()->user()->isMedico()
    )

    <div
        id="modal-heredofamiliares"
        class="fixed inset-0 z-50 hidden
           items-center justify-center
           bg-slate-950/50 p-4"
        aria-hidden="true"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-heredofamiliares">

        <div
            class="max-h-[90vh] w-full max-w-5xl
               overflow-y-auto rounded-2xl
               bg-white shadow-2xl">

            <div
                class="sticky top-0 z-10
                   flex items-start justify-between
                   border-b border-slate-100
                   bg-white px-6 py-5">

                <div>
                    <h3
                        id="titulo-modal-heredofamiliares"
                        class="text-lg font-semibold text-slate-900">
                        Antecedentes heredofamiliares
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Escribe “negado” o especifica quién presentó
                        el padecimiento.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalHeredofamiliares()"
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
                action="{{ route(
                'pacientes.historia-clinica.'
                . 'heredofamiliares.update',
                $pacientes
            ) }}">

                @csrf
                @method('PUT')

                <div
                    class="grid grid-cols-1 gap-4 p-6
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4">

                    {{-- Número de hermanos --}}
                    <div>
                        <label
                            for="numero_hermanos"
                            class="mb-1.5 block
                               text-sm font-medium
                               text-slate-700">
                            Hermanos
                        </label>

                        <input
                            id="numero_hermanos"
                            name="numero_hermanos"
                            type="number"
                            min="0"
                            max="100"
                            value="{{ old(
                            'numero_hermanos',
                            $heredofamiliaresModal?->numero_hermanos
                        ) }}"
                            class="block w-full rounded-xl
                               border-slate-300 text-sm
                               shadow-sm
                               focus:border-emerald-500
                               focus:ring-emerald-500">

                        @error('numero_hermanos', 'heredofamiliares')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Antecedentes --}}
                    @foreach ($camposHeredofamiliares as $clave => $etiqueta)

                    <div>
                        <label
                            for="antecedente_{{ $clave }}"
                            class="mb-1.5 block
                                   text-sm font-medium
                                   text-slate-700">
                            {{ $etiqueta }}
                        </label>

                        <input
                            id="antecedente_{{ $clave }}"
                            name="antecedentes[{{ $clave }}]"
                            type="text"
                            maxlength="1000"
                            value="{{ old(
    "antecedentes.{$clave}",
    data_get(
        $valoresHeredofamiliaresModal,
        $clave
    )
) }}"
                            placeholder="Negado o especifique"
                            class="block w-full rounded-xl
                                   border-slate-300 text-sm
                                   shadow-sm
                                   focus:border-emerald-500
                                   focus:ring-emerald-500">

                        @error(
                        "antecedentes.{$clave}",
                        'heredofamiliares'
                        )
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    @endforeach
                </div>

                <div
                    class="sticky bottom-0
                       flex items-center justify-end gap-3
                       border-t border-slate-100
                       bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="cerrarModalHeredofamiliares()"
                        class="rounded-xl border
                           border-slate-300 bg-white
                           px-4 py-2.5
                           text-sm font-semibold
                           text-slate-700 transition
                           hover:bg-slate-50">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-emerald-600
                           px-5 py-2.5
                           text-sm font-semibold
                           text-white shadow-sm
                           transition hover:bg-emerald-700">
                        Guardar antecedentes
                    </button>
                </div>
            </form>
        </div>
    </div>



    @endif

    @php
    $personalesPatologicosModal = $pacientes
    ->historiaClinica
    ?->antecedentesPersonalesPatologicos;

    $valoresPersonalesPatologicosModal =
    $personalesPatologicosModal?->antecedentes
    ?? [];
    @endphp

    @if (
    request()->user()->isAdmin()
    || request()->user()->isMedico()
    )

    <div
        id="modal-personales-patologicos"
        class="fixed inset-0 z-50 hidden
           items-center justify-center
           bg-slate-950/50 p-4"
        aria-hidden="true"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-personales-patologicos">

        <div
            class="max-h-[90vh] w-full max-w-5xl
               overflow-y-auto rounded-2xl
               bg-white shadow-2xl">

            <div
                class="sticky top-0 z-10
                   flex items-start justify-between
                   border-b border-slate-100
                   bg-white px-6 py-5">

                <div>
                    <h3
                        id="titulo-modal-personales-patologicos"
                        class="text-lg font-semibold text-slate-900">
                        Antecedentes personales patológicos
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Escribe “negado” o especifica el antecedente.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalPersonalesPatologicos()"
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
                action="{{ route(
                'pacientes.historia-clinica.'
                . 'personales-patologicos.update',
                $pacientes
            ) }}">

                @csrf
                @method('PUT')

                <div
                    class="grid grid-cols-1 gap-4 p-6
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4">

                    @foreach (
                    $camposPersonalesPatologicos
                    as $clave => $etiqueta
                    )

                    <div>
                        <label
                            for="personal_patologico_{{ $clave }}"
                            class="mb-1.5 block
                                   text-sm font-medium
                                   text-slate-700">
                            {{ $etiqueta }}
                        </label>

                        <input
                            id="personal_patologico_{{ $clave }}"
                            name="antecedentes[{{ $clave }}]"
                            type="text"
                            maxlength="1000"
                            value="{{ old(
    "antecedentes.{$clave}",
    data_get(
        $valoresPersonalesPatologicosModal,
        $clave
    ),
    'personalesPatologicos'
) }}"
                            placeholder="Negado o especifique"
                            class="block w-full rounded-xl
                                   border-slate-300 text-sm
                                   shadow-sm
                                   focus:border-amber-500
                                   focus:ring-amber-500">

                        @error(
                        "antecedentes.{$clave}",
                        'personalesPatologicos'
                        )
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    @endforeach
                </div>

                <div
                    class="sticky bottom-0
                       flex items-center justify-end gap-3
                       border-t border-slate-100
                       bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="cerrarModalPersonalesPatologicos()"
                        class="rounded-xl border
                           border-slate-300 bg-white
                           px-4 py-2.5
                           text-sm font-semibold
                           text-slate-700 transition
                           hover:bg-slate-50">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-amber-600
                           px-5 py-2.5
                           text-sm font-semibold
                           text-white shadow-sm
                           transition hover:bg-amber-700">
                        Guardar antecedentes
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endif

    @php
    $personalesNoPatologicosModal = $pacientes
    ->historiaClinica
    ?->antecedentesPersonalesNoPatologicos;

    $valoresPersonalesNoPatologicosModal =
    $personalesNoPatologicosModal?->antecedentes
    ?? [];
    @endphp

    @if (
    request()->user()->isAdmin()
    || request()->user()->isMedico()
    )

    <div
        id="modal-personales-no-patologicos"
        class="fixed inset-0 z-50 hidden
           items-center justify-center
           bg-slate-950/50 p-4"
        aria-hidden="true"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-personales-no-patologicos">

        <div
            class="max-h-[90vh] w-full max-w-4xl
               overflow-y-auto rounded-2xl
               bg-white shadow-2xl">

            <div
                class="sticky top-0 z-10
                   flex items-start justify-between
                   border-b border-slate-100
                   bg-white px-6 py-5">

                <div>
                    <h3
                        id="titulo-modal-personales-no-patologicos"
                        class="text-lg font-semibold text-slate-900">
                        Antecedentes personales no patológicos
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Registra hábitos y condiciones generales del paciente.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalPersonalesNoPatologicos()"
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
                action="{{ route(
                'pacientes.historia-clinica.'
                . 'personales-no-patologicos.update',
                $pacientes
            ) }}">

                @csrf
                @method('PUT')

                <div
                    class="grid grid-cols-1 gap-4 p-6
                       sm:grid-cols-2
                       lg:grid-cols-3">

                    @foreach (
                    $camposPersonalesNoPatologicos
                    as $clave => $etiqueta
                    )

                    <div>
                        <label
                            for="personal_no_patologico_{{ $clave }}"
                            class="mb-1.5 block
                                   text-sm font-medium
                                   text-slate-700">
                            {{ $etiqueta }}
                        </label>

                        <input
                            id="personal_no_patologico_{{ $clave }}"
                            name="antecedentes[{{ $clave }}]"
                            type="text"
                            maxlength="1000"
                            value="{{ old(
    "antecedentes.{$clave}",
    data_get(
        $valoresPersonalesNoPatologicosModal,
        $clave
    )
) }}"
                            placeholder="Escribe la información"
                            class="block w-full rounded-xl
                                   border-slate-300 text-sm
                                   shadow-sm
                                   focus:border-indigo-500
                                   focus:ring-indigo-500">

                        @error(
                        "antecedentes.{$clave}",
                        'personalesNoPatologicos'
                        )
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    @endforeach
                </div>

                <div
                    class="sticky bottom-0
                       flex items-center justify-end gap-3
                       border-t border-slate-100
                       bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="cerrarModalPersonalesNoPatologicos()"
                        class="rounded-xl border
                           border-slate-300 bg-white
                           px-4 py-2.5
                           text-sm font-semibold
                           text-slate-700 transition
                           hover:bg-slate-50">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-indigo-600
                           px-5 py-2.5
                           text-sm font-semibold
                           text-white shadow-sm
                           transition hover:bg-indigo-700">
                        Guardar antecedentes
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endif

    @php
    $habitoAlimenticioModal = $pacientes
    ->historiaClinica
    ?->habitoAlimenticio;

    $comidasRegistradasModal =
    $habitoAlimenticioModal?->comidas ?? [];

    $alimentosRegistradosModal =
    $habitoAlimenticioModal?->alimentos ?? [];
    @endphp

    @if (
    request()->user()->isAdmin()
    || request()->user()->isMedico()
    )
    <div
        id="modal-habitos-alimenticios"
        class="fixed inset-0 z-50 hidden
               items-center justify-center
               bg-slate-950/60 p-4"
        aria-hidden="true"
        onclick="
            if (event.target === this) {
                cerrarModalHabitosAlimenticios();
            }
        ">

        <div
            class="flex max-h-[90vh] w-full
                   max-w-6xl flex-col
                   overflow-hidden rounded-2xl
                   bg-white shadow-2xl">

            {{-- Encabezado --}}
            <div
                class="flex items-center justify-between
                       border-b border-slate-200
                       px-6 py-5">

                <div>
                    <h2
                        class="text-lg font-semibold
                               text-slate-900">
                        Hábitos alimenticios
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Registra las comidas habituales y la
                        frecuencia o cantidad de consumo.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalHabitosAlimenticios()"
                    class="rounded-lg p-2 text-slate-400
                           transition hover:bg-slate-100
                           hover:text-slate-700"
                    aria-label="Cerrar">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form
                method="POST"
                action="{{ route(
                    'pacientes.historia-clinica.'
                    . 'habitos-alimenticios.update',
                    $pacientes
                ) }}"
                class="flex min-h-0 flex-1 flex-col">

                @csrf
                @method('PUT')

                <div class="flex-1 overflow-y-auto p-6">

                    {{-- Errores generales --}}
                    @if (
                    $errors->habitosAlimenticios->any()
                    )
                    <div
                        class="mb-6 rounded-xl border
                                   border-red-200 bg-red-50
                                   px-4 py-3 text-sm text-red-700">

                        <p class="font-semibold">
                            Revisa los campos señalados.
                        </p>

                        <ul class="mt-2 list-disc pl-5">
                            @foreach (
                            $errors->habitosAlimenticios->all()
                            as $error
                            )
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Comidas --}}
                    <section>
                        <h3
                            class="text-sm font-semibold
                                   text-slate-900">
                            Comidas realizadas habitualmente
                        </h3>

                        <div
                            class="mt-4 grid grid-cols-2 gap-3
                                   sm:grid-cols-3
                                   lg:grid-cols-5">

                            @foreach (
                            $comidasHabitosAlimenticios
                            as $clave => $etiqueta
                            )
                            @php
                            $seleccionada = old(
                            "comidas.{$clave}",
                            data_get(
                            $comidasRegistradasModal,
                            $clave,
                            false
                            )
                            );
                            @endphp

                            <label
                                class="flex cursor-pointer
                                           items-center gap-3
                                           rounded-xl border
                                           border-slate-200
                                           bg-slate-50 p-4
                                           transition
                                           hover:border-indigo-300
                                           hover:bg-indigo-50">

                                <input
                                    id="habito_comida_{{ $clave }}"
                                    type="checkbox"
                                    name="comidas[{{ $clave }}]"
                                    value="1"
                                    @checked($seleccionada)
                                    class="h-4 w-4 rounded
                                               border-slate-300
                                               text-indigo-600
                                               focus:ring-indigo-500">

                                <span
                                    class="text-sm font-medium
                                               text-slate-700">
                                    {{ $etiqueta }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </section>

                    {{-- Alimentos --}}
                    <section class="mt-8">

                        <div>
                            <h3
                                class="text-sm font-semibold
                                       text-slate-900">
                                Frecuencia o cantidad de alimentos
                            </h3>

                            <p class="mt-1 text-xs text-slate-400">
                                Ejemplos: diario, ocasional,
                                2 veces por semana, 1.5 litros.
                            </p>
                        </div>

                        <div
                            class="mt-4 grid grid-cols-1 gap-4
                                   sm:grid-cols-2
                                   lg:grid-cols-3">

                            @foreach (
                            $camposHabitosAlimenticios
                            as $clave => $etiqueta
                            )
                            <div>
                                <label
                                    for="habito_alimento_{{ $clave }}"
                                    class="mb-1.5 block
                                               text-xs font-semibold
                                               text-slate-600">

                                    {{ $etiqueta }}
                                </label>

                                <input
                                    id="habito_alimento_{{ $clave }}"
                                    type="text"
                                    name="alimentos[{{ $clave }}]"
                                    value="{{ old(
    "alimentos.{$clave}",
    data_get(
        $alimentosRegistradosModal,
        $clave
    )
) }}"
                                    maxlength="500"
                                    placeholder="Frecuencia o cantidad"
                                    class="w-full rounded-xl
                                               border-slate-300
                                               text-sm shadow-sm
                                               focus:border-indigo-500
                                               focus:ring-indigo-500">

                                @error(
                                "alimentos.{$clave}",
                                'habitosAlimenticios'
                                )
                                <p
                                    class="mt-1 text-xs
                                                   text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                    </section>
                </div>

                {{-- Acciones --}}
                <div
                    class="flex justify-end gap-3
                           border-t border-slate-200
                           bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="
                            cerrarModalHabitosAlimenticios()
                        "
                        class="rounded-xl border
                               border-slate-300 bg-white
                               px-5 py-2.5 text-sm
                               font-semibold text-slate-700
                               transition hover:bg-slate-100">

                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-indigo-600
                               px-5 py-2.5 text-sm
                               font-semibold text-white
                               shadow-sm transition
                               hover:bg-indigo-700">

                        Guardar hábitos
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if (
    request()->user()->isMedico()
    && request()->user()->medico
    )
    @php
    $citasDisponiblesExploracion = $pacientes
    ->citas
    ->filter(function ($cita) {
    return (int) $cita->medico_id
    === (int) request()
    ->user()
    ->medico
    ->id
    && $cita->estado !== 'cancelada';
    })
    ->values();
    @endphp

    <div
        id="modal-exploracion-fisica"
        class="fixed inset-0 z-50 hidden
               items-center justify-center
               bg-slate-950/60 p-4"
        aria-hidden="true"
        onclick="
            if (event.target === this) {
                cerrarModalExploracionFisica();
            }
        ">

        <div
            class="flex max-h-[90vh] w-full
                   max-w-5xl flex-col
                   overflow-hidden rounded-2xl
                   bg-white shadow-2xl">

            {{-- Encabezado --}}
            <div
                class="flex items-center justify-between
                       border-b border-slate-200
                       px-6 py-5">

                <div>
                    <h2
                        class="text-lg font-semibold
                               text-slate-900">
                        Exploración física
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Selecciona una consulta para registrar
                        o actualizar su exploración.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalExploracionFisica()"
                    class="rounded-lg p-2 text-slate-400
                           transition hover:bg-slate-100
                           hover:text-slate-700"
                    aria-label="Cerrar">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form
                id="form-exploracion-fisica"
                method="POST"
                action="#"
                data-route-template="{{ route(
                    'citas.exploracion-fisica.update',
                    ['cita' => '__CITA__']
                ) }}"
                class="flex min-h-0 flex-1 flex-col">

                @csrf
                @method('PUT')

                <div class="flex-1 overflow-y-auto p-6">

                    @if ($errors->exploracionFisica->any())
                    <div
                        class="mb-6 rounded-xl border
                                   border-red-200 bg-red-50
                                   px-4 py-3 text-sm text-red-700">

                        <p class="font-semibold">
                            Revisa los campos señalados.
                        </p>

                        <ul class="mt-2 list-disc pl-5">
                            @foreach (
                            $errors->exploracionFisica->all()
                            as $error
                            )
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Selector de cita --}}
                    <div>
                        <label
                            for="exploracion_cita_id"
                            class="mb-1.5 block
                                   text-sm font-semibold
                                   text-slate-700">

                            Consulta
                        </label>

                        <select
                            id="exploracion_cita_id"
                            name="cita_seleccionada"
                            required
                            class="w-full rounded-xl
                                   border-slate-300
                                   text-sm shadow-sm
                                   focus:border-indigo-500
                                   focus:ring-indigo-500">

                            <option value="">
                                Selecciona una consulta
                            </option>

                            @foreach (
                            $citasDisponiblesExploracion
                            as $citaDisponible
                            )
                            <option
                                value="{{ $citaDisponible->id }}"
                                @selected(
                                old('cita_seleccionada')==$citaDisponible->id
                                )>

                                {{ $citaDisponible->fecha
                                        ? $citaDisponible
                                            ->fecha
                                            ->format('d/m/Y')
                                        : 'Sin fecha' }}

                                @if ($citaDisponible->hora)
                                ·
                                {{ \Carbon\Carbon::parse(
                                            $citaDisponible->hora
                                        )->format('H:i') }}
                                @endif

                                ·
                                {{ $citaDisponible->motivo_texto }}

                                {{ $citaDisponible->exploracionFisica
                                        ? '— Editar registro'
                                        : '— Nueva exploración' }}
                            </option>
                            @endforeach
                        </select>

                        @if ($citasDisponiblesExploracion->isEmpty())
                        <p
                            class="mt-2 text-sm
                                       font-medium text-amber-600">
                            No tienes consultas disponibles
                            con este paciente.
                        </p>
                        @endif
                    </div>

                    {{-- Signos vitales --}}
                    <section
                        id="resumen-signos-exploracion"
                        class="mt-6 hidden rounded-xl
                               border border-slate-200
                               bg-slate-50 p-4">

                        <p
                            class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-slate-500">
                            Signos vitales de la consulta
                        </p>

                        <div
                            class="mt-3 grid grid-cols-2 gap-3
                                   sm:grid-cols-3 lg:grid-cols-6">

                            <div>
                                <p class="text-xs text-slate-400">
                                    Peso
                                </p>
                                <p
                                    id="exploracion_signo_peso"
                                    class="text-sm font-semibold
                                           text-slate-800">
                                    —
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-400">
                                    T/A
                                </p>
                                <p
                                    id="exploracion_signo_presion"
                                    class="text-sm font-semibold
                                           text-slate-800">
                                    —
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-400">
                                    F.C.
                                </p>
                                <p
                                    id="exploracion_signo_fc"
                                    class="text-sm font-semibold
                                           text-slate-800">
                                    —
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-400">
                                    F.R.
                                </p>
                                <p
                                    id="exploracion_signo_fr"
                                    class="text-sm font-semibold
                                           text-slate-800">
                                    —
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-400">
                                    Temperatura
                                </p>
                                <p
                                    id="exploracion_signo_temperatura"
                                    class="text-sm font-semibold
                                           text-slate-800">
                                    —
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-400">
                                    SatO₂
                                </p>
                                <p
                                    id="exploracion_signo_saturacion"
                                    class="text-sm font-semibold
                                           text-slate-800">
                                    —
                                </p>
                            </div>
                        </div>

                        <p
                            id="exploracion_sin_signos"
                            class="mt-3 hidden text-sm text-amber-600">
                            Enfermería todavía no ha registrado
                            signos vitales para esta consulta.
                        </p>
                    </section>

                    {{-- Campos clínicos --}}
                    <div
                        class="mt-6 grid grid-cols-1 gap-5
                               lg:grid-cols-2">

                        @foreach (
                        $camposExploracionFisica
                        as $clave => $etiqueta
                        )
                        <div>
                            <label
                                for="exploracion_{{ $clave }}"
                                class="mb-1.5 block
                                           text-sm font-semibold
                                           text-slate-700">

                                {{ $etiqueta }}
                            </label>

                            <textarea
                                id="exploracion_{{ $clave }}"
                                name="{{ $clave }}"
                                rows="6"
                                maxlength="20000"
                                placeholder="Escribe la información clínica..."
                                class="w-full resize-y rounded-xl
                                           border-slate-300
                                           text-sm shadow-sm
                                           focus:border-indigo-500
                                           focus:ring-indigo-500">{{ old($clave) }}</textarea>

                            @error(
                            $clave,
                            'exploracionFisica'
                            )
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                        @endforeach
                    </div>

                    {{-- Sistemas y órganos --}}
                    <section class="mt-8">

                        <div>
                            <h3
                                class="text-sm font-semibold
                   text-slate-900">
                                Exploración por sistemas y órganos
                            </h3>

                            <p class="mt-1 text-xs text-slate-400">
                                Registra los hallazgos relevantes de cada sistema.
                                Los campos sin observaciones pueden permanecer vacíos.
                            </p>
                        </div>

                        <div
                            class="mt-4 grid grid-cols-1 gap-4
               md:grid-cols-2
               xl:grid-cols-3">

                            @foreach (
                            $sistemasExploracionFisica
                            as $clave => $etiqueta
                            )
                            @php
                            $inicialesSistema = collect(
                            preg_split(
                            '/\s+/',
                            $etiqueta
                            )
                            )
                            ->filter()
                            ->map(
                            fn ($palabra) =>
                            mb_strtoupper(
                            mb_substr($palabra, 0, 1)
                            )
                            )
                            ->take(2)
                            ->implode('');
                            @endphp

                            <article
                                class="overflow-hidden rounded-xl
                       border border-slate-200 bg-white
                       shadow-sm">

                                <div
                                    class="flex items-center gap-3
                           border-b border-slate-100
                           bg-slate-50 px-4 py-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0
                               items-center justify-center
                               rounded-xl bg-indigo-100
                               text-xs font-bold
                               text-indigo-700">

                                        {{ $inicialesSistema }}
                                    </div>

                                    <label
                                        for="exploracion_sistema_{{ $clave }}"
                                        class="text-sm font-semibold
                               text-slate-800">

                                        {{ $etiqueta }}
                                    </label>
                                </div>

                                <div class="p-4">

                                    <textarea
                                        id="exploracion_sistema_{{ $clave }}"
                                        name="sistemas[{{ $clave }}]"
                                        rows="4"
                                        maxlength="5000"
                                        data-exploracion-sistema="{{ $clave }}"
                                        placeholder="Hallazgos clínicos..."
                                        class="w-full resize-y rounded-xl
                               border-slate-300
                               text-sm shadow-sm
                               focus:border-indigo-500
                               focus:ring-indigo-500">{{ old(
                            "sistemas.{$clave}"
                        ) }}</textarea>

                                    @error(
                                    "sistemas.{$clave}",
                                    'exploracionFisica'
                                    )
                                    <p class="mt-1 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </article>
                            @endforeach
                        </div>
                    </section>
                </div>

                {{-- Acciones --}}
                <div
                    class="flex justify-end gap-3
                           border-t border-slate-200
                           bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="cerrarModalExploracionFisica()"
                        class="rounded-xl border
                               border-slate-300 bg-white
                               px-5 py-2.5 text-sm
                               font-semibold text-slate-700
                               transition hover:bg-slate-100">

                        Cancelar
                    </button>

                    <button
                        type="submit"
                        @disabled(
                        $citasDisponiblesExploracion->isEmpty()
                        )
                        class="rounded-xl bg-indigo-600
                        px-5 py-2.5 text-sm
                        font-semibold text-white
                        shadow-sm transition
                        hover:bg-indigo-700
                        disabled:cursor-not-allowed
                        disabled:opacity-50">

                        Guardar exploración
                    </button>
                </div>
            </form>
        </div>
    </div>

    @php
    $datosExploracionesFisicas =
    $citasDisponiblesExploracion
    ->mapWithKeys(function ($cita) {
    $exploracion =
    $cita->exploracionFisica;

    $signos = $cita->signoVital;

    return [
    (string) $cita->id => [
    'campos' => [
    'interrogatorio' =>
    $exploracion?->interrogatorio,

    'anotaciones' =>
    $exploracion?->anotaciones,

    'recomendaciones' =>
    $exploracion?->recomendaciones,
    ],

    'sistemas' =>
    $exploracion?->sistemas ?? [],

    'signos' => $signos
    ? [
    'peso' => $signos->peso,

    'presion_sistolica' =>
    $signos->presion_sistolica,

    'presion_diastolica' =>
    $signos->presion_diastolica,

    'frecuencia_cardiaca' =>
    $signos->frecuencia_cardiaca,

    'frecuencia_respiratoria' =>
    $signos->frecuencia_respiratoria,

    'temperatura' =>
    $signos->temperatura,

    'saturacion_oxigeno' =>
    $signos->saturacion_oxigeno,
    ]
    : null,
    ],
    ];
    })
    ->all();
    @endphp

    <script
        id="datos-exploraciones-fisicas"
        type="application/json">
        {
            !!json_encode(
                $datosExploracionesFisicas,
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_AMP |
                JSON_HEX_QUOT
            ) !!
        }
    </script>
    @endif

    <div
        id="estado-validacion-historia"
        data-tiene-errores="{{ $errors->hasAny([
        'patologia_base',
        'padecimiento_actual',
        'tratamientos_actuales',
        'prioridad_analisis_medico',
    ]) ? 'true' : 'false' }}"
        hidden>
    </div>

    <div
        id="estado-validacion-heredofamiliares"
        data-tiene-errores="{{ (
        $errors->heredofamiliares->has('numero_hermanos')
        || $errors->heredofamiliares->has('antecedentes.*')
    ) ? 'true' : 'false' }}"
        hidden>
    </div>

    <div
        id="estado-validacion-habitos-alimenticios"
        data-tiene-errores="{{ (
        $errors
            ->habitosAlimenticios
            ->has('comidas.*')
        || $errors
            ->habitosAlimenticios
            ->has('alimentos.*')
    ) ? 'true' : 'false' }}"
        hidden>
    </div>

    <div
        id="estado-validacion-personales-patologicos"
        data-tiene-errores="{{ (
        $errors
            ->personalesPatologicos
            ->has('antecedentes.*')
    ) ? 'true' : 'false' }}"
        hidden>
    </div>

    <div
        id="estado-validacion-personales-no-patologicos"
        data-tiene-errores="{{ (
        $errors
            ->personalesNoPatologicos
            ->has('antecedentes.*')
    ) ? 'true' : 'false' }}"
        hidden>
    </div>

    <div
        id="estado-validacion-exploracion-fisica"
        data-tiene-errores="{{ (
        $errors->exploracionFisica->hasAny([
            'interrogatorio',
            'anotaciones',
            'recomendaciones',
        ])
        || $errors
            ->exploracionFisica
            ->has('sistemas.*')
    ) ? 'true' : 'false' }}"
        hidden>
    </div>

    <script>
        /*
    |--------------------------------------------------------------------------
    | Funciones generales para modales
    |--------------------------------------------------------------------------
    */

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

        /*
        |--------------------------------------------------------------------------
        | Modal de exploración física
        |--------------------------------------------------------------------------
        */

        function abrirModalExploracionFisica() {
            abrirModal(
                'modal-exploracion-fisica',
                'exploracion_cita_id'
            );
        }

        function cerrarModalExploracionFisica() {
            cerrarModal('modal-exploracion-fisica');
        }

        function obtenerDatosExploracionesFisicas() {
            const elemento = document.getElementById(
                'datos-exploraciones-fisicas'
            );

            if (!elemento) {
                return {};
            }

            try {
                return JSON.parse(
                    elemento.textContent.trim() || '{}'
                );
            } catch (error) {
                console.error(
                    'No fue posible cargar las exploraciones:',
                    error
                );

                return {};
            }
        }

        function establecerTextoExploracion(
            idElemento,
            valor
        ) {
            const elemento = document.getElementById(
                idElemento
            );

            if (elemento) {
                elemento.textContent = valor;
            }
        }

        function actualizarFormularioExploracionFisica(
            conservarValores = false
        ) {
            const selector = document.getElementById(
                'exploracion_cita_id'
            );

            const formulario = document.getElementById(
                'form-exploracion-fisica'
            );

            const resumenSignos = document.getElementById(
                'resumen-signos-exploracion'
            );

            if (!selector || !formulario) {
                return;
            }

            const citaId = selector.value;

            const datos =
                obtenerDatosExploracionesFisicas();

            const datosCita = datos[citaId] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Acción dinámica del formulario
            |--------------------------------------------------------------------------
            */

            if (citaId) {
                const plantilla =
                    formulario.dataset.routeTemplate;

                formulario.action = plantilla.replace(
                    '__CITA__',
                    citaId
                );
            } else {
                formulario.action = '#';
            }

            /*
            |--------------------------------------------------------------------------
            | Campos clínicos
            |--------------------------------------------------------------------------
            */

            const campos = [
                'interrogatorio',
                'anotaciones',
                'recomendaciones',
            ];

            if (!conservarValores) {
                campos.forEach(function(campo) {
                    const elemento = document.getElementById(
                        `exploracion_${campo}`
                    );

                    if (elemento) {
                        elemento.value =
                            datosCita?.campos?.[campo] ??
                            '';
                    }
                });

                document
                    .querySelectorAll(
                        '[data-exploracion-sistema]'
                    )
                    .forEach(function(elemento) {
                        const sistema =
                            elemento.dataset.exploracionSistema;

                        elemento.value =
                            datosCita?.sistemas?.[sistema] ??
                            '';
                    });
            }



            /*
            |--------------------------------------------------------------------------
            | Signos vitales
            |--------------------------------------------------------------------------
            */

            if (!citaId || !resumenSignos) {
                resumenSignos?.classList.add('hidden');

                return;
            }

            resumenSignos.classList.remove('hidden');

            const signos = datosCita?.signos ?? null;

            const sinSignos = document.getElementById(
                'exploracion_sin_signos'
            );

            sinSignos?.classList.toggle(
                'hidden',
                signos !== null
            );

            establecerTextoExploracion(
                'exploracion_signo_peso',
                signos?.peso ?
                `${signos.peso} kg` :
                '—'
            );

            const presion =
                signos?.presion_sistolica &&
                signos?.presion_diastolica ?
                `${signos.presion_sistolica}` +
                `/${signos.presion_diastolica}` :
                '—';

            establecerTextoExploracion(
                'exploracion_signo_presion',
                presion
            );

            establecerTextoExploracion(
                'exploracion_signo_fc',
                signos?.frecuencia_cardiaca ??
                '—'
            );

            establecerTextoExploracion(
                'exploracion_signo_fr',
                signos?.frecuencia_respiratoria ??
                '—'
            );

            establecerTextoExploracion(
                'exploracion_signo_temperatura',
                signos?.temperatura ?
                `${signos.temperatura} °C` :
                '—'
            );

            establecerTextoExploracion(
                'exploracion_signo_saturacion',
                signos?.saturacion_oxigeno ?
                `${signos.saturacion_oxigeno} %` :
                '—'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de hábitos alimenticios
        |--------------------------------------------------------------------------
        */

        function abrirModalHabitosAlimenticios() {
            abrirModal(
                'modal-habitos-alimenticios',
                'habito_comida_desayuno'
            );
        }

        function cerrarModalHabitosAlimenticios() {
            cerrarModal('modal-habitos-alimenticios');
        }

        /*
|--------------------------------------------------------------------------
| Modal de antecedentes ginecoobstétricos
|--------------------------------------------------------------------------
*/

        function abrirModalGinecoobstetricos() {
            abrirModal(
                'modal-ginecoobstetricos',
                'gineco_edad_menarca'
            );
        }

        function cerrarModalGinecoobstetricos() {
            cerrarModal(
                'modal-ginecoobstetricos'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de antecedentes personales no patológicos
        |--------------------------------------------------------------------------
        */

        function abrirModalPersonalesNoPatologicos() {
            abrirModal(
                'modal-personales-no-patologicos',
                'personal_no_patologico_casa_habitacion'
            );
        }

        function cerrarModalPersonalesNoPatologicos() {
            cerrarModal(
                'modal-personales-no-patologicos'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de antecedentes personales patológicos
        |--------------------------------------------------------------------------
        */

        function abrirModalPersonalesPatologicos() {
            abrirModal(
                'modal-personales-patologicos',
                'personal_patologico_enfermedades_infancia'
            );
        }

        function cerrarModalPersonalesPatologicos() {
            cerrarModal(
                'modal-personales-patologicos'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de antecedentes heredofamiliares
        |--------------------------------------------------------------------------
        */

        function abrirModalHeredofamiliares() {
            abrirModal(
                'modal-heredofamiliares',
                'numero_hermanos'
            );
        }

        function cerrarModalHeredofamiliares() {
            cerrarModal('modal-heredofamiliares');
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de datos generales
        |--------------------------------------------------------------------------
        */

        function abrirModalDatosGenerales() {
            abrirModal(
                'modal-datos-generales',
                'modal_nombre'
            );
        }

        function cerrarModalDatosGenerales() {
            cerrarModal('modal-datos-generales');
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de contacto
        |--------------------------------------------------------------------------
        */

        function abrirModalContacto() {
            abrirModal(
                'modal-contacto',
                'modal_telefono'
            );
        }

        function cerrarModalContacto() {
            cerrarModal('modal-contacto');
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de notas
        |--------------------------------------------------------------------------
        */

        function abrirModalNotas() {
            abrirModal(
                'modal-notas',
                'modal_notas'
            );
        }

        function cerrarModalNotas() {
            cerrarModal('modal-notas');
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de historia clínica
        |--------------------------------------------------------------------------
        */

        function abrirModalHistoriaClinica() {
            abrirModal(
                'modal-historia-clinica',
                'patologia_base'
            );
        }

        function cerrarModalHistoriaClinica() {
            cerrarModal('modal-historia-clinica');
        }

        /*
        |--------------------------------------------------------------------------
        | Cerrar modales al presionar Escape
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function(event) {
                if (event.key !== 'Escape') {
                    return;
                }

                cerrarModalDatosGenerales();
                cerrarModalContacto();
                cerrarModalNotas();
                cerrarModalHistoriaClinica();
                cerrarModalHeredofamiliares();
                cerrarModalPersonalesPatologicos();
                cerrarModalPersonalesNoPatologicos();
                cerrarModalHabitosAlimenticios();
                cerrarModalGinecoobstetricos();
                cerrarModalExploracionFisica();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Cerrar al hacer clic sobre el fondo
        |--------------------------------------------------------------------------
        */

        const modales = [{
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
            {
                id: 'modal-historia-clinica',
                cerrar: cerrarModalHistoriaClinica,
            },
            {
                id: 'modal-heredofamiliares',
                cerrar: cerrarModalHeredofamiliares,
            },
            {
                id: 'modal-personales-patologicos',
                cerrar: cerrarModalPersonalesPatologicos,
            },
            {
                id: 'modal-personales-no-patologicos',
                cerrar: cerrarModalPersonalesNoPatologicos,
            },
            {
                id: 'modal-habitos-alimenticios',
                cerrar: cerrarModalHabitosAlimenticios,
            },
            {
                id: 'modal-exploracion-fisica',
                cerrar: cerrarModalExploracionFisica,
            },

            {
                id: 'modal-habitos-alimenticios',
                cerrar: cerrarModalHabitosAlimenticios,
            },
        ];

        modales.forEach(function(configuracion) {
            document
                .getElementById(configuracion.id)
                ?.addEventListener(
                    'click',
                    function(event) {
                        if (event.target === this) {
                            configuracion.cerrar();
                        }
                    }
                );
        });

        /*
        |--------------------------------------------------------------------------
        | Inicialización y errores de validación
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {
                /*
                |--------------------------------------------------------------------------
                | Historia clínica principal
                |--------------------------------------------------------------------------
                */

                /*
|--------------------------------------------------------------------------
| Mover modal ginecoobstétrico al nivel del body
|--------------------------------------------------------------------------
|
| Evita que contenedores superiores limiten el fondo del modal.
|
*/

                const modalGinecoobstetricos =
                    document.getElementById(
                        'modal-ginecoobstetricos'
                    );

                if (modalGinecoobstetricos) {
                    document.body.appendChild(
                        modalGinecoobstetricos
                    );
                }

                const estadoValidacion =
                    document.getElementById(
                        'estado-validacion-historia'
                    );

                const tieneErrores =
                    estadoValidacion
                    ?.dataset.tieneErrores === 'true';

                /*
                |--------------------------------------------------------------------------
                | Antecedentes heredofamiliares
                |--------------------------------------------------------------------------
                */

                const estadoHeredofamiliares =
                    document.getElementById(
                        'estado-validacion-heredofamiliares'
                    );

                const tieneErroresHeredofamiliares =
                    estadoHeredofamiliares
                    ?.dataset.tieneErrores === 'true';

                /*
                |--------------------------------------------------------------------------
                | Antecedentes personales patológicos
                |--------------------------------------------------------------------------
                */

                const estadoPersonalesPatologicos =
                    document.getElementById(
                        'estado-validacion-personales-patologicos'
                    );

                const tieneErroresPersonalesPatologicos =
                    estadoPersonalesPatologicos
                    ?.dataset.tieneErrores === 'true';

                /*
                |--------------------------------------------------------------------------
                | Antecedentes personales no patológicos
                |--------------------------------------------------------------------------
                */

                const estadoPersonalesNoPatologicos =
                    document.getElementById(
                        'estado-validacion-personales-no-patologicos'
                    );

                const tieneErroresPersonalesNoPatologicos =
                    estadoPersonalesNoPatologicos
                    ?.dataset.tieneErrores === 'true';

                /*
                |--------------------------------------------------------------------------
                | Hábitos alimenticios
                |--------------------------------------------------------------------------
                */

                const estadoHabitosAlimenticios =
                    document.getElementById(
                        'estado-validacion-habitos-alimenticios'
                    );

                const tieneErroresHabitosAlimenticios =
                    estadoHabitosAlimenticios
                    ?.dataset.tieneErrores === 'true';

                /*
                |--------------------------------------------------------------------------
                | Exploración física
                |--------------------------------------------------------------------------
                */

                const estadoExploracionFisica =
                    document.getElementById(
                        'estado-validacion-exploracion-fisica'
                    );

                const tieneErroresExploracionFisica =
                    estadoExploracionFisica
                    ?.dataset.tieneErrores === 'true';

                const selectorExploracion =
                    document.getElementById(
                        'exploracion_cita_id'
                    );

                selectorExploracion?.addEventListener(
                    'change',
                    function() {
                        actualizarFormularioExploracionFisica();
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | Antecedentes ginecoobstétricos
                |--------------------------------------------------------------------------
                */

                const estadoGinecoobstetricos =
                    document.getElementById(
                        'estado-validacion-ginecoobstetricos'
                    );

                const tieneErroresGinecoobstetricos =
                    estadoGinecoobstetricos
                    ?.dataset.tieneErrores === 'true';

                if (selectorExploracion?.value) {
                    actualizarFormularioExploracionFisica(
                        tieneErroresExploracionFisica
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Reabrir el modal correspondiente
                |--------------------------------------------------------------------------
                */

                if (tieneErroresExploracionFisica) {
                    abrirModalExploracionFisica();
                }

                if (tieneErroresHabitosAlimenticios) {
                    abrirModalHabitosAlimenticios();
                }

                if (tieneErroresPersonalesNoPatologicos) {
                    abrirModalPersonalesNoPatologicos();
                }

                if (tieneErroresPersonalesPatologicos) {
                    abrirModalPersonalesPatologicos();
                }

                if (tieneErroresHeredofamiliares) {
                    abrirModalHeredofamiliares();
                }

                if (tieneErrores) {
                    abrirModalHistoriaClinica();
                }

                if (tieneErroresGinecoobstetricos) {
                    abrirModalGinecoobstetricos();
                }
            }
        );
    </script>
</x-app-layout>