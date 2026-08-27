<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a
                    href="{{ request()->user()->isMedico()
    ? route('citas.index')
    : route('pacientes.index') }}"
                    class="text-slate-400 transition hover:text-slate-700">
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
                            d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <div>
                    <h2 class="text-xl font-semibold text-slate-900">
                        {{ $pacientes->nombre }}
                        {{ $pacientes->apellido }}
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ficha del paciente
                    </p>

                    @if ($ultimaActividad)
                    <div
                        class="mt-2 flex flex-wrap
               items-center gap-2">
                        <span
                            class="inline-flex h-2 w-2
                   rounded-full bg-blue-500"></span>

                        <p class="text-xs text-slate-500">
                            Última actividad:

                            <span class="font-semibold text-slate-700">
                                {{ $ultimaActividad['titulo'] }}
                            </span>

                            <span class="mx-1 text-slate-300">
                                ·
                            </span>

                            {{ $ultimaActividad['fecha']->format('d/m/Y') }}

                            <span class="text-slate-400">
                                {{ $ultimaActividad['fecha']->format('h:i A') }}
                            </span>
                        </p>
                    </div>
                    @else
                    <p class="mt-2 text-xs text-slate-400">
                        Sin actividad clínica registrada
                    </p>
                    @endif
                </div>
            </div>

            <span
                class="rounded-full px-3 py-1 text-xs font-semibold
                    {{ $pacientes->status
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'bg-red-50 text-red-700' }}">
                {{ $pacientes->status ? 'Activo' : 'Inactivo' }}
            </span>
        </div>
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

                    {{-- Perfil --}}
                    <section
                        class="overflow-hidden rounded-2xl
                               border border-slate-200 bg-white shadow-sm">
                        <div class="p-6">

                            <div class="flex items-start gap-4">

                                <img
                                    src="{{ $pacientes->fotoUrl() }}"
                                    alt="Foto de {{ $pacientes->nombre }}"
                                    class="h-24 w-24 shrink-0
               rounded-xl border
               border-slate-200
               object-cover shadow-sm">

                                <div class="min-w-0 flex-1">

                                    <h1
                                        class="text-xl font-bold
                   text-slate-900">
                                        {{ $pacientes->nombre }}
                                        {{ $pacientes->apellido }}
                                    </h1>

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $pacientes->edad
                ?? 'Edad no disponible' }}
                                    </p>

                                    {{-- Sexo y condición --}}
                                    <div class="mt-2 flex flex-wrap gap-2">

                                        <span
                                            class="inline-flex rounded-full
                       bg-slate-100 px-2.5 py-1
                       text-xs font-semibold
                       text-slate-700">

                                            {{ $pacientes->sexo_texto }}
                                        </span>

                                        @if ($pacientes->finado)
                                        <span
                                            class="inline-flex rounded-full
                           bg-red-100 px-2.5 py-1
                           text-xs font-semibold
                           text-red-700">
                                            Finado
                                        </span>
                                        @endif
                                    </div>

                                    {{-- ID y categoría --}}
                                    <div class="mt-2 flex flex-wrap gap-2">

                                        <span
                                            class="inline-flex rounded-full
                       bg-blue-50 px-2.5 py-1
                       text-xs font-semibold
                       text-blue-700">

                                            Paciente #{{ $pacientes->id }}
                                        </span>

                                        @unless (request()->user()->isMedico())

                                        @php
                                        $estiloCategoria =
                                        $pacientes->categoria_estilo;

                                        $estiloCategoriaInline = sprintf(
                                        'background-color: %s; color: %s; border-color: %s;',
                                        $estiloCategoria['fondo'],
                                        $estiloCategoria['texto'],
                                        $estiloCategoria['borde']
                                        );
                                        @endphp

                                        <span
                                            class="inline-flex rounded-full
           border px-2.5 py-1
           text-xs font-semibold"
                                            style="{{ $estiloCategoriaInline }}">

                                            {{ $pacientes->categoria_texto }}
                                        </span>

                                        @endunless
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>

                    {{-- Datos generales --}}
                    <section
                        class="overflow-hidden rounded-2xl
                               border border-slate-200
                               bg-white shadow-sm">
                        <div
                            class="flex items-center justify-between
                                   border-b border-slate-100
                                   px-5 py-4">
                            <div>
                                <h3
                                    class="text-sm font-semibold
                                           text-slate-900">
                                    Datos generales
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Información personal
                                </p>
                            </div>

                            @if (request()->user()->isAdmin())
                            <button
                                type="button"
                                onclick="abrirModalDatosGenerales()"
                                class="inline-flex items-center gap-1.5
               rounded-lg px-2.5 py-1.5
               text-xs font-semibold text-blue-600
               transition hover:bg-blue-50
               hover:text-blue-800">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16.862 4.487
                   18.55 2.8a1.875 1.875 0 1 1
                   2.652 2.652L10.582 16.07
                   a4.5 4.5 0 0 1-1.897 1.13
                   L6 18l.8-2.685
                   a4.5 4.5 0 0 1
                   1.13-1.897l8.932-8.931Z" />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19.5 7.125V16.5
                   A2.25 2.25 0 0 1
                   17.25 18.75H5.625
                   A2.25 2.25 0 0 1
                   3.375 16.5V4.875
                   A2.25 2.25 0 0 1
                   5.625 2.625H15" />
                                </svg>

                                Editar
                            </button>
                            @endif
                        </div>

                        <dl
                            class="grid grid-cols-1 gap-4 p-5
           sm:grid-cols-2
           lg:grid-cols-1 xl:grid-cols-2">

                            <div>
                                <dt class="text-xs font-medium text-slate-400">
                                    Fecha de nacimiento
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $pacientes->fecha_nacimiento
                ?->format('d/m/Y')
                ?? 'No registrada' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-slate-400">
                                    Edad
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $pacientes->edad ?? 'No disponible' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-slate-400">
                                    Sexo
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $pacientes->sexo_texto }}
                                </dd>
                            </div>

                            @unless (request()->user()->isMedico())

                            <div>
                                <dt class="text-xs font-medium text-slate-400">
                                    Lugar de nacimiento
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $pacientes->lugar_nacimiento
                    ?: 'No registrado' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-slate-400">
                                    Ocupación
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $pacientes->ocupacion
                    ?: 'No registrada' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-slate-400">
                                    Religión
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $pacientes->religion
                    ?: 'No registrada' }}
                                </dd>
                            </div>

                            @endunless
                        </dl>
                    </section>

                    @unless (request()->user()->isMedico())

                    {{-- Contacto --}}
                    <section
                        class="overflow-hidden rounded-2xl
                               border border-slate-200
                               bg-white shadow-sm">
                        <div
                            class="flex items-center justify-between
                                   border-b border-slate-100
                                   px-5 py-4">
                            <div>
                                <h3
                                    class="text-sm font-semibold
                                           text-slate-900">
                                    Contacto
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Teléfono y correo
                                </p>
                            </div>

                            @if (
                            request()->user()->isAdmin()
                            || request()->user()->isRecepcionista()
                            )
                            <button
                                type="button"
                                onclick="abrirModalContacto()"
                                class="text-xs font-semibold
                                 text-blue-600
                                hover:text-blue-800">
                                Editar
                            </button>
                            @endif
                        </div>

                        <dl class="space-y-4 p-5">

                            @foreach ([
                            'telefono' => 'Celular / WhatsApp',
                            'telefono_fijo' => 'Teléfono',
                            'telefono_secundario' => 'Teléfono secundario',
                            'email' => 'Correo electrónico',
                            ] as $campo => $etiqueta)

                            <div>
                                <dt class="text-xs font-medium text-slate-400">
                                    {{ $etiqueta }}
                                </dt>

                                <dd
                                    class="mt-1 break-words
                       text-sm font-semibold
                       text-slate-800">
                                    {{ $pacientes->{$campo}
                    ?: 'No registrado' }}
                                </dd>
                            </div>
                            @endforeach

                            <div class="border-t border-slate-100 pt-4">
                                <dt class="text-xs font-medium text-slate-400">
                                    Domicilio
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $pacientes->domicilio
                ?: 'No registrado' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-slate-400">
                                    Ciudad y estado
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-800">
                                    {{
                collect([
                    $pacientes->ciudad,
                    $pacientes->estado,
                ])
                    ->filter()
                    ->implode(', ')
                ?: 'No registrado'
            }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-slate-400">
                                    Código postal
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $pacientes->codigo_postal
                ?: 'No registrado' }}
                                </dd>
                            </div>

                            <div class="border-t border-slate-100 pt-4">
                                <dt class="text-xs font-medium text-slate-400">
                                    Costo personalizado
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-800">
                                    @if (
                                    $pacientes->costo_consulta_personalizado
                                    !== null
                                    )
                                    ${{ number_format(
                    (float) $pacientes
                        ->costo_consulta_personalizado,
                    2
                ) }}
                                    @else
                                    No configurado
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </section>

                    @endunless


                    @unless (request()->user()->isMedico())

                    {{-- Notas --}}
                    <section
                        class="overflow-hidden rounded-2xl
                               border border-slate-200
                               bg-white shadow-sm">
                        <div
                            class="flex items-center justify-between
                                   border-b border-slate-100
                                   px-5 py-4">
                            <div>
                                <h3
                                    class="text-sm font-semibold
                                           text-slate-900">
                                    Notas
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Observaciones generales
                                </p>
                            </div>

                            @if (request()->user()->isAdmin())
                            <button
                                type="button"
                                onclick="abrirModalNotas()"
                                class="text-xs font-semibold
                                           text-blue-600
                                           hover:text-blue-800">
                                Editar
                            </button>
                            @endif
                        </div>

                        <div
                            class="whitespace-pre-line p-5
                                   text-sm leading-6 text-slate-700">
                            {{ $pacientes->notas
                                ?? 'Sin notas registradas.' }}
                        </div>
                    </section>

                    @endunless



                </aside>

                {{-- ========================================================= --}}
                {{-- COLUMNA DERECHA --}}
                {{-- ========================================================= --}}

                <main class="space-y-4 lg:col-span-8">

                    {{-- ========================================================= --}}
                    {{-- HISTORIA CLÍNICA PRINCIPAL --}}
                    {{-- ========================================================= --}}

                    <details
                        class="group overflow-hidden rounded-2xl
               border border-slate-200
               bg-white shadow-sm">

                        <summary
                            class="flex cursor-pointer list-none
                   items-center justify-between
                   gap-4 px-6 py-5">

                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center
                           justify-center rounded-xl
                           bg-cyan-50 text-cyan-700">

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
                                            d="M9 12h6m-6 4h6M7 3h7l4 4v14H7z" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Historia clínica
                                    </h3>

                                    <p class="text-xs text-slate-400">
                                        Resumen clínico principal del paciente
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">

                                @if (
                                request()->user()->isAdmin()
                                || request()->user()->isMedico()
                                )
                                <button
                                    type="button"
                                    onclick="
                            event.preventDefault();
                            event.stopPropagation();
                            abrirModalHistoriaClinica();
                        "
                                    class="inline-flex items-center
                               justify-center rounded-xl
                               bg-cyan-600 px-4 py-2
                               text-xs font-semibold
                               text-white shadow-sm
                               transition hover:bg-cyan-700">

                                    {{ $pacientes->historiaClinica
                            ? 'Editar'
                            : 'Registrar' }}
                                </button>
                                @endif

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                           transition duration-200
                           group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100">

                            @if ($pacientes->historiaClinica)

                            <div class="grid grid-cols-1 gap-0 lg:grid-cols-2">

                                {{-- Patología base --}}
                                <article
                                    class="border-b border-slate-100
                               p-6 lg:border-r">

                                    <p
                                        class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-cyan-700">
                                        Patología base
                                    </p>

                                    <div
                                        class="mt-3 whitespace-pre-line
                                   text-sm leading-6
                                   text-slate-700">{{ $pacientes->historiaClinica->patologia_base
                                ?: 'Sin información registrada.' }}</div>
                                </article>

                                {{-- Padecimiento actual --}}
                                <article class="border-b border-slate-100 p-6">

                                    <p
                                        class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-cyan-700">
                                        Padecimiento actual
                                    </p>

                                    <div
                                        class="mt-3 whitespace-pre-line
                                   text-sm leading-6
                                   text-slate-700">{{ $pacientes->historiaClinica->padecimiento_actual
                                ?: 'Sin información registrada.' }}</div>
                                </article>

                                {{-- Tratamientos actuales --}}
                                <article
                                    class="border-b border-slate-100
                               p-6 lg:border-b-0 lg:border-r">

                                    <p
                                        class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-cyan-700">
                                        Tratamientos actuales
                                    </p>

                                    <div
                                        class="mt-3 whitespace-pre-line
                                   text-sm leading-6
                                   text-slate-700">{{ $pacientes->historiaClinica->tratamientos_actuales
                                ?: 'Sin información registrada.' }}</div>
                                </article>

                                {{-- Prioridad y análisis --}}
                                <article class="p-6">

                                    <p
                                        class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-cyan-700">
                                        Prioridad y análisis médico
                                    </p>

                                    <div
                                        class="mt-3 whitespace-pre-line
                                   text-sm leading-6
                                   text-slate-700">{{ $pacientes->historiaClinica->prioridad_analisis_medico
                                ?: 'Sin información registrada.' }}</div>
                                </article>
                            </div>

                            @else

                            <div class="px-6 py-12 text-center">

                                <div
                                    class="mx-auto flex h-12 w-12
                               items-center justify-center
                               rounded-2xl bg-slate-100
                               text-slate-400">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6M7 3h7l4 4v14H7z" />
                                    </svg>
                                </div>

                                <p class="mt-4 text-sm font-semibold text-slate-700">
                                    Sin historia clínica registrada
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Registra el primer resumen clínico del paciente.
                                </p>
                            </div>

                            @endif

                        </div>
                    </details>

                    @php
                    $heredofamiliares = $pacientes
                    ->historiaClinica
                    ?->antecedentesHeredofamiliares;

                    $valoresHeredofamiliares =
                    $heredofamiliares?->antecedentes ?? [];
                    @endphp

                    {{-- ========================================================= --}}
                    {{-- ANTECEDENTES HEREDOFAMILIARES --}}
                    {{-- ========================================================= --}}

                    <details
                        class="group overflow-hidden rounded-2xl
               border border-slate-200
               bg-white shadow-sm">

                        <summary
                            class="flex cursor-pointer list-none
                   items-center justify-between
                   gap-4 px-6 py-5">

                            <div>
                                <h3 class="font-semibold text-slate-900">
                                    Antecedentes heredofamiliares
                                </h3>

                                <p class="mt-1 text-xs text-slate-400">
                                    Enfermedades y condiciones presentes en la familia
                                </p>
                            </div>

                            <div class="flex items-center gap-3">

                                @if (
                                request()->user()->isAdmin()
                                || request()->user()->isMedico()
                                )
                                <button
                                    type="button"
                                    onclick="
                            event.preventDefault();
                            event.stopPropagation();
                            abrirModalHeredofamiliares();
                        "
                                    class="inline-flex items-center
                               justify-center rounded-xl
                               bg-emerald-600 px-4 py-2
                               text-xs font-semibold
                               text-white shadow-sm
                               transition hover:bg-emerald-700">

                                    {{ $heredofamiliares
                            ? 'Editar'
                            : 'Registrar' }}
                                </button>
                                @endif

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                           transition duration-200
                           group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100">

                            @if ($heredofamiliares)

                            <div
                                class="grid grid-cols-1 gap-0
                           sm:grid-cols-2
                           lg:grid-cols-3
                           xl:grid-cols-4">

                                <div
                                    class="border-b border-r
                               border-slate-100 p-4">

                                    <p class="text-xs font-medium text-slate-400">
                                        Hermanos
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-800">
                                        {{ $heredofamiliares->numero_hermanos
                                ?? 'No registrado' }}
                                    </p>
                                </div>

                                @foreach (
                                $camposHeredofamiliares
                                as $clave => $etiqueta
                                )

                                @php
                                $valor = data_get(
                                $valoresHeredofamiliares,
                                $clave
                                );
                                @endphp

                                <div
                                    class="border-b border-r
                                   border-slate-100 p-4">

                                    <p class="text-xs font-medium text-slate-400">
                                        {{ $etiqueta }}
                                    </p>

                                    <p
                                        class="mt-1 whitespace-pre-line
                                       text-sm font-semibold
                                       text-slate-800">
                                        {{ filled($valor)
                                    ? $valor
                                    : 'No registrado' }}
                                    </p>
                                </div>

                                @endforeach
                            </div>

                            @else

                            <div class="px-6 py-10 text-center">

                                <p class="text-sm font-semibold text-slate-700">
                                    Sin antecedentes heredofamiliares
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Registra las enfermedades y condiciones familiares.
                                </p>
                            </div>

                            @endif

                        </div>
                    </details>

                    @php
                    $personalesPatologicos = $pacientes
                    ->historiaClinica
                    ?->antecedentesPersonalesPatologicos;

                    $valoresPersonalesPatologicos =
                    $personalesPatologicos?->antecedentes ?? [];
                    @endphp

                    {{-- ========================================================= --}}
                    {{-- ANTECEDENTES PERSONALES PATOLÓGICOS --}}
                    {{-- ========================================================= --}}

                    <details
                        class="group overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">

                        <summary
                            class="flex cursor-pointer list-none
               items-center justify-between
               gap-4 px-6 py-5">

                            <div>
                                <h3 class="font-semibold text-slate-900">
                                    Antecedentes personales patológicos
                                </h3>

                                <p class="mt-1 text-xs text-slate-400">
                                    Enfermedades y eventos médicos previos del paciente
                                </p>
                            </div>

                            <div class="flex items-center gap-3">

                                @if (
                                request()->user()->isAdmin()
                                || request()->user()->isMedico()
                                )
                                <button
                                    type="button"
                                    onclick="
                        event.preventDefault();
                        event.stopPropagation();
                        abrirModalPersonalesPatologicos();
                    "
                                    class="inline-flex items-center
                           justify-center rounded-xl
                           bg-amber-600 px-4 py-2
                           text-xs font-semibold
                           text-white shadow-sm
                           transition hover:bg-amber-700">

                                    {{ $personalesPatologicos
                        ? 'Editar'
                        : 'Registrar' }}
                                </button>
                                @endif

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                       transition duration-200
                       group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100">

                            @if ($personalesPatologicos)

                            <div
                                class="grid grid-cols-1 gap-0
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4">

                                @foreach (
                                $camposPersonalesPatologicos
                                as $clave => $etiqueta
                                )

                                @php
                                $valor = data_get(
                                $valoresPersonalesPatologicos,
                                $clave
                                );
                                @endphp

                                <div
                                    class="border-b border-r
                               border-slate-100 p-4">

                                    <p class="text-xs font-medium text-slate-400">
                                        {{ $etiqueta }}
                                    </p>

                                    <p
                                        class="mt-1 whitespace-pre-line
                                   text-sm font-semibold
                                   text-slate-800">
                                        {{ filled($valor)
                                ? $valor
                                : 'No registrado' }}
                                    </p>
                                </div>

                                @endforeach
                            </div>

                            @else

                            <div class="px-6 py-10 text-center">

                                <p class="text-sm font-semibold text-slate-700">
                                    Sin antecedentes personales patológicos
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Registra las enfermedades y eventos médicos previos.
                                </p>
                            </div>

                            @endif

                        </div>
                    </details>

                    @php
                    $personalesNoPatologicos = $pacientes
                    ->historiaClinica
                    ?->antecedentesPersonalesNoPatologicos;

                    $valoresPersonalesNoPatologicos =
                    $personalesNoPatologicos?->antecedentes ?? [];
                    @endphp

                    {{-- ========================================================= --}}
                    {{-- ANTECEDENTES PERSONALES NO PATOLÓGICOS --}}
                    {{-- ========================================================= --}}

                    <details
                        class="group overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">

                        <summary
                            class="flex cursor-pointer list-none
               items-center justify-between
               gap-4 px-6 py-5">

                            <div>
                                <h3 class="font-semibold text-slate-900">
                                    Antecedentes personales no patológicos
                                </h3>

                                <p class="mt-1 text-xs text-slate-400">
                                    Vivienda, higiene, actividad física e inmunizaciones
                                </p>
                            </div>

                            <div class="flex items-center gap-3">

                                @if (
                                request()->user()->isAdmin()
                                || request()->user()->isMedico()
                                )
                                <button
                                    type="button"
                                    onclick="
                        event.preventDefault();
                        event.stopPropagation();
                        abrirModalPersonalesNoPatologicos();
                    "
                                    class="inline-flex items-center
                           justify-center rounded-xl
                           bg-indigo-600 px-4 py-2
                           text-xs font-semibold
                           text-white shadow-sm
                           transition hover:bg-indigo-700">

                                    {{ $personalesNoPatologicos
                        ? 'Editar'
                        : 'Registrar' }}
                                </button>
                                @endif

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                       transition duration-200
                       group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100">

                            @if ($personalesNoPatologicos)

                            <div
                                class="grid grid-cols-1 gap-0
                       sm:grid-cols-2
                       lg:grid-cols-3">

                                @foreach (
                                $camposPersonalesNoPatologicos
                                as $clave => $etiqueta
                                )

                                @php
                                $valor = data_get(
                                $valoresPersonalesNoPatologicos,
                                $clave
                                );
                                @endphp

                                <div
                                    class="border-b border-r
                               border-slate-100 p-4">

                                    <p class="text-xs font-medium text-slate-400">
                                        {{ $etiqueta }}
                                    </p>

                                    <p
                                        class="mt-1 whitespace-pre-line
                                   text-sm font-semibold
                                   text-slate-800">
                                        {{ filled($valor)
                                ? $valor
                                : 'No registrado' }}
                                    </p>
                                </div>

                                @endforeach
                            </div>

                            @else

                            <div class="px-6 py-10 text-center">

                                <p class="text-sm font-semibold text-slate-700">
                                    Sin antecedentes personales no patológicos
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Registra los hábitos y condiciones generales.
                                </p>
                            </div>

                            @endif

                        </div>
                    </details>

                    {{-- ===================================================== --}}
                    {{-- ANTECEDENTES GINECOOBSTÉTRICOS --}}
                    {{-- Solo se muestran para pacientes femeninas --}}
                    {{-- ===================================================== --}}

                    @if ($pacientes->sexo === 'femenino')
                    @include(
                    'pacientes.partials.ginecoobstetricos'
                    )
                    @endif

                    @php
                    $habitoAlimenticio = $pacientes
                    ->historiaClinica
                    ?->habitoAlimenticio;

                    $comidasRegistradas =
                    $habitoAlimenticio?->comidas ?? [];

                    $alimentosRegistrados =
                    $habitoAlimenticio?->alimentos ?? [];
                    @endphp

                    {{-- ========================================================= --}}
                    {{-- HÁBITOS ALIMENTICIOS --}}
                    {{-- ========================================================= --}}

                    <details
                        class="group overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">

                        <summary
                            class="flex cursor-pointer list-none
               items-center justify-between
               gap-4 px-6 py-5">

                            <div>
                                <h3 class="font-semibold text-slate-900">
                                    Hábitos alimenticios
                                </h3>

                                <p class="mt-1 text-xs text-slate-400">
                                    Comidas habituales, alimentos,
                                    frecuencia y cantidad de consumo
                                </p>
                            </div>

                            <div class="flex items-center gap-3">

                                @if (
                                request()->user()->isAdmin()
                                || request()->user()->isMedico()
                                )
                                <button
                                    type="button"
                                    onclick="
                        event.preventDefault();
                        event.stopPropagation();
                        abrirModalHabitosAlimenticios();
                    "
                                    class="inline-flex items-center
                           justify-center rounded-xl
                           bg-indigo-600 px-4 py-2
                           text-xs font-semibold
                           text-white shadow-sm
                           transition hover:bg-indigo-700">

                                    {{ $habitoAlimenticio
                        ? 'Editar'
                        : 'Registrar' }}
                                </button>
                                @endif

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                       transition duration-200
                       group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100">

                            @if ($habitoAlimenticio)

                            {{-- Comidas realizadas --}}
                            <div class="border-b border-slate-100 p-5">

                                <p
                                    class="mb-3 text-xs font-semibold
                           uppercase tracking-wide
                           text-slate-400">
                                    Comidas realizadas habitualmente
                                </p>

                                <div class="flex flex-wrap gap-2">

                                    @foreach (
                                    $comidasHabitosAlimenticios
                                    as $clave => $etiqueta
                                    )
                                    @php
                                    $realizaComida = (bool) data_get(
                                    $comidasRegistradas,
                                    $clave,
                                    false
                                    );
                                    @endphp

                                    <span
                                        @class([ 'inline-flex items-center rounded-full px-3 py-1' , 'text-xs font-semibold' , 'bg-emerald-50 text-emerald-700'=>
                                        $realizaComida,
                                        'bg-slate-100 text-slate-400' =>
                                        ! $realizaComida,
                                        ])>

                                        {{ $etiqueta }}:
                                        {{ $realizaComida ? 'Sí' : 'No' }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Frecuencia de alimentos --}}
                            <div
                                class="grid grid-cols-1 gap-0
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4">

                                @foreach (
                                $camposHabitosAlimenticios
                                as $clave => $etiqueta
                                )
                                @php
                                $valor = data_get(
                                $alimentosRegistrados,
                                $clave
                                );
                                @endphp

                                <div
                                    class="border-b border-r
                               border-slate-100 p-4">

                                    <p
                                        class="text-xs font-medium
                                   text-slate-400">
                                        {{ $etiqueta }}
                                    </p>

                                    <p
                                        class="mt-1 whitespace-pre-line
                                   text-sm font-semibold
                                   text-slate-800">

                                        {{ filled($valor)
                                ? $valor
                                : 'No registrado' }}
                                    </p>
                                </div>
                                @endforeach
                            </div>

                            @else

                            <div class="px-6 py-10 text-center">

                                <p class="text-sm font-semibold text-slate-700">
                                    Sin hábitos alimenticios registrados
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Registra las comidas habituales y la frecuencia
                                    de consumo de alimentos.
                                </p>
                            </div>

                            @endif
                        </div>
                    </details>

                    @php
                    $exploracionesFisicas = $pacientes
                    ->historiaClinica
                    ?->exploracionesFisicas
                    ?? collect();
                    @endphp

                    {{-- ========================================================= --}}
                    {{-- EXPLORACIONES FÍSICAS --}}
                    {{-- ========================================================= --}}

                    <details
                        class="group overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">

                        <summary
                            class="flex cursor-pointer list-none
               items-center justify-between
               gap-4 px-6 py-5">

                            <div>
                                <div class="flex items-center gap-2">

                                    <h3 class="font-semibold text-slate-900">
                                        Exploraciones físicas
                                    </h3>

                                    <span
                                        class="inline-flex min-w-6 items-center
                           justify-center rounded-full
                           bg-indigo-50 px-2 py-0.5
                           text-xs font-semibold
                           text-indigo-700">

                                        {{ $exploracionesFisicas->count() }}
                                    </span>
                                </div>

                                <p class="mt-1 text-xs text-slate-400">
                                    Historial clínico por consulta y signos vitales asociados
                                </p>
                            </div>

                            <div class="flex items-center gap-3">

                                @if (request()->user()->isMedico())
                                <button
                                    type="button"
                                    onclick="
                        event.preventDefault();
                        event.stopPropagation();
                        abrirModalExploracionFisica();
                    "
                                    class="inline-flex items-center
                           justify-center rounded-xl
                           bg-indigo-600 px-4 py-2
                           text-xs font-semibold
                           text-white shadow-sm
                           transition hover:bg-indigo-700">

                                    Registrar o editar
                                </button>
                                @endif

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                       transition duration-200
                       group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100">

                            @forelse ($exploracionesFisicas as $exploracion)

                            @php
                            $citaExploracion = $exploracion->cita;
                            $signosExploracion =
                            $citaExploracion?->signoVital;

                            $nombreMedico = trim(
                            ($exploracion->medico?->nombre ?? '')
                            . ' '
                            . ($exploracion->medico?->apellido_paterno ?? '')
                            . ' '
                            . ($exploracion->medico?->apellido_materno ?? '')
                            );
                            @endphp

                            <article
                                class="border-b border-slate-100
                       p-5 last:border-b-0">

                                {{-- Cabecera de la consulta --}}
                                <div
                                    class="flex flex-col justify-between
                           gap-3 sm:flex-row
                           sm:items-start">

                                    <div>
                                        <p
                                            class="text-sm font-semibold
                                   text-slate-900">

                                            Consulta del
                                            {{ $citaExploracion?->fecha
                                ? $citaExploracion
                                    ->fecha
                                    ->format('d/m/Y')
                                : 'Sin fecha' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Dr. {{ filled($nombreMedico)
                                ? $nombreMedico
                                : 'No disponible' }}

                                            @if ($citaExploracion?->hora)
                                            ·
                                            {{ \Carbon\Carbon::parse(
                                    $citaExploracion->hora
                                )->format('H:i') }}
                                            @endif
                                        </p>
                                    </div>

                                    <span
                                        class="inline-flex w-fit rounded-full
                               bg-emerald-50 px-3 py-1
                               text-xs font-semibold
                               text-emerald-700">

                                        Registro clínico
                                    </span>
                                </div>

                                {{-- Signos vitales reutilizados --}}
                                <section
                                    class="mt-5 rounded-xl
                           border border-slate-200
                           bg-slate-50 p-4">

                                    <p
                                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-500">
                                        Signos vitales de la consulta
                                    </p>

                                    @if ($signosExploracion)

                                    <div
                                        class="mt-3 grid grid-cols-2
                                   gap-3 sm:grid-cols-3
                                   lg:grid-cols-6">

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Peso
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $signosExploracion->peso ?? '—' }}
                                                @if ($signosExploracion->peso)
                                                kg
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Presión arterial
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                @if (
                                                $signosExploracion->presion_sistolica
                                                && $signosExploracion->presion_diastolica
                                                )
                                                {{ $signosExploracion->presion_sistolica }}
                                                /
                                                {{ $signosExploracion->presion_diastolica }}
                                                @else
                                                —
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                F. cardiaca
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $signosExploracion
                                        ->frecuencia_cardiaca ?? '—' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                F. respiratoria
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $signosExploracion
                                        ->frecuencia_respiratoria ?? '—' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Temperatura
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $signosExploracion->temperatura ?? '—' }}
                                                @if ($signosExploracion->temperatura)
                                                °C
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                SatO₂
                                            </p>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $signosExploracion
                                        ->saturacion_oxigeno ?? '—' }}
                                                @if ($signosExploracion->saturacion_oxigeno)
                                                %
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    @if ($signosExploracion->observaciones)
                                    <p
                                        class="mt-3 whitespace-pre-line
                                       border-t border-slate-200
                                       pt-3 text-xs text-slate-600">

                                        <span class="font-semibold">
                                            Observaciones de enfermería:
                                        </span>

                                        {{ $signosExploracion->observaciones }}
                                    </p>
                                    @endif

                                    @else

                                    <p class="mt-2 text-sm text-slate-400">
                                        Enfermería todavía no ha registrado signos
                                        vitales para esta cita.
                                    </p>

                                    @endif
                                </section>

                                {{-- Información narrativa --}}
                                <div
                                    class="mt-5 grid grid-cols-1 gap-4
                                            lg:grid-cols-2">

                                    @foreach (
                                    $camposExploracionFisica
                                    as $clave => $etiqueta
                                    )
                                    <div
                                        class="rounded-xl border
                                   border-slate-200 p-4">

                                        <p
                                            class="text-xs font-semibold
                                       text-slate-500">
                                            {{ $etiqueta }}
                                        </p>

                                        <p
                                            class="mt-2 whitespace-pre-line
                                       text-sm text-slate-700">

                                            {{ filled($exploracion->{$clave})
                                    ? $exploracion->{$clave}
                                    : 'No registrado' }}
                                        </p>
                                    </div>
                                    @endforeach
                                </div>

                                @php
                                $sistemasRegistrados =
                                $exploracion->sistemas ?? [];

                                $sistemasConDatos = collect(
                                $sistemasExploracionFisica
                                )->filter(
                                function ($etiqueta, $clave)
                                use ($sistemasRegistrados) {
                                return filled(
                                data_get(
                                $sistemasRegistrados,
                                $clave
                                )
                                );
                                }
                                );
                                @endphp

                                {{-- Exploración por sistemas --}}
                                <section class="mt-5">

                                    <div class="flex items-center justify-between gap-3">

                                        <div>
                                            <h4
                                                class="text-sm font-semibold
                       text-slate-900">
                                                Exploración por sistemas y órganos
                                            </h4>

                                            <p class="mt-1 text-xs text-slate-400">
                                                Hallazgos registrados durante la consulta
                                            </p>
                                        </div>

                                        <span
                                            class="rounded-full bg-indigo-50
                   px-3 py-1 text-xs font-semibold
                   text-indigo-700">

                                            {{ $sistemasConDatos->count() }}
                                            registrados
                                        </span>
                                    </div>

                                    @if ($sistemasConDatos->isNotEmpty())

                                    <div
                                        class="mt-4 grid grid-cols-1 gap-4
                   md:grid-cols-2
                   xl:grid-cols-3">

                                        @foreach (
                                        $sistemasConDatos
                                        as $clave => $etiqueta
                                        )
                                        @php
                                        $hallazgo = data_get(
                                        $sistemasRegistrados,
                                        $clave
                                        );

                                        $iniciales = collect(
                                        preg_split('/\s+/', $etiqueta)
                                        )
                                        ->filter()
                                        ->map(
                                        fn ($palabra) =>
                                        mb_strtoupper(
                                        mb_substr(
                                        $palabra,
                                        0,
                                        1
                                        )
                                        )
                                        )
                                        ->take(2)
                                        ->implode('');
                                        @endphp

                                        <article
                                            class="overflow-hidden rounded-xl
                           border border-slate-200
                           bg-white">

                                            <div
                                                class="flex items-center gap-3
                               border-b border-slate-100
                               bg-slate-50 px-4 py-3">

                                                <div
                                                    class="flex h-9 w-9 shrink-0
                                   items-center justify-center
                                   rounded-lg bg-indigo-100
                                   text-xs font-bold
                                   text-indigo-700">

                                                    {{ $iniciales }}
                                                </div>

                                                <p
                                                    class="text-sm font-semibold
                                   text-slate-800">
                                                    {{ $etiqueta }}
                                                </p>
                                            </div>

                                            <p
                                                class="whitespace-pre-line
                               p-4 text-sm text-slate-700">
                                                {{ $hallazgo }}
                                            </p>
                                        </article>
                                        @endforeach
                                    </div>

                                    @else

                                    <div
                                        class="mt-4 rounded-xl border
                   border-dashed border-slate-300
                   px-5 py-8 text-center">

                                        <p class="text-sm text-slate-400">
                                            No se registraron hallazgos específicos
                                            por sistema en esta consulta.
                                        </p>
                                    </div>

                                    @endif
                                </section>
                            </article>

                            @empty

                            <div class="px-6 py-10 text-center">

                                <p class="text-sm font-semibold text-slate-700">
                                    Sin exploraciones físicas registradas
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Las exploraciones realizadas durante cada consulta
                                    aparecerán en este historial.
                                </p>
                            </div>

                            @endforelse
                        </div>
                    </details>

                    {{-- Resumen --}}
                    <section
                        class="rounded-2xl border
                               border-slate-200 bg-white
                               p-5 shadow-sm">
                        <div
                            class="grid grid-cols-2 gap-3
                                   sm:grid-cols-4">
                            <div
                                class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-medium
                                           text-slate-500">
                                    Citas
                                </p>

                                <p
                                    class="mt-1 text-2xl font-bold
                                           text-slate-900">
                                    {{ $pacientes->citas->count() }}
                                </p>
                            </div>

                            <div
                                class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-medium
                                           text-slate-500">
                                    Estudios
                                </p>

                                <p
                                    class="mt-1 text-2xl font-bold
                                           text-slate-900">
                                    {{ $pacientes->estudios->count() }}
                                </p>
                            </div>

                            <div
                                class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-medium
                                           text-slate-500">
                                    Recetas
                                </p>

                                <p
                                    class="mt-1 text-2xl font-bold
                                           text-slate-900">
                                    {{ $pacientes->recetas->count() }}
                                </p>
                            </div>

                            <div
                                class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-medium
                                           text-slate-500">
                                    Signos vitales
                                </p>

                                <p
                                    class="mt-1 text-2xl font-bold
                                           text-slate-900">
                                    {{ $pacientes->signosVitales->count() }}
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- ================================================= --}}
                    {{-- CITAS --}}
                    {{-- ================================================= --}}
                    <details
                        class="group overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm"
                        open>
                        {{-- Encabezado del acordeón --}}
                        <summary
                            class="flex cursor-pointer
               list-none items-center
               justify-between gap-4
               px-6 py-5">
                            <div class="flex items-center gap-3">

                                {{-- Icono --}}
                                <div
                                    class="flex h-9 w-9 items-center
                       justify-center rounded-lg
                       bg-blue-50 text-blue-600">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Historial de citas
                                    </h3>

                                    <p class="text-xs text-slate-400">
                                        Consultas y expediente asociado
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">

                                {{-- Contador --}}
                                <span
                                    class="rounded-full bg-blue-50
                       px-2.5 py-1
                       text-xs font-semibold
                       text-blue-700">
                                    {{ $pacientes->citas->count() }}
                                </span>

                                {{-- Flecha --}}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                       transition
                       group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        {{-- Contenido --}}
                        <div class="border-t border-slate-100">

                            @if ($pacientes->citas->isEmpty())

                            <div
                                class="px-6 py-10 text-center
                       text-sm text-slate-500">
                                <p class="font-medium text-slate-600">
                                    No hay citas registradas.
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Las consultas del paciente aparecerán aquí.
                                </p>
                            </div>

                            @else

                            <div class="overflow-x-auto">

                                <table class="min-w-full">

                                    {{-- ================================================= --}}
                                    {{-- ENCABEZADOS --}}
                                    {{-- ================================================= --}}
                                    <thead class="bg-slate-50">
                                        <tr>

                                            <th
                                                class="px-5 py-3
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500">
                                                Fecha
                                            </th>

                                            <th
                                                class="px-5 py-3
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500">
                                                Médico
                                            </th>

                                            <th
                                                class="px-5 py-3
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500">
                                                Motivo
                                            </th>

                                            <th
                                                class="px-5 py-3
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500">
                                                Estado
                                            </th>

                                            <th
                                                class="px-5 py-3
                                       text-left
                                       text-xs font-semibold
                                       text-slate-500">
                                                Expediente
                                            </th>

                                            <th
                                                class="px-5 py-3
                                       text-right
                                       text-xs font-semibold
                                       text-slate-500">
                                                Acción
                                            </th>

                                        </tr>
                                    </thead>

                                    {{-- ================================================= --}}
                                    {{-- CITAS --}}
                                    {{-- ================================================= --}}
                                    <tbody class="divide-y divide-slate-100">

                                        @foreach ($pacientes->citas as $cita)

                                        @php
                                        /*
                                        |--------------------------------------------------------------------------
                                        | Motivo legible
                                        |--------------------------------------------------------------------------
                                        */

                                        $motivoTexto = match ($cita->motivo) {
                                        'consulta_inicial'
                                        => 'Consulta inicial',

                                        'consulta_subsecuente'
                                        => 'Consulta subsecuente',

                                        'consulta_emergencia'
                                        => 'Consulta de emergencia',

                                        default => $cita->motivo
                                        ? ucfirst(
                                        str_replace(
                                        '_',
                                        ' ',
                                        $cita->motivo
                                        )
                                        )
                                        : 'No especificado',
                                        };

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Estado calculado
                                        |--------------------------------------------------------------------------
                                        */

                                        $estado = $cita->estado_actual;

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Recursos asociados
                                        |--------------------------------------------------------------------------
                                        */

                                        $tieneSignos =
                                        $cita->signoVital !== null;

                                        $tieneReceta =
                                        $cita->receta !== null;

                                        $cantidadEstudios =
                                        $cita->estudios->count();
                                        @endphp

                                        <tr
                                            class="transition
                                       hover:bg-slate-50/70">

                                            {{-- ===================================== --}}
                                            {{-- FECHA --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="whitespace-nowrap
                                           px-5 py-4
                                           align-top
                                           text-sm text-slate-700">
                                                <p class="font-semibold text-slate-800">
                                                    {{ $cita->fecha?->format('d/m/Y')
                                            ?? '—' }}
                                                </p>

                                                <p
                                                    class="mt-0.5
                                               text-xs text-slate-400">
                                                    {{ $cita->hora
                                            ? \Carbon\Carbon::parse(
                                                $cita->hora
                                            )->format('h:i A')
                                            : '—' }}
                                                </p>
                                            </td>

                                            {{-- ===================================== --}}
                                            {{-- MÉDICO --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="px-5 py-4
                                           align-top
                                           text-sm text-slate-700">
                                                <p class="font-medium text-slate-800">
                                                    {{ $cita
                                            ->medico
                                            ?->user
                                            ?->name
                                            ?? 'No disponible' }}
                                                </p>

                                                @if ($cita->medico?->especialidad)
                                                <p
                                                    class="mt-0.5
                                                   text-xs text-slate-400">
                                                    {{ $cita->medico->especialidad }}
                                                </p>
                                                @endif
                                            </td>

                                            {{-- ===================================== --}}
                                            {{-- MOTIVO --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="px-5 py-4
                                           align-top
                                           text-sm text-slate-700">
                                                <p class="font-medium">
                                                    {{ $motivoTexto }}
                                                </p>

                                                @if ($cita->modalidad)
                                                <p
                                                    class="mt-1
                                                   text-xs text-slate-400">
                                                    {{ ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $cita->modalidad
                                                )
                                            ) }}
                                                </p>
                                                @endif
                                            </td>

                                            {{-- ===================================== --}}
                                            {{-- ESTADO --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="px-5 py-4
                                           align-top">
                                                <span
                                                    @class([ 'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold' , 'bg-emerald-50 text-emerald-700'=> $estado === 'finalizada',

                                                    'bg-blue-50 text-blue-700'
                                                    => $estado === 'programada',

                                                    'bg-amber-50 text-amber-700'
                                                    => $estado === 'en_curso',

                                                    'bg-red-50 text-red-700'
                                                    => $estado === 'cancelada',

                                                    'bg-slate-100 text-slate-600'
                                                    => !in_array(
                                                    $estado,
                                                    [
                                                    'finalizada',
                                                    'programada',
                                                    'en_curso',
                                                    'cancelada',
                                                    ],
                                                    true
                                                    ),
                                                    ])
                                                    >
                                                    {{ ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $estado
                                            )
                                        ) }}
                                                </span>
                                            </td>

                                            {{-- ===================================== --}}
                                            {{-- EXPEDIENTE --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="px-5 py-4
                                           align-top">
                                                <div class="space-y-2">

                                                    {{-- Signos vitales --}}
                                                    <div
                                                        class="flex items-center
                                                   gap-2">
                                                        @if ($tieneSignos)

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-emerald-500"></span>

                                                        <span
                                                            class="text-xs
                                                           font-medium
                                                           text-slate-600">
                                                            Signos vitales
                                                        </span>

                                                        @else

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-slate-300"></span>

                                                        <span
                                                            class="text-xs
                                                           text-slate-400">
                                                            Sin signos vitales
                                                        </span>

                                                        @endif
                                                    </div>

                                                    {{-- Receta --}}
                                                    <div
                                                        class="flex items-center
                                                   gap-2">
                                                        @if ($tieneReceta)

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-emerald-500"></span>

                                                        <span
                                                            class="text-xs
                                                           font-medium
                                                           text-slate-600">
                                                            Receta emitida
                                                        </span>

                                                        @else

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-slate-300"></span>

                                                        <span
                                                            class="text-xs
                                                           text-slate-400">
                                                            Sin receta
                                                        </span>

                                                        @endif
                                                    </div>

                                                    {{-- Estudios --}}
                                                    <div
                                                        class="flex items-center
                                                   gap-2">
                                                        @if ($cantidadEstudios > 0)

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-violet-500"></span>

                                                        <span
                                                            class="text-xs
                                                           font-medium
                                                           text-slate-600">
                                                            {{ $cantidadEstudios }}

                                                            {{ $cantidadEstudios === 1
                                                        ? 'estudio'
                                                        : 'estudios' }}
                                                        </span>

                                                        @else

                                                        <span
                                                            class="h-2 w-2
                                                           shrink-0
                                                           rounded-full
                                                           bg-slate-300"></span>

                                                        <span
                                                            class="text-xs
                                                           text-slate-400">
                                                            Sin estudios
                                                        </span>

                                                        @endif
                                                    </div>

                                                </div>
                                            </td>

                                            {{-- ===================================== --}}
                                            {{-- ACCIÓN --}}
                                            {{-- ===================================== --}}
                                            <td
                                                class="whitespace-nowrap
                                           px-5 py-4
                                           text-right
                                           align-top">
                                                <a
                                                    href="{{ route(
                                            'citas.show',
                                            $cita
                                        ) }}"
                                                    class="inline-flex
                                               items-center gap-1.5
                                               rounded-lg
                                               border border-slate-200
                                               bg-white
                                               px-3 py-2
                                               text-xs font-semibold
                                               text-slate-700
                                               shadow-sm
                                               transition
                                               hover:border-blue-200
                                               hover:bg-blue-50
                                               hover:text-blue-700">
                                                    Ver cita

                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-3.5 w-3.5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="2">
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </td>

                                        </tr>

                                        @endforeach

                                    </tbody>
                                </table>

                            </div>

                            @endif

                        </div>
                    </details>

                    {{-- ================================================= --}}
                    {{-- ESTUDIOS CLÍNICOS --}}
                    {{-- ================================================= --}}
                    <details
                        class="group overflow-hidden rounded-2xl
           border border-slate-200
           bg-white shadow-sm">
                        <summary
                            class="flex cursor-pointer
               list-none items-center
               justify-between px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center
                       justify-center rounded-lg
                       bg-violet-50 text-violet-600">
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
                                            d="M9 12h6m-6 4h6M7 3h7l4 4v14H7z" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Estudios clínicos
                                    </h3>

                                    <p class="text-xs text-slate-400">
                                        Documentos asociados al paciente
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span
                                    class="rounded-full bg-violet-50
                       px-2.5 py-1 text-xs
                       font-semibold text-violet-700">
                                    {{ $pacientes->estudios->count() }}
                                </span>

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                       transition group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100">

                            @forelse ($pacientes->estudios as $estudio)

                            <div
                                class="border-b border-slate-100
                       px-6 py-5 last:border-0">
                                <div
                                    class="flex flex-col gap-4
                           sm:flex-row
                           sm:items-start
                           sm:justify-between">

                                    {{-- Información --}}
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-semibold text-slate-900">
                                                {{ $estudio->nombre }}
                                            </p>

                                            <span
                                                class="rounded-full bg-red-50
                                       px-2 py-0.5
                                       text-[11px] font-semibold
                                       text-red-600">
                                                PDF
                                            </span>
                                        </div>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $estudio->fecha_estudio?->format('d/m/Y')
                                ?? 'Fecha no registrada' }}
                                        </p>

                                        @if ($estudio->descripcion)
                                        <p
                                            class="mt-2 max-w-2xl
                                       text-sm leading-5
                                       text-slate-600">
                                            {{ $estudio->descripcion }}
                                        </p>
                                        @endif

                                        <div
                                            class="mt-3 flex flex-wrap gap-x-4
                                   gap-y-1 text-xs text-slate-400">
                                            <span>
                                                Archivo:
                                                {{ $estudio->archivo_original
                                    ?? 'No disponible' }}
                                            </span>

                                            @if ($estudio->subidoPor)
                                            <span>
                                                Subido por:
                                                {{ $estudio->subidoPor->name }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Acciones --}}
                                    <div
                                        class="flex shrink-0
                               flex-wrap items-center gap-2">
                                        <a
                                            href="{{ route(
                                'estudios.archivo',
                                $estudio
                            ) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center
                                   gap-1.5 rounded-lg
                                   border border-slate-200
                                   bg-white px-3 py-2
                                   text-xs font-semibold
                                   text-slate-700 transition
                                   hover:bg-slate-50">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6.75
                                       9.75-6.75S21.75 12
                                       21.75 12 18 18.75
                                       12 18.75 2.25 12
                                       2.25 12Z" />

                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.75" />
                                            </svg>

                                            Ver
                                        </a>

                                        <a
                                            href="{{ route(
                                'estudios.descargar',
                                $estudio
                            ) }}"
                                            class="inline-flex items-center
                                   gap-1.5 rounded-lg
                                   bg-violet-600
                                   px-3 py-2
                                   text-xs font-semibold
                                   text-white transition
                                   hover:bg-violet-700">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 3v12m0 0
                                       4-4m-4 4-4-4
                                       M5 21h14" />
                                            </svg>

                                            Descargar
                                        </a>
                                    </div>

                                </div>
                            </div>

                            @empty

                            <div
                                class="px-6 py-10 text-center">
                                <p class="text-sm font-medium text-slate-600">
                                    No hay estudios registrados.
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Los estudios asociados al paciente
                                    aparecerán aquí.
                                </p>
                            </div>

                            @endforelse

                            @if ($pacientes->estudios->isNotEmpty())
                            <div
                                class="border-t border-slate-100
                       bg-slate-50/60 px-6 py-4
                       text-right">
                                <a
                                    href="{{ route(
                        'pacientes.estudios.index',
                        $pacientes
                    ) }}"
                                    class="text-sm font-semibold
                           text-violet-600
                           hover:text-violet-800">
                                    Ver historial completo →
                                </a>
                            </div>
                            @endif

                        </div>
                    </details>

                    {{-- ================================================= --}}
                    {{-- RECETAS MÉDICAS --}}
                    {{-- ================================================= --}}
                    @if (
                    request()->user()->isAdmin()
                    || request()->user()->role === 'medico'
                    )
                    <details
                        class="group overflow-hidden rounded-2xl
               border border-slate-200
               bg-white shadow-sm">
                        <summary
                            class="flex cursor-pointer
                   list-none items-center
                   justify-between px-6 py-5">
                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center
                           justify-center rounded-lg
                           bg-emerald-50 text-emerald-600">
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
                                            d="M9 12h6m-6 4h6
                               M7 3h7l4 4v14H7z" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Recetas médicas
                                    </h3>

                                    <p class="text-xs text-slate-400">
                                        Prescripciones emitidas al paciente
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span
                                    class="rounded-full bg-emerald-50
                           px-2.5 py-1 text-xs
                           font-semibold text-emerald-700">
                                    {{ $pacientes->recetas->count() }}
                                </span>

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                           transition group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100">

                            @forelse ($pacientes->recetas as $receta)

                            <div
                                class="border-b border-slate-100
                           px-6 py-5 last:border-0">
                                <div
                                    class="flex flex-col gap-4
                               sm:flex-row
                               sm:items-center
                               sm:justify-between">

                                    {{-- Información --}}
                                    <div class="min-w-0">

                                        <div
                                            class="flex flex-wrap
                                       items-center gap-2">
                                            <p
                                                class="font-semibold
                                           text-slate-900">
                                                Receta #{{ $receta->id }}
                                            </p>

                                            <span
                                                class="rounded-full
                                           bg-emerald-50
                                           px-2 py-0.5
                                           text-[11px]
                                           font-semibold
                                           text-emerald-700">
                                                Receta médica
                                            </span>
                                        </div>

                                        <p
                                            class="mt-1 text-sm
                                       text-slate-500">
                                            Expedida:
                                            {{ $receta->fecha_expedicion
                                    ? \Carbon\Carbon::parse(
                                        $receta->fecha_expedicion
                                    )->format('d/m/Y')
                                    : 'Fecha no disponible' }}
                                        </p>

                                        <p
                                            class="mt-2 text-sm
                                       text-slate-600">
                                            Médico:
                                            <span class="font-medium">
                                                {{ $receta
                                        ->cita
                                        ?->medico
                                        ?->user
                                        ?->name
                                        ?? 'No disponible' }}
                                            </span>
                                        </p>

                                        @if ($receta->cita)
                                        <p
                                            class="mt-1 text-xs
                                           text-slate-400">
                                            Cita #{{ $receta->cita->id }}

                                            @if ($receta->cita->fecha)
                                            ·
                                            {{ $receta
                                            ->cita
                                            ->fecha
                                            ->format('d/m/Y') }}
                                            @endif
                                        </p>
                                        @endif
                                    </div>

                                    {{-- Acciones --}}
                                    <div
                                        class="flex shrink-0
                                   flex-wrap items-center gap-2">
                                        <a
                                            href="{{ route(
                                    'recetas.show',
                                    $receta
                                ) }}"
                                            class="inline-flex items-center
                                       gap-1.5 rounded-lg
                                       border border-slate-200
                                       bg-white px-3 py-2
                                       text-xs font-semibold
                                       text-slate-700
                                       transition
                                       hover:bg-slate-50">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6.75
                                           9.75-6.75S21.75 12
                                           21.75 12 18 18.75
                                           12 18.75 2.25 12
                                           2.25 12Z" />

                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.75" />
                                            </svg>

                                            Ver receta
                                        </a>

                                        <a
                                            href="{{ route(
                                    'recetas.pdf',
                                    $receta
                                ) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center
                                       gap-1.5 rounded-lg
                                       bg-emerald-600
                                       px-3 py-2
                                       text-xs font-semibold
                                       text-white transition
                                       hover:bg-emerald-700">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M6 2h9l3 3v17H6z" />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9 13h6M9 17h4" />
                                            </svg>

                                            PDF
                                        </a>
                                    </div>

                                </div>
                            </div>

                            @empty

                            <div class="px-6 py-10 text-center">
                                <p
                                    class="text-sm font-medium
                               text-slate-600">
                                    No hay recetas registradas.
                                </p>

                                <p
                                    class="mt-1 text-xs
                               text-slate-400">
                                    Las recetas emitidas aparecerán aquí.
                                </p>
                            </div>

                            @endforelse

                            @if ($pacientes->recetas->isNotEmpty())
                            <div
                                class="border-t border-slate-100
                           bg-slate-50/60 px-6 py-4
                           text-right">
                                <a
                                    href="{{ route(
                            'pacientes.recetas.index',
                            $pacientes
                        ) }}"
                                    class="text-sm font-semibold
                               text-emerald-600
                               hover:text-emerald-800">
                                    Ver historial completo →
                                </a>
                            </div>
                            @endif

                        </div>
                    </details>
                    @endif

                    {{-- ================================================= --}}
                    {{-- SIGNOS VITALES --}}
                    {{-- ================================================= --}}
                    @if (
                    request()->user()->isAdmin()
                    || request()->user()->role === 'medico'
                    || request()->user()->role === 'enfermero'
                    )
                    <details
                        class="group overflow-hidden rounded-2xl
                          border border-slate-200
                          bg-white shadow-sm">
                        <summary
                            class="flex cursor-pointer
                   list-none items-center
                   justify-between px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center
                           justify-center rounded-lg
                           bg-rose-50 text-rose-600">
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
                                            d="M3 12h4l2-6 4 12 2-6h6" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Signos vitales
                                    </h3>

                                    <p class="text-xs text-slate-400">
                                        Registros clínicos del paciente
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span
                                    class="rounded-full bg-rose-50
                           px-2.5 py-1 text-xs
                           font-semibold text-rose-700">
                                    {{ $pacientes->signosVitales->count() }}
                                </span>

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-400
                           transition group-open:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100">

                            @forelse ($pacientes->signosVitales as $signo)

                            <div
                                class="border-b border-slate-100
                           px-6 py-5 last:border-0">
                                <div
                                    class="flex flex-col gap-4
                               lg:flex-row
                               lg:items-center
                               lg:justify-between">
                                    <div
                                        class="grid flex-1 gap-4
                                   sm:grid-cols-2
                                   xl:grid-cols-6">
                                        {{-- Fecha --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Fecha
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                {{ $signo->created_at?->format('d/m/Y')
                                        ?? '—' }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ $signo->created_at?->format('h:i A')
                                        ?? '' }}
                                            </p>
                                        </div>

                                        {{-- Peso --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Peso
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                {{ $signo->peso
                                        ? $signo->peso . ' kg'
                                        : '—' }}
                                            </p>
                                        </div>

                                        {{-- Presión --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Presión arterial
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                @if (
                                                $signo->presion_sistolica
                                                && $signo->presion_diastolica
                                                )
                                                {{ $signo->presion_sistolica }}
                                                /
                                                {{ $signo->presion_diastolica }}
                                                mmHg
                                                @else
                                                —
                                                @endif
                                            </p>
                                        </div>

                                        {{-- Frecuencia cardiaca --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                FC
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                {{ $signo->frecuencia_cardiaca
                                        ? $signo->frecuencia_cardiaca . ' lpm'
                                        : '—' }}
                                            </p>
                                        </div>

                                        {{-- Saturación --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                SpO₂
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                {{ $signo->spo2
                                        ? $signo->spo2 . '%'
                                        : '—' }}
                                            </p>
                                        </div>

                                        {{-- Temperatura --}}
                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Temperatura
                                            </p>

                                            <p
                                                class="mt-1 text-sm
                                           font-semibold text-slate-700">
                                                {{ $signo->temperatura
                                        ? $signo->temperatura . ' °C'
                                        : '—' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Acción --}}
                                    @if (
                                    request()->user()->isAdmin()
                                    || request()->user()->role === 'enfermero'
                                    )
                                    <div class="shrink-0">
                                        <a
                                            href="{{ route(
                                        'signos-vitales.show',
                                        $signo
                                    ) }}"
                                            class="inline-flex items-center gap-1.5
                                           rounded-lg border
                                           border-slate-200
                                           bg-white px-3 py-2
                                           text-xs font-semibold
                                           text-slate-700 shadow-sm
                                           transition
                                           hover:border-rose-200
                                           hover:bg-rose-50
                                           hover:text-rose-700">
                                            Ver detalle

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            @empty

                            <div class="px-6 py-10 text-center">
                                <p class="text-sm font-medium text-slate-600">
                                    No hay signos vitales registrados.
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Los registros de enfermería aparecerán aquí.
                                </p>
                            </div>

                            @endforelse

                        </div>
                    </details>
                    @endif

                </main>
            </div>
        </div>
    </div>



    @if (
    request()->user()->isAdmin()
    || request()->user()->isRecepcionista()
    )

    {{-- MODAL: CONTACTO --}}
    <div
        id="modal-contacto"
        class="fixed inset-0 z-50 hidden
               items-center justify-center
               bg-slate-950/40 px-4 py-8
               backdrop-blur-[2px]"
        aria-hidden="true">
        <div
            class="w-full max-w-lg overflow-hidden
                   rounded-2xl bg-white shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-modal-contacto">
            <div
                class="flex items-start justify-between
                       border-b border-slate-100
                       px-6 py-5">
                <div>
                    <h3
                        id="titulo-modal-contacto"
                        class="text-lg font-semibold text-slate-900">
                        Editar información de contacto
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Actualiza el teléfono y correo del paciente.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalContacto()"
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
                action="{{ route('pacientes.update', $pacientes) }}">
                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="seccion"
                    value="contacto">

                <div class="space-y-5 px-6 py-6">

                    <div>
                        <label
                            for="modal_telefono"
                            class="mb-1.5 block text-sm
                                   font-medium text-slate-700">
                            Celular / WhatsApp
                        </label>

                        <input
                            id="modal_telefono"
                            name="telefono"
                            type="text"
                            value="{{ old(
                                'telefono',
                                $pacientes->telefono
                            ) }}"
                            maxlength="20"
                            class="block w-full rounded-xl
                                   border-slate-300 text-sm
                                   shadow-sm
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                        @error('telefono')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Teléfono fijo --}}
                    <div>
                        <label
                            for="modal_telefono_fijo"
                            class="mb-1.5 block text-sm
               font-medium text-slate-700">
                            Teléfono
                        </label>

                        <input
                            id="modal_telefono_fijo"
                            name="telefono_fijo"
                            type="text"
                            value="{{ old(
            'telefono_fijo',
            $pacientes->telefono_fijo
        ) }}"
                            maxlength="20"
                            class="block w-full rounded-xl
               border-slate-300 text-sm
               shadow-sm
               focus:border-blue-500
               focus:ring-blue-500">

                        @error('telefono_fijo')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Teléfono secundario --}}
                    <div>
                        <label
                            for="modal_telefono_secundario"
                            class="mb-1.5 block text-sm
               font-medium text-slate-700">
                            Teléfono secundario
                        </label>

                        <input
                            id="modal_telefono_secundario"
                            name="telefono_secundario"
                            type="text"
                            value="{{ old(
            'telefono_secundario',
            $pacientes->telefono_secundario
        ) }}"
                            maxlength="20"
                            class="block w-full rounded-xl
               border-slate-300 text-sm
               shadow-sm
               focus:border-blue-500
               focus:ring-blue-500">

                        @error('telefono_secundario')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="modal_email"
                            class="mb-1.5 block text-sm
                                   font-medium text-slate-700">
                            Correo electrónico
                        </label>

                        <input
                            id="modal_email"
                            name="email"
                            type="email"
                            value="{{ old(
                                'email',
                                $pacientes->email
                            ) }}"
                            maxlength="255"
                            class="block w-full rounded-xl
                                   border-slate-300 text-sm
                                   shadow-sm
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                        @error('email')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                </div>

                <div
                    class="flex items-center justify-end gap-3
                           border-t border-slate-100
                           bg-slate-50 px-6 py-4">
                    <button
                        type="button"
                        onclick="cerrarModalContacto()"
                        class="rounded-xl border
                               border-slate-300 bg-white
                               px-4 py-2.5 text-sm
                               font-semibold text-slate-700
                               transition hover:bg-slate-50">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600
                               px-5 py-2.5 text-sm
                               font-semibold text-white
                               shadow-sm transition
                               hover:bg-blue-700">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endif

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

    @if (request()->user()->isAdmin())

    {{-- ===================================================== --}}
    {{-- MODAL: NOTAS --}}
    {{-- ===================================================== --}}
    <div
        id="modal-notas"
        class="fixed inset-0 z-50 hidden
               items-center justify-center
               bg-slate-950/40 px-4 py-8
               backdrop-blur-[2px]"
        aria-hidden="true">
        <div
            class="w-full max-w-lg overflow-hidden
                   rounded-2xl bg-white shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-modal-notas">
            {{-- Encabezado --}}
            <div
                class="flex items-start justify-between
                       border-b border-slate-100
                       px-6 py-5">
                <div>
                    <h3
                        id="titulo-modal-notas"
                        class="text-lg font-semibold text-slate-900">
                        Editar notas
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Actualiza las observaciones generales
                        del paciente.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalNotas()"
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

            {{-- Formulario --}}
            <form
                method="POST"
                action="{{ route('pacientes.update', $pacientes) }}">
                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="seccion"
                    value="notas">

                <div class="px-6 py-6">
                    <label
                        for="modal_notas"
                        class="mb-1.5 block
                               text-sm font-medium
                               text-slate-700">
                        Notas / observaciones
                    </label>

                    <textarea
                        id="modal_notas"
                        name="notas"
                        rows="7"
                        maxlength="5000"
                        placeholder="Escribe observaciones generales sobre el paciente..."
                        class="block w-full resize-y
                               rounded-xl border-slate-300
                               text-sm shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500">{{ old('notas', $pacientes->notas) }}</textarea>

                    @error('notas')
                    <p class="mt-1.5 text-xs text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                    <p class="mt-2 text-xs text-slate-400">
                        Utiliza este espacio para información general
                        relevante del paciente.
                    </p>
                </div>

                {{-- Acciones --}}
                <div
                    class="flex items-center justify-end gap-3
                           border-t border-slate-100
                           bg-slate-50 px-6 py-4">
                    <button
                        type="button"
                        onclick="cerrarModalNotas()"
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
                        class="rounded-xl bg-blue-600
                               px-5 py-2.5
                               text-sm font-semibold
                               text-white shadow-sm
                               transition
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

    @if (
    request()->user()->isAdmin()
    || request()->user()->isMedico()
    )

    <div
        id="modal-historia-clinica"
        class="fixed inset-0 z-50 hidden
           items-center justify-center
           bg-slate-950/50 p-4"
        aria-hidden="true"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-historia-clinica">

        <div
            class="max-h-[90vh] w-full max-w-4xl
               overflow-y-auto rounded-2xl
               bg-white shadow-2xl">

            {{-- Encabezado --}}
            <div
                class="sticky top-0 z-10
                   flex items-start justify-between
                   border-b border-slate-100
                   bg-white px-6 py-5">

                <div>
                    <h3
                        id="titulo-modal-historia-clinica"
                        class="text-lg font-semibold text-slate-900">

                        {{ $pacientes->historiaClinica
                        ? 'Editar historia clínica'
                        : 'Registrar historia clínica' }}
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Registra el resumen clínico principal del paciente.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalHistoriaClinica()"
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
                'pacientes.historia-clinica.update',
                $pacientes
            ) }}">

                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2">

                    {{-- Patología base --}}
                    <div>
                        <label
                            for="patologia_base"
                            class="mb-1.5 block
                               text-sm font-semibold
                               text-slate-700">
                            Patología base
                        </label>

                        <textarea
                            id="patologia_base"
                            name="patologia_base"
                            rows="6"
                            maxlength="20000"
                            placeholder="Describe las enfermedades o condiciones principales..."
                            class="block w-full resize-y
                               rounded-xl border-slate-300
                               text-sm shadow-sm
                               focus:border-cyan-500
                               focus:ring-cyan-500">{{ old(
                            'patologia_base',
                            $pacientes->historiaClinica?->patologia_base
                        ) }}</textarea>

                        @error('patologia_base')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Padecimiento actual --}}
                    <div>
                        <label
                            for="padecimiento_actual"
                            class="mb-1.5 block
                               text-sm font-semibold
                               text-slate-700">
                            Padecimiento actual
                        </label>

                        <textarea
                            id="padecimiento_actual"
                            name="padecimiento_actual"
                            rows="6"
                            maxlength="20000"
                            placeholder="Describe síntomas, evolución y motivo de atención..."
                            class="block w-full resize-y
                               rounded-xl border-slate-300
                               text-sm shadow-sm
                               focus:border-cyan-500
                               focus:ring-cyan-500">{{ old(
                            'padecimiento_actual',
                            $pacientes->historiaClinica?->padecimiento_actual
                        ) }}</textarea>

                        @error('padecimiento_actual')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Tratamientos actuales --}}
                    <div>
                        <label
                            for="tratamientos_actuales"
                            class="mb-1.5 block
                               text-sm font-semibold
                               text-slate-700">
                            Tratamientos actuales
                        </label>

                        <textarea
                            id="tratamientos_actuales"
                            name="tratamientos_actuales"
                            rows="6"
                            maxlength="20000"
                            placeholder="Medicamentos, terapias, dosis y frecuencia..."
                            class="block w-full resize-y
                               rounded-xl border-slate-300
                               text-sm shadow-sm
                               focus:border-cyan-500
                               focus:ring-cyan-500">{{ old(
                            'tratamientos_actuales',
                            $pacientes->historiaClinica?->tratamientos_actuales
                        ) }}</textarea>

                        @error('tratamientos_actuales')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Prioridad y análisis --}}
                    <div>
                        <label
                            for="prioridad_analisis_medico"
                            class="mb-1.5 block
                               text-sm font-semibold
                               text-slate-700">
                            Prioridad y análisis médico
                        </label>

                        <textarea
                            id="prioridad_analisis_medico"
                            name="prioridad_analisis_medico"
                            rows="6"
                            maxlength="20000"
                            placeholder="Registra prioridades, valoración y análisis clínico..."
                            class="block w-full resize-y
                               rounded-xl border-slate-300
                               text-sm shadow-sm
                               focus:border-cyan-500
                               focus:ring-cyan-500">{{ old(
                            'prioridad_analisis_medico',
                            $pacientes->historiaClinica?->prioridad_analisis_medico
                        ) }}</textarea>

                        @error('prioridad_analisis_medico')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                </div>

                {{-- Acciones --}}
                <div
                    class="sticky bottom-0
                       flex items-center justify-end gap-3
                       border-t border-slate-100
                       bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="cerrarModalHistoriaClinica()"
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
                        class="rounded-xl bg-cyan-600
                           px-5 py-2.5
                           text-sm font-semibold
                           text-white shadow-sm
                           transition hover:bg-cyan-700">
                        Guardar historia clínica
                    </button>
                </div>
            </form>
        </div>
    </div>

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
                            $heredofamiliares?->numero_hermanos
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
        $valoresPersonalesPatologicos,
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

    @endif

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
                                    $valoresPersonalesPatologicos,
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
                                    $valoresPersonalesNoPatologicos,
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
                            $comidasRegistradas,
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
                                                $alimentosRegistrados,
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