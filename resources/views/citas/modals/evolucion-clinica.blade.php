@if (
$puedeConsultarInformacionClinica
&& $cita->evolucionClinica
)
@php
$evolucionCita =
$cita->evolucionClinica;

$casoEvolucion =
$evolucionCita->casoClinico;

$erroresEvolucion =
$errors->getBag('evolucionClinica');

$puedeEditarEvolucion =
request()->user()->isMedico()
&& request()->user()->medico
&& (int) request()->user()->medico->id
=== (int) $evolucionCita->medico_id
&& $cita->estado !== 'cancelada';

$valorEvolucion =
function (
string $campo
) use (
$erroresEvolucion,
$evolucionCita
) {
return $erroresEvolucion->any()
? old($campo)
: $evolucionCita->{$campo};
};
@endphp

<div
    id="modal-clinico-evolucion"
    data-modal-clinico-panel="evolucion"
    class="fixed inset-0 z-50 hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="titulo-modal-evolucion">
    <div
        class="absolute inset-0 bg-slate-950/60
                   backdrop-blur-sm"
        data-cerrar-modal-clinico></div>

    <div
        class="relative flex min-h-full items-center
                   justify-center p-3 sm:p-6">
        <div
            class="relative flex max-h-[94vh] w-full
                       max-w-4xl flex-col overflow-hidden
                       rounded-2xl bg-white shadow-2xl">

            <header
                class="flex shrink-0 items-start
                           justify-between gap-5 border-b
                           border-slate-200 px-5 py-4 sm:px-6">
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                                   tracking-wide text-[#0D3B7F]">
                        Caso clínico
                    </p>

                    <h2
                        id="titulo-modal-evolucion"
                        class="mt-1 text-xl font-bold
                                   text-slate-900">
                        {{ $casoEvolucion?->nombre
                                ?? 'Evolución clínica' }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Evolución registrada el
                        {{ $evolucionCita
                                ->fecha
                                ->format('d/m/Y') }}
                    </p>
                </div>

                <button
                    type="button"
                    data-cerrar-modal-clinico
                    aria-label="Cerrar evolución"
                    class="rounded-lg p-2 text-slate-400
                               transition hover:bg-slate-100
                               hover:text-slate-700">
                    ✕
                </button>
            </header>

            <form
                method="POST"
                action="{{ route(
                        'evoluciones.update',
                        $evolucionCita
                    ) }}"
                class="flex min-h-0 flex-1 flex-col">
                @csrf
                @method('PUT')

                <div
                    class="min-h-0 flex-1 overflow-y-auto
                               bg-slate-50/70 p-5 sm:p-6">

                    {{-- Identificación --}}
                    <section
                        class="rounded-2xl border
                                   border-blue-200 bg-blue-50 p-5">
                        <div
                            class="grid gap-4
                                       sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <p
                                    class="text-xs font-semibold
                                               uppercase tracking-wide
                                               text-blue-500">
                                    Paciente
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold
                                               text-blue-950">
                                    {{ $cita->paciente?->nombre }}
                                    {{ $cita->paciente?->apellido }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-xs font-semibold
                                               uppercase tracking-wide
                                               text-blue-500">
                                    Médico
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold
                                               text-blue-950">
                                    Dr.
                                    {{ $cita->medico?->nombre }}
                                    {{ $cita->medico
                                            ?->apellido_paterno }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-xs font-semibold
                                               uppercase tracking-wide
                                               text-blue-500">
                                    Cita
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold
                                               text-blue-950">
                                    #{{ $cita->id }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-xs font-semibold
                                               uppercase tracking-wide
                                               text-blue-500">
                                    Estado del caso
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold
                                               text-blue-950">
                                    {{ $casoEvolucion?->estado
                                            === 'activo'
                                                ? 'Activo'
                                                : 'Cerrado' }}
                                </p>
                            </div>
                        </div>

                        @if ($casoEvolucion?->descripcion_inicial)
                        <div
                            class="mt-4 border-t
                                           border-blue-200 pt-4">
                            <p
                                class="text-xs font-semibold
                                               uppercase tracking-wide
                                               text-blue-500">
                                Descripción inicial
                            </p>

                            <p
                                class="mt-1 whitespace-pre-line
                                               text-sm text-blue-950">
                                {{ $casoEvolucion
                                            ->descripcion_inicial }}
                            </p>
                        </div>
                        @endif
                    </section>

                    {{-- Campos clínicos --}}
                    <section
                        class="mt-5 rounded-2xl border
                                   border-slate-200 bg-white p-5">
                        <div
                            class="flex items-center
                                       justify-between gap-4">
                            <h3
                                class="font-bold text-slate-900">
                                Valoración de la cita
                            </h3>

                            <span
                                class="rounded-full px-3 py-1
                                           text-xs font-semibold
                                           {{ $puedeEditarEvolucion
                                                ? 'bg-emerald-50 text-emerald-700'
                                                : 'bg-slate-100 text-slate-500' }}">
                                {{ $puedeEditarEvolucion
                                        ? 'Editable'
                                        : 'Solo lectura' }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label
                                    for="editar-evolucion-clinica"
                                    class="block text-sm
                                               font-semibold
                                               text-slate-700">
                                    Evolución clínica
                                </label>

                                <textarea
                                    id="editar-evolucion-clinica"
                                    name="evolucion_clinica"
                                    rows="4"
                                    required
                                    maxlength="50000"
                                    @readonly(
                                    ! $puedeEditarEvolucion
                                    )
                                    class="mt-1.5 block w-full
                                               rounded-xl border-slate-300
                                               text-sm shadow-sm
                                               read-only:bg-slate-50
                                               read-only:text-slate-600
                                               focus:border-[#0D3B7F]
                                               focus:ring-[#0D3B7F]">{{ $valorEvolucion(
                                            'evolucion_clinica'
                                        ) }}</textarea>

                                @error(
                                'evolucion_clinica',
                                'evolucionClinica'
                                )
                                <p
                                    class="mt-1 text-xs
                                                   text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div
                                class="grid gap-4
                                           md:grid-cols-2">
                                @foreach ([
                                [
                                'campo' => 'diagnostico',
                                'titulo' => 'Diagnóstico actual',
                                ],
                                [
                                'campo' => 'tratamiento',
                                'titulo' => 'Tratamiento',
                                ],
                                [
                                'campo' => 'plan_recomendaciones',
                                'titulo' => 'Plan y recomendaciones',
                                ],
                                [
                                'campo' => 'indicaciones_enfermeria',
                                'titulo' => 'Indicaciones para enfermería',
                                ],
                                ] as $campoClinico)
                                <div>
                                    <label
                                        for="editar-{{ $campoClinico['campo'] }}"
                                        class="block text-sm
                                                       font-semibold
                                                       text-slate-700">
                                        {{ $campoClinico['titulo'] }}
                                    </label>

                                    <textarea
                                        id="editar-{{ $campoClinico['campo'] }}"
                                        name="{{ $campoClinico['campo'] }}"
                                        rows="3"
                                        maxlength="50000"
                                        @readonly(
                                        ! $puedeEditarEvolucion
                                        )
                                        class="mt-1.5 block w-full
                                                       rounded-xl
                                                       border-slate-300
                                                       text-sm shadow-sm
                                                       read-only:bg-slate-50
                                                       read-only:text-slate-600
                                                       focus:border-[#0D3B7F]
                                                       focus:ring-[#0D3B7F]">{{ $valorEvolucion(
                                                    $campoClinico['campo']
                                                ) }}</textarea>

                                    @error(
                                    $campoClinico['campo'],
                                    'evolucionClinica'
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

                            <div>
                                <label
                                    for="editar-observaciones-evolucion"
                                    class="block text-sm
                                               font-semibold
                                               text-slate-700">
                                    Observaciones adicionales
                                </label>

                                <textarea
                                    id="editar-observaciones-evolucion"
                                    name="observaciones"
                                    rows="3"
                                    maxlength="50000"
                                    @readonly(
                                    ! $puedeEditarEvolucion
                                    )
                                    class="mt-1.5 block w-full
                                               rounded-xl border-slate-300
                                               text-sm shadow-sm
                                               read-only:bg-slate-50
                                               read-only:text-slate-600
                                               focus:border-[#0D3B7F]
                                               focus:ring-[#0D3B7F]">{{ $valorEvolucion(
                                            'observaciones'
                                        ) }}</textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <footer
                    class="flex shrink-0 flex-col-reverse gap-3
                               border-t border-slate-200 bg-white
                               px-5 py-4 sm:flex-row
                               sm:justify-end sm:px-6">

                    <button
                        type="button"
                        data-modal-clinico="historial-caso"
                        class="rounded-xl border border-[#0D3B7F]
           px-5 py-2.5 text-sm font-semibold
           text-[#0D3B7F] transition
           hover:bg-[#0D3B7F] hover:text-white">
                        Ver historial completo
                    </button>
                    <button
                        type="button"
                        data-cerrar-modal-clinico
                        class="rounded-xl border border-slate-300
                                   px-5 py-2.5 text-sm font-semibold
                                   text-slate-700 transition
                                   hover:bg-slate-50">
                        Cerrar
                    </button>

                    @if ($puedeEditarEvolucion)
                    <button
                        type="submit"
                        class="rounded-xl bg-[#0D3B7F]
                                       px-5 py-2.5 text-sm
                                       font-semibold text-white
                                       transition hover:bg-[#082a5d]">
                        Guardar cambios
                    </button>
                    @endif
                </footer>
            </form>
        </div>
    </div>
</div>

@if ($erroresEvolucion->any())
<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {
            abrirModalClinico('evolucion');
        }
    );
</script>
@endif
@endif