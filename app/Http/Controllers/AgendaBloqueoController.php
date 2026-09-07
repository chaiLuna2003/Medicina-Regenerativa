<?php

namespace App\Http\Controllers;

use App\Models\AgendaBloqueo;
use App\Models\Citas;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AgendaBloqueoController extends Controller
{
    private const HORA_APERTURA = '09:00';

    private const HORA_CIERRE = '21:00';

    private const DURACION_BLOQUE = 15;

    /**
     * Registrar un bloqueo de agenda.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->autorizarGestion($request);

        $datos = $this->validarDatos(
            $request
        );

        AgendaBloqueo::query()->create([
            ...$datos,
            'creado_por' => $request->user()->id,
        ]);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'La agenda del médico fue bloqueada correctamente.'
            );
    }

    /**
     * Modificar un bloqueo existente.
     */
    public function update(
        Request $request,
        AgendaBloqueo $agendaBloqueo
    ): RedirectResponse {
        $this->autorizarGestion($request);

        /*
         * El bloqueo actual se ignora al comprobar
         * traslapes contra otros bloqueos.
         */
        $datos = $this->validarDatos(
            $request,
            $agendaBloqueo
        );

        $agendaBloqueo->update($datos);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'El bloqueo fue actualizado correctamente.'
            );
    }

    /**
     * Liberar un bloqueo conservando su historial.
     */
    public function destroy(
        Request $request,
        AgendaBloqueo $agendaBloqueo
    ): RedirectResponse {
        $this->autorizarGestion($request);

        $agendaBloqueo->delete();

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'El bloqueo fue liberado correctamente.'
            );
    }

    /**
     * Validar datos, límites y traslapes.
     *
     * @return array<string, mixed>
     */
    private function validarDatos(
        Request $request,
        ?AgendaBloqueo $ignorarBloqueo = null
    ): array {
        $datos = $request->validateWithBag(
            'bloquearAgenda',
            [
                'medico_id' => [
                    'required',
                    'integer',
                    Rule::exists('medicos', 'id')
                        ->where(
                            fn ($query) => $query
                                ->where('status', true)
                        ),
                ],
                'fecha' => [
                    'required',
                    'date_format:Y-m-d',
                    'after_or_equal:today',
                ],
                'hora_inicio' => [
                    'required',
                    'date_format:H:i',
                ],
                'hora_fin' => [
                    'required',
                    'date_format:H:i',
                ],
                'motivo' => [
                    'required',
                    'string',
                    'max:500',
                ],
            ]
        );

        $inicio = Carbon::createFromFormat(
            'Y-m-d H:i',
            $datos['fecha'].' '.$datos['hora_inicio']
        );

        $fin = Carbon::createFromFormat(
            'Y-m-d H:i',
            $datos['fecha'].' '.$datos['hora_fin']
        );

        $apertura = Carbon::createFromFormat(
            'Y-m-d H:i',
            $datos['fecha'].' '.self::HORA_APERTURA
        );

        $cierre = Carbon::createFromFormat(
            'Y-m-d H:i',
            $datos['fecha'].' '.self::HORA_CIERRE
        );

        if ($fin->lte($inicio)) {
            $this->fallarValidacion(
                'hora_fin',
                'La hora final debe ser posterior a la hora inicial.'
            );
        }

        $horarioValido =
            $inicio->gte($apertura)
            && $fin->lte($cierre)
            && $inicio->minute
                % self::DURACION_BLOQUE === 0
            && $fin->minute
                % self::DURACION_BLOQUE === 0;

        if (! $horarioValido) {
            $this->fallarValidacion(
                'hora_inicio',
                'El bloqueo debe utilizar intervalos de 15 minutos '
                    .'entre las 09:00 AM y las 09:00 PM.'
            );
        }

        if ($inicio->lte(now())) {
            $this->fallarValidacion(
                'hora_inicio',
                'No puedes bloquear un horario que ya pasó.'
            );
        }

        /*
         * El bloqueo no puede cruzarse
         * con citas activas del médico.
         */
        $citaTraslapada = Citas::query()
            ->where(
                'medico_id',
                $datos['medico_id']
            )
            ->whereDate(
                'fecha',
                $datos['fecha']
            )
            ->where(
                'estado',
                '!=',
                'cancelada'
            )
            ->get([
                'hora',
                'duracion_minutos',
            ])
            ->contains(
                function (Citas $cita) use (
                    $datos,
                    $inicio,
                    $fin
                ): bool {
                    $inicioCita = Carbon::parse(
                        $datos['fecha'].' '.$cita->hora
                    );

                    $finCita = $inicioCita
                        ->copy()
                        ->addMinutes(
                            $cita->duracion_minutos ?? 15
                        );

                    return $inicioCita->lt($fin)
                        && $finCita->gt($inicio);
                }
            );

        if ($citaTraslapada) {
            $this->fallarValidacion(
                'hora_inicio',
                'No puedes bloquear este horario porque el médico '
                    .'ya tiene una cita programada.'
            );
        }

        /*
         * Tampoco puede cruzarse con otro bloqueo.
         * Durante la edición ignoramos el registro actual.
         */
        $bloqueosExistentes = AgendaBloqueo::query()
            ->where(
                'medico_id',
                $datos['medico_id']
            )
            ->whereDate(
                'fecha',
                $datos['fecha']
            )
            ->when(
                $ignorarBloqueo !== null,
                fn ($query) => $query->whereKeyNot(
                    $ignorarBloqueo->getKey()
                )
            )
            ->get([
                'hora_inicio',
                'hora_fin',
            ]);

        $bloqueoTraslapado = $bloqueosExistentes
            ->contains(
                function (AgendaBloqueo $bloqueo) use (
                    $datos,
                    $inicio,
                    $fin
                ): bool {
                    $inicioBloqueo = Carbon::parse(
                        $datos['fecha'].' '.
                            $bloqueo->hora_inicio
                    );

                    $finBloqueo = Carbon::parse(
                        $datos['fecha'].' '.
                            $bloqueo->hora_fin
                    );

                    return $inicioBloqueo->lt($fin)
                        && $finBloqueo->gt($inicio);
                }
            );

        if ($bloqueoTraslapado) {
            $this->fallarValidacion(
                'hora_inicio',
                'El horario seleccionado ya contiene otro bloqueo.'
            );
        }

        return $datos;
    }

    /**
     * Comprobar quién puede gestionar bloqueos.
     */
    private function autorizarGestion(
        Request $request
    ): void {
        abort_unless(
            in_array(
                $request->user()?->role,
                [
                    'recepcionista',
                    'admin',
                ],
                true
            ),
            403
        );
    }

    /**
     * Lanzar errores en la bolsa del modal.
     */
    private function fallarValidacion(
        string $campo,
        string $mensaje
    ): never {
        $excepcion =
            ValidationException::withMessages([
                $campo => $mensaje,
            ]);

        $excepcion->errorBag =
            'bloquearAgenda';

        throw $excepcion;
    }
}
