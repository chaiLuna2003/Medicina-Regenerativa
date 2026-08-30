<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvolucionClinicaRequest;
use App\Models\CasoClinico;
use App\Models\Citas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\UpdateEvolucionClinicaRequest;
use App\Models\EvolucionClinica;
use App\Http\Requests\UpdateEvolucionAparatosRequest;

class EvolucionesClinicasController extends Controller
{
    /**
     * Agrega una evolución de la cita actual
     * a un caso clínico existente.
     */
    public function store(
        StoreEvolucionClinicaRequest $request,
        Citas $cita,
        CasoClinico $casoClinico
    ): RedirectResponse {
        /*
         * Comprueba que:
         * - La cita pertenezca al médico autenticado.
         * - El caso esté activo.
         * - Caso y cita pertenezcan al mismo paciente.
         * - La cita todavía no tenga evolución.
         */
        Gate::authorize(
            'agregarEvolucion',
            [
                $casoClinico,
                $cita,
            ]
        );

        $datos = $request->validated();
        $usuario = $request->user();

        DB::transaction(
            function () use (
                $cita,
                $casoClinico,
                $datos,
                $usuario
            ): void {
                /*
                 * Bloquear la cita evita que dos solicitudes
                 * creen una evolución para la misma consulta.
                 */
                $citaBloqueada = Citas::query()
                    ->whereKey($cita->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * Bloquear el caso evita agregar información
                 * mientras otra solicitud intenta cerrarlo.
                 */
                $casoBloqueado = CasoClinico::query()
                    ->whereKey($casoClinico->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if(
                    ! $casoBloqueado->estaActivo(),
                    409,
                    'El caso clínico está cerrado.'
                );

                abort_unless(
                    (int) $casoBloqueado->paciente_id
                        === (int) $citaBloqueada->paciente_id,
                    422,
                    'El caso clínico no pertenece al paciente de la cita.'
                );

                abort_if(
                    $citaBloqueada
                        ->evolucionClinica()
                        ->exists(),
                    409,
                    'Esta cita ya tiene una evolución clínica.'
                );

                /*
                 * Médico, paciente y fecha siempre proceden
                 * de la cita y no del formulario.
                 */
                $casoBloqueado
                    ->evoluciones()
                    ->create([
                        'cita_id' =>
                        $citaBloqueada->id,

                        'paciente_id' =>
                        $citaBloqueada->paciente_id,

                        'medico_id' =>
                        $citaBloqueada->medico_id,

                        'fecha' =>
                        $citaBloqueada->fecha,

                        'evolucion_clinica' =>
                        $datos['evolucion_clinica'],

                        'diagnostico' =>
                        $datos['diagnostico']
                            ?? null,

                        'tratamiento' =>
                        $datos['tratamiento']
                            ?? null,

                        'plan_recomendaciones' =>
                        $datos['plan_recomendaciones']
                            ?? null,

                        'indicaciones_enfermeria' =>
                        $datos['indicaciones_enfermeria']
                            ?? null,

                        'observaciones' =>
                        $datos['observaciones']
                            ?? null,

                        'created_by' =>
                        $usuario->id,
                    ]);
            }
        );

        return redirect()
            ->route(
                'citas.show',
                $cita
            )
            ->with(
                'success',
                'El seguimiento clínico se registró correctamente.'
            );
    }


    /**
     * Actualiza únicamente el contenido clínico
     * de una evolución existente.
     */
    public function update(
        UpdateEvolucionClinicaRequest $request,
        EvolucionClinica $evolucionClinica
    ): RedirectResponse {
        /*
     * Solamente el médico responsable de la cita
     * puede modificar esta evolución.
     */
        Gate::authorize(
            'update',
            $evolucionClinica
        );

        /*
     * El Form Request solamente devuelve campos clínicos.
     *
     * Caso, cita, paciente, médico, fecha y creador
     * permanecen inmutables.
     */
        $evolucionClinica->update(
            $request->validated()
        );

        return redirect()
            ->route(
                'citas.show',
                $evolucionClinica->cita_id
            )
            ->with(
                'success',
                'La evolución clínica se actualizó correctamente.'
            );
    }

    /**
 * Crea o actualiza la valoración completa de aparatos
 * perteneciente a una evolución clínica.
 */
public function updateAparatos(
    UpdateEvolucionAparatosRequest $request,
    EvolucionClinica $evolucionClinica
): RedirectResponse {
    /*
     * Solamente el médico responsable de la cita
     * puede administrar los aparatos.
     */
    Gate::authorize(
        'gestionarAparatos',
        $evolucionClinica
    );

    $aparatos = $request
        ->validated('aparatos');

    DB::transaction(
        function () use (
            $evolucionClinica,
            $aparatos
        ): void {
            /*
             * Evita que dos solicitudes actualicen
             * simultáneamente la misma valoración.
             */
            $evolucionBloqueada =
                EvolucionClinica::query()
                    ->whereKey(
                        $evolucionClinica->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

            foreach (
                $aparatos
                as $clave => $valoracion
            ) {
                $evolucionBloqueada
                    ->aparatos()
                    ->updateOrCreate(
                        [
                            'aparato' => $clave,
                        ],
                        [
                            'estado' =>
                                $valoracion['estado'],

                            'observaciones' =>
                                $valoracion[
                                    'observaciones'
                                ] ?? null,
                        ]
                    );
            }
        }
    );

    return redirect()
        ->route(
            'citas.show',
            $evolucionClinica->cita_id
        )
        ->with(
            'success',
            'La valoración de aparatos se actualizó correctamente.'
        );
}
}
