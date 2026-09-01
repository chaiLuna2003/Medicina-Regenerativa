<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        return match ($user->role) {
            'admin' =>
            $this->dashboardAdministrador(),

            'recepcionista' =>
            $this->dashboardRecepcion($request),

            'medico' =>
            $this->dashboardMedico(
                $user,
                $request
            ),

            'enfermero' =>
            $this->dashboardEnfermero(),

            default =>
            abort(403),
        };
    }

    /**
     * Dashboard del administrador.
     * En esta funcion devolvemos la vista del administrador por medio del total de medicos, pacientes etc
     */
    private function dashboardAdministrador()
    {
        $hoy = Carbon::today();

        $totalPacientes = Pacientes::query()->count();

        $pacientesNuevos = Pacientes::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $totalMedicos = Medicos::query()->count();

        $medicosActivos = Medicos::query()
            ->where('status', true)
            ->count();

        $totalUsuarios = User::query()->count();

        $totalCitasHoy = Citas::query()
            ->whereDate('fecha', $hoy)
            ->count();

        $citasConfirmadasHoy = Citas::query()
            ->whereDate('fecha', $hoy)
            ->where('estado', 'confirmada')
            ->count();

        $citasPendientesHoy = Citas::query()
            ->whereDate('fecha', $hoy)
            ->where('estado', 'en_espera')
            ->count();

        $ultimosPacientes = Pacientes::query()
            ->latest()
            ->take(5)
            ->get();

        $proximasCitas = Citas::query()
            ->with(['paciente', 'medico'])
            ->where(function ($query) use ($hoy) {
                $query->whereDate('fecha', '>', $hoy)
                    ->orWhere(function ($subquery) use ($hoy) {
                        $subquery
                            ->whereDate('fecha', $hoy)
                            ->whereTime('hora', '>=', now()->format('H:i:s'));
                    });
            })
            ->whereNotIn('estado', ['cancelada', 'finalizada'])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->take(5)
            ->get();
        $cumpleanosPacientes =
            $this->obtenerCumpleanosPacientes(7);

        return view('dashboard.admin', compact(
            'totalPacientes',
            'pacientesNuevos',
            'totalMedicos',
            'medicosActivos',
            'totalUsuarios',
            'totalCitasHoy',
            'citasConfirmadasHoy',
            'citasPendientesHoy',
            'ultimosPacientes',
            'proximasCitas',
            'cumpleanosPacientes',
        ));
    }

    /**
     * Dashboard de recepción.
     */
    private function dashboardRecepcion(Request $request)
    {
        $request->validate([
            'fecha' => ['nullable', 'date_format:Y-m-d'],
            'mes' => ['nullable', 'date_format:Y-m'],
            'medico_id' => [
                'nullable',
                'integer',
                'exists:medicos,id',
            ],
        ]);

        $medicoSeleccionadoId = $request->filled('medico_id')
            ? $request->integer('medico_id')
            : null;

        $hoy = Carbon::today();

        /*
     * Día seleccionado.
     * Si no se recibe una fecha, se utiliza el día actual.
     */
        $fechaSeleccionada = $request->filled('fecha')
            ? Carbon::createFromFormat(
                'Y-m-d',
                $request->input('fecha')
            )->startOfDay()
            : $hoy->copy();

        /*
     * Mes mostrado en el calendario.
     */
        if ($request->filled('mes')) {
            $mesCalendario = Carbon::createFromFormat(
                'Y-m-d',
                $request->input('mes') . '-01'
            )->startOfMonth();
        } else {
            $mesCalendario = $fechaSeleccionada
                ->copy()
                ->startOfMonth();
        }

        /*
     * Indicadores correspondientes al día actual.
     */
        $totalCitasHoy = Citas::query()
            ->whereDate('fecha', $hoy)
            ->count();

        $citasEnEspera = Citas::query()
            ->whereDate('fecha', $hoy)
            ->where('estado', 'en_espera')
            ->count();

        $citasConfirmadas = Citas::query()
            ->whereDate('fecha', $hoy)
            ->where('estado', 'confirmada')
            ->count();

        $citasCanceladas = Citas::query()
            ->whereDate('fecha', $hoy)
            ->where('estado', 'cancelada')
            ->count();

        /*
     * Citas correspondientes al día seleccionado.
     * Se ordenan por hora, no por fecha de creación.
     */
        $consultaCitasSeleccionadas = Citas::query()
            ->with([
                'paciente',
                'medico',
            ])
            ->whereDate(
                'fecha',
                $fechaSeleccionada->toDateString()
            );

        if ($medicoSeleccionadoId !== null) {
            $consultaCitasSeleccionadas->where(
                'medico_id',
                $medicoSeleccionadoId
            );
        }

        $citasSeleccionadas = $consultaCitasSeleccionadas
            ->orderBy('hora', 'asc')
            ->get();

        /*
        * Cantidad de citas por médico en la fecha seleccionada.
    */
        $conteosPorMedico = Citas::query()
            ->whereDate(
                'fecha',
                $fechaSeleccionada->toDateString()
            )
            ->selectRaw('medico_id, COUNT(*) as total')
            ->groupBy('medico_id')
            ->pluck('total', 'medico_id');

        /*
 * Médicos disponibles para el filtro.
 */
        $medicosFiltro = Medicos::query()
            ->where('status', true)
            ->orderBy('nombre')
            ->orderBy('apellido_paterno')
            ->get()
            ->map(function (Medicos $medico) use ($conteosPorMedico) {
                $medico->citas_fecha_count = (int) (
                    $conteosPorMedico->get($medico->id) ?? 0
                );

                return $medico;
            });

        /*
 * Construye los horarios de la agenda visual.
 * Las citas se manejan en intervalos de 15 minutos,
 * desde las 09:00 hasta las 20:45.
 */
        $horasAgenda = collect();

        $horaAgenda = Carbon::createFromTime(9, 0);
        $ultimaHoraAgenda = Carbon::createFromTime(20, 45);

        while ($horaAgenda->lte($ultimaHoraAgenda)) {
            $horasAgenda->push(
                $horaAgenda->format('H:i')
            );

            $horaAgenda->addMinutes(15);
        }

        /*
 * Si se seleccionó un médico, la agenda mostrará
 * únicamente su columna. En caso contrario,
 * mostrará todos los médicos activos.
 */
        $medicosAgenda = $medicoSeleccionadoId !== null
            ? $medicosFiltro
            ->where('id', $medicoSeleccionadoId)
            ->values()
            : $medicosFiltro->values();

        /*
 * Distribuye cada cita entre todos los bloques
 * de 15 minutos que ocupa.
 *
 * Ejemplo:
 * 09:00 con duración de 60 minutos ocupará:
 * 09:00, 09:15, 09:30 y 09:45.
 */
        $citasAgenda = collect();

        foreach ($citasSeleccionadas as $cita) {
            /*
     * Las citas canceladas permanecen en el historial,
     * pero no bloquean espacios en la cuadrícula.
     */
            if ($cita->estado === 'cancelada') {
                continue;
            }

            $inicioCita = Carbon::parse(
                $cita->hora
            );

            $duracionCita =
                $cita->duracion_minutos ?? 15;

            $cantidadBloques = max(
                1,
                (int) ceil(
                    $duracionCita / 15
                )
            );

            for (
                $indice = 0;
                $indice < $cantidadBloques;
                $indice++
            ) {
                $horaBloque = $inicioCita
                    ->copy()
                    ->addMinutes(
                        $indice * 15
                    )
                    ->format('H:i');

                $llaveBloque =
                    $cita->medico_id
                    . '|'
                    . $horaBloque;

                $citasAgenda->put(
                    $llaveBloque,
                    [
                        'cita' => $cita,
                        'es_inicio' => $indice === 0,
                        'es_final' =>
                        $indice ===
                            $cantidadBloques - 1,
                        'indice' => $indice,
                        'total_bloques' =>
                        $cantidadBloques,
                    ]
                );
            }
        }

        /*
     * Todas las citas del mes para marcar los días
     * que tienen actividad en el calendario.
     */
        $inicioMes = $mesCalendario
            ->copy()
            ->startOfMonth();

        $finMes = $mesCalendario
            ->copy()
            ->endOfMonth();

        $consultaCitasMes = Citas::query()
            ->select([
                'id',
                'fecha',
                'estado',
                'modalidad',
                'medico_id',
            ])
            ->whereBetween('fecha', [
                $inicioMes->toDateString(),
                $finMes->toDateString(),
            ]);

        if ($medicoSeleccionadoId !== null) {
            $consultaCitasMes->where(
                'medico_id',
                $medicoSeleccionadoId
            );
        }

        $citasPorDia = $consultaCitasMes
            ->orderBy('fecha')
            ->get()
            ->groupBy(
                fn(Citas $cita) => $cita->fecha->format('Y-m-d')
            )
            ->map(
                fn($grupo) => [
                    'total' => $grupo->count(),

                    'activas' => $grupo
                        ->where('estado', '!=', 'cancelada')
                        ->count(),

                    'canceladas' => $grupo
                        ->where('estado', 'cancelada')
                        ->count(),

                    'videoconsultas' => $grupo
                        ->where('modalidad', 'videoconsulta')
                        ->count(),
                ]
            );

        /*
     * Construcción de la cuadrícula completa.
     * Incluye algunos días del mes anterior y siguiente
     * para completar las semanas del calendario.
     */
        $inicioCuadricula = $inicioMes
            ->copy()
            ->startOfWeek(Carbon::MONDAY);

        $finCuadricula = $finMes
            ->copy()
            ->endOfWeek(Carbon::SUNDAY);

        $diasCalendario = collect();

        for (
            $dia = $inicioCuadricula->copy();
            $dia->lte($finCuadricula);
            $dia->addDay()
        ) {
            $diasCalendario->push($dia->copy());
        }

        /*
     * Meses utilizados por los botones de navegación.
     */
        $mesAnterior = $mesCalendario
            ->copy()
            ->subMonth()
            ->startOfMonth();

        $mesSiguiente = $mesCalendario
            ->copy()
            ->addMonth()
            ->startOfMonth();

        /*
     * Próxima cita del día seleccionado.
     */
        $consultaProximaCita = Citas::query()
            ->with([
                'paciente',
                'medico',
            ])
            ->whereDate(
                'fecha',
                $fechaSeleccionada->toDateString()
            )
            ->whereNotIn('estado', [
                'cancelada',
                'finalizada',
            ]);


        if ($medicoSeleccionadoId !== null) {
            $consultaProximaCita->where(
                'medico_id',
                $medicoSeleccionadoId
            );
        }
        /*
     * Si se está consultando hoy, solo tomamos horarios
     * posteriores a la hora actual.
     */
        if ($fechaSeleccionada->isToday()) {
            $consultaProximaCita->whereTime(
                'hora',
                '>=',
                now()->format('H:i:s')
            );
        }

        $proximaCita = $consultaProximaCita
            ->orderBy('hora', 'asc')
            ->first();

        $cumpleanosPacientes =
            $this->obtenerCumpleanosPacientes(7);

        $pacienteCitaAnterior = null;

        $pacienteCitaAnteriorId =
            $request->session()->getOldInput(
                'paciente_id'
            );

        if ($pacienteCitaAnteriorId) {
            $pacienteCitaAnterior = Pacientes::query()
                ->select([
                    'id',
                    'nombre',
                    'apellido',
                ])
                ->find($pacienteCitaAnteriorId);
        }

        return view(
            'dashboard.recepcion',
            compact(
                'medicosFiltro',
                'medicoSeleccionadoId',
                'totalCitasHoy',
                'citasEnEspera',
                'citasConfirmadas',
                'citasCanceladas',
                'citasSeleccionadas',
                'fechaSeleccionada',
                'mesCalendario',
                'mesAnterior',
                'mesSiguiente',
                'diasCalendario',
                'citasPorDia',
                'proximaCita',
                'citasAgenda',
                'horasAgenda',
                'medicosAgenda',
                'cumpleanosPacientes',
                'pacienteCitaAnterior',
            )
        );
    }


    /**
     * Dashboard del médico autenticado.
     */
    /**
     * Dashboard del médico autenticado.
     */
    private function dashboardMedico(
        User $user,
        Request $request
    ) {
        $request->validate([
            'fecha' => [
                'nullable',
                'date_format:Y-m-d',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Médico autenticado
    |--------------------------------------------------------------------------
    */

        $medico = $user->medico;

        /*
    |--------------------------------------------------------------------------
    | Fecha seleccionada
    |--------------------------------------------------------------------------
    */

        $fechaSeleccionada = $request->filled('fecha')
            ? Carbon::createFromFormat(
                'Y-m-d',
                $request->input('fecha')
            )->startOfDay()
            : Carbon::today();

        /*
    |--------------------------------------------------------------------------
    | Valores predeterminados
    |--------------------------------------------------------------------------
    */

        $citas = collect();
        $citasFinalizadas = collect();
        $citasSeleccionadas = collect();
        $medicosAgenda = collect();
        $citasAgenda = collect();
        $horasAgenda = collect();

        /*
    |--------------------------------------------------------------------------
    | Horarios de 15 minutos
    |--------------------------------------------------------------------------
    */

        $horaAgenda = Carbon::createFromTime(
            9,
            0
        );

        $ultimaHoraAgenda = Carbon::createFromTime(
            20,
            45
        );

        while ($horaAgenda->lte($ultimaHoraAgenda)) {
            $horasAgenda->push(
                $horaAgenda->format('H:i')
            );

            $horaAgenda->addMinutes(15);
        }

        if ($medico !== null) {
            /*
        |--------------------------------------------------------------------------
        | Agenda general existente
        |--------------------------------------------------------------------------
        |
        | Se conserva para los indicadores y modales actuales mientras
        | terminamos de migrar la interfaz médica.
        |
        */

            $inicioCalendario = now()
                ->subMonth()
                ->startOfMonth();

            $finCalendario = now()
                ->addMonths(6)
                ->endOfMonth();

            $todasLasCitas = Citas::query()
                ->with([
                    'paciente' => function ($query) {
                        $query->withCount('citas');
                    },
                    'signoVital.enfermero',
                ])
                ->where(
                    'medico_id',
                    $medico->id
                )
                ->where(
                    'estado',
                    '!=',
                    'cancelada'
                )
                ->whereBetween('fecha', [
                    $inicioCalendario->toDateString(),
                    $finCalendario->toDateString(),
                ])
                ->orderBy('fecha')
                ->orderBy('hora')
                ->get();

            [
                $citas,
                $citasFinalizadas,
            ] = $todasLasCitas->partition(
                function (Citas $cita) {
                    $fechaHoraFinal = Carbon::parse(
                        $cita->fecha->format('Y-m-d')
                            . ' '
                            . Carbon::parse(
                                $cita->hora
                            )->format('H:i:s')
                    )->addMinutes(
                        $cita->duracion_minutos ?? 15
                    );

                    return $cita->estado !== 'finalizada'
                        && $fechaHoraFinal->gte(now());
                }
            );

            /*
        |--------------------------------------------------------------------------
        | Citas del día seleccionado
        |--------------------------------------------------------------------------
        |
        | La restricción por medico_id se aplica en el servidor.
        | El médico no puede alterar la URL para consultar otra agenda.
        |
        */

            $citasSeleccionadas = Citas::query()
                ->with([
                    'paciente',
                    'medico',
                ])
                ->where(
                    'medico_id',
                    $medico->id
                )
                ->whereDate(
                    'fecha',
                    $fechaSeleccionada->toDateString()
                )
                ->orderBy('hora')
                ->get();

            /*
        |--------------------------------------------------------------------------
        | Única columna médica
        |--------------------------------------------------------------------------
        */

            $medicosAgenda = collect([
                $medico,
            ]);

            /*
        |--------------------------------------------------------------------------
        | Distribución en bloques de 15 minutos
        |--------------------------------------------------------------------------
        */

            foreach ($citasSeleccionadas as $cita) {
                /*
     * Las citas canceladas permanecen en el historial,
     * pero no bloquean espacios en la cuadrícula.
     */
                if ($cita->estado === 'cancelada') {
                    continue;
                }

                $inicioCita = Carbon::parse(
                    $cita->hora
                );

                $duracionCita =
                    $cita->duracion_minutos ?? 15;

                $cantidadBloques = max(
                    1,
                    (int) ceil(
                        $duracionCita / 15
                    )
                );

                for (
                    $indice = 0;
                    $indice < $cantidadBloques;
                    $indice++
                ) {
                    $horaBloque = $inicioCita
                        ->copy()
                        ->addMinutes(
                            $indice * 15
                        )
                        ->format('H:i');

                    $llaveBloque =
                        $medico->id
                        . '|'
                        . $horaBloque;

                    $citasAgenda->put(
                        $llaveBloque,
                        [
                            'cita' => $cita,
                            'es_inicio' =>
                            $indice === 0,
                            'es_final' =>
                            $indice
                                === $cantidadBloques - 1,
                            'indice' =>
                            $indice,
                            'total_bloques' =>
                            $cantidadBloques,
                        ]
                    );
                }
            }
        }

        return view(
            'Dashboard.medico',
            compact(
                'medico',
                'citas',
                'citasFinalizadas',
                'citasSeleccionadas',
                'fechaSeleccionada',
                'medicosAgenda',
                'horasAgenda',
                'citasAgenda',
            )
        );
    }

    /**
     * Dashboard del personal de enfermería.
     */
    private function dashboardEnfermero()
    {
        $hoy = Carbon::today();

        /*
     * Todas las citas de hoy, junto con paciente, médico
     * y posibles signos vitales registrados.
     */
        $citasHoy = Citas::query()
            ->with([
                'paciente',
                'medico',
                'signoVital',
            ])
            ->whereDate('fecha', $hoy)
            ->where('estado', '!=', 'cancelada')
            ->orderBy('hora')
            ->get();

        /*
     * Citas que todavía no tienen signos vitales.
     */
        $citasPendientes = Citas::query()
            ->whereDate('fecha', $hoy)
            ->where('estado', '!=', 'cancelada')
            ->whereDoesntHave('signoVital')
            ->count();

        /*
     * Citas que ya tienen signos vitales registrados.
     */
        $valoracionesRealizadas = Citas::query()
            ->whereDate('fecha', $hoy)
            ->whereHas('signoVital')
            ->count();

        /*
     * Citas canceladas durante el día.
     */
        $citasCanceladas = Citas::query()
            ->whereDate('fecha', $hoy)
            ->where('estado', 'cancelada')
            ->count();

        /*
     * Siguiente cita pendiente de valoración.
     */
        $proximaCita = Citas::query()
            ->with([
                'paciente',
                'medico',
                'signoVital',
            ])
            ->whereDate('fecha', $hoy)
            ->where('estado', '!=', 'cancelada')
            ->whereDoesntHave('signoVital')
            ->whereTime('hora', '>=', now()->format('H:i:s'))
            ->orderBy('hora')
            ->first();

        return view('dashboard.enfermeria', compact(
            'citasHoy',
            'citasPendientes',
            'valoracionesRealizadas',
            'citasCanceladas',
            'proximaCita',
        ));
    }

    /**
     * Obtiene los pacientes que cumplen años
     * desde hoy hasta los próximos N días.
     */
    private function obtenerCumpleanosPacientes(
        int $dias = 7
    ) {
        $hoy = Carbon::today();

        return Pacientes::query()
            ->whereNotNull('fecha_nacimiento')
            ->where('status', true)
            ->get()
            ->map(function (Pacientes $paciente) use ($hoy) {
                $nacimiento = Carbon::parse(
                    $paciente->fecha_nacimiento
                );

                /*
             * Calculamos el próximo cumpleaños.
             *
             * Para nacidos el 29 de febrero,
             * en años no bisiestos usamos el 28.
             */
                $diaCumple = $nacimiento->day;

                if (
                    $nacimiento->month === 2
                    && $nacimiento->day === 29
                    && !Carbon::create($hoy->year, 1, 1)
                        ->isLeapYear()
                ) {
                    $diaCumple = 28;
                }

                $proximoCumpleanos = Carbon::create(
                    $hoy->year,
                    $nacimiento->month,
                    $diaCumple
                )->startOfDay();

                /*
             * Si ya pasó este año,
             * calculamos el del siguiente año.
             */
                if ($proximoCumpleanos->lt($hoy)) {
                    $anioSiguiente = $hoy->year + 1;

                    $diaCumple = $nacimiento->day;

                    if (
                        $nacimiento->month === 2
                        && $nacimiento->day === 29
                        && !Carbon::create(
                            $anioSiguiente,
                            1,
                            1
                        )->isLeapYear()
                    ) {
                        $diaCumple = 28;
                    }

                    $proximoCumpleanos = Carbon::create(
                        $anioSiguiente,
                        $nacimiento->month,
                        $diaCumple
                    )->startOfDay();
                }

                $paciente->proximo_cumpleanos =
                    $proximoCumpleanos;

                $paciente->dias_para_cumpleanos =
                    $hoy->diffInDays(
                        $proximoCumpleanos,
                        false
                    );

                /*
             * Edad que cumplirá ese día.
             */
                $paciente->edad_cumpleanos =
                    $proximoCumpleanos->year
                    - $nacimiento->year;

                return $paciente;
            })
            ->filter(
                fn(Pacientes $paciente) =>
                $paciente->dias_para_cumpleanos >= 0
                    && $paciente->dias_para_cumpleanos <= $dias
            )
            ->sortBy('proximo_cumpleanos')
            ->values();
    }
}
