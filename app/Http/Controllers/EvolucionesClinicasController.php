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
 * Revalidar con los datos actuales de la cita,
 * una vez adquirido el bloqueo.
 */
                Gate::authorize(
                    'crearDesdeCita',
                    [
                        CasoClinico::class,
                        $citaBloqueada,
                    ]
                );

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
    /**
     * Actualiza únicamente el contenido clínico de una evolución.
     * El caso permanece bloqueado hasta terminar la actualización.
     */
    public function update(
        UpdateEvolucionClinicaRequest $request,
        EvolucionClinica $evolucionClinica
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $evolucionClinica
        );

        $datos = $request->validated();

        DB::transaction(function () use (
            $evolucionClinica,
            $datos
        ): void {
            /*
         * El cierre utiliza este mismo bloqueo.
         * Así, cerrar el caso y editar su evolución
         * no pueden ejecutarse al mismo tiempo.
         */
            $casoBloqueado = CasoClinico::query()
                ->whereKey($evolucionClinica->caso_clinico_id)
                ->lockForUpdate()
                ->firstOrFail();

            $evolucionBloqueada = EvolucionClinica::query()
                ->whereKey($evolucionClinica->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
         * La Policy debe consultar el estado recién leído,
         * no una relación cargada antes de la transacción.
         */
            $evolucionBloqueada->setRelation(
                'casoClinico',
                $casoBloqueado
            );

            Gate::authorize(
                'update',
                $evolucionBloqueada
            );

            /*
         * Solo se actualizan los campos clínicos validados.
         * Las relaciones y los datos de auditoría no cambian.
         */
            $evolucionBloqueada->update($datos);
        });

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
     * Crea o actualiza la valoración de aparatos.
     * Impide guardar cambios después de un cierre concurrente.
     */
    public function updateAparatos(
        UpdateEvolucionAparatosRequest $request,
        EvolucionClinica $evolucionClinica
    ): RedirectResponse {
        Gate::authorize(
            'gestionarAparatos',
            $evolucionClinica
        );

        $aparatos = $request->validated('aparatos');

        DB::transaction(function () use (
            $evolucionClinica,
            $aparatos
        ): void {
            /*
         * Conservamos el mismo orden de bloqueo que en update():
         * primero el caso y después la evolución.
         */
            $casoBloqueado = CasoClinico::query()
                ->whereKey($evolucionClinica->caso_clinico_id)
                ->lockForUpdate()
                ->firstOrFail();

            $evolucionBloqueada = EvolucionClinica::query()
                ->whereKey($evolucionClinica->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
         * Revalidamos usando el estado actual del caso,
         * leído dentro de la transacción.
         */
            $evolucionBloqueada->setRelation(
                'casoClinico',
                $casoBloqueado
            );

            Gate::authorize(
                'gestionarAparatos',
                $evolucionBloqueada
            );

            foreach ($aparatos as $clave => $valoracion) {
                $evolucionBloqueada
                    ->aparatos()
                    ->updateOrCreate(
                        [
                            'aparato' => $clave,
                        ],
                        [
                            'estado' => $valoracion['estado'],
                            'observaciones' =>
                            $valoracion['observaciones'] ?? null,
                        ]
                    );
            }
        });

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
