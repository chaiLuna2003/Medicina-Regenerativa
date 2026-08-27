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