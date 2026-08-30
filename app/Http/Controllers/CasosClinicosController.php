<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCasoClinicoRequest;
use App\Models\CasoClinico;
use App\Models\Citas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CasosClinicosController extends Controller
{
    /**
     * Abre un caso clínico y registra su primera evolución.
     */
    public function store(
        StoreCasoClinicoRequest $request,
        Citas $cita
    ): RedirectResponse {
        /*
         * La Policy comprueba que:
         * - El usuario sea médico.
         * - Tenga un perfil médico.
         * - Sea el médico asignado a la cita.
         * - La cita no esté cancelada.
         */
        Gate::authorize(
            'crearDesdeCita',
            [
                CasoClinico::class,
                $cita,
            ]
        );

        $datos = $request->validated();
        $usuario = $request->user();

        DB::transaction(
            function () use (
                $cita,
                $datos,
                $usuario
            ): void {
                /*
                 * Bloqueamos la cita durante la operación para
                 * impedir dos evoluciones simultáneas.
                 */
                $citaBloqueada = Citas::query()
                    ->whereKey($cita->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if(
                    $citaBloqueada
                        ->evolucionClinica()
                        ->exists(),
                    409,
                    'Esta cita ya tiene una evolución clínica.'
                );

                /*
                 * Crear el caso clínico.
                 */
                $casoClinico = CasoClinico::create([
                    'paciente_id' =>
                        $citaBloqueada->paciente_id,

                    'nombre' =>
                        $datos['nombre'],

                    'descripcion_inicial' =>
                        $datos['descripcion_inicial']
                            ?? null,

                    'fecha_inicio' =>
                        $citaBloqueada->fecha,

                    'estado' =>
                        CasoClinico::ESTADO_ACTIVO,

                    'created_by' =>
                        $usuario->id,
                ]);

                /*
                 * Crear la primera evolución dentro del caso.
                 *
                 * Paciente, médico y fecha se obtienen
                 * exclusivamente de la cita.
                 */
                $casoClinico
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
                'El caso clínico y su primera evolución se registraron correctamente.'
            );
    }
}