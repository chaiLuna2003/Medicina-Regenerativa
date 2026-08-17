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
        'admin' => $this->dashboardAdministrador(),
        'recepcionista' => $this->dashboardRecepcion($request),
        'medico' => $this->dashboardMedico($user),
        'enfermero' => $this->dashboardEnfermero(),
        default => abort(403),
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
    ]);

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
    $citasSeleccionadas = Citas::query()
        ->with([
            'paciente',
            'medico',
        ])
        ->whereDate(
            'fecha',
            $fechaSeleccionada->toDateString()
        )
        ->orderBy('hora', 'asc')
        ->get();

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

    $citasPorDia = Citas::query()
        ->select([
            'id',
            'fecha',
            'estado',
            'modalidad',
        ])
        ->whereBetween('fecha', [
            $inicioMes->toDateString(),
            $finMes->toDateString(),
        ])
        ->orderBy('fecha')
        ->get()
        ->groupBy(
            fn (Citas $cita) => $cita->fecha->format('Y-m-d')
        )
        ->map(
            fn ($grupo) => [
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

    return view(
        'dashboard.recepcion',
        compact(
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
        )
    );
}

    
/**
 * Dashboard del médico autenticado.
 */
private function dashboardMedico(User $user)
{
    /*
     * Médico vinculado con el usuario autenticado.
     */
    $medico = $user->medico;

    /*
     * Valores predeterminados por si el usuario
     * todavía no tiene un perfil médico asociado.
     */
    $citas = collect();

    if ($medico !== null) {
        /*
         * El calendario conserva contexto reciente y permite planear
         * los siguientes meses sin descargar todo el historial clínico.
         */
        $inicioCalendario = now()->subMonth()->startOfMonth();
        $finCalendario = now()->addMonths(6)->endOfMonth();

        $citas = Citas::query()
            ->with([
                'paciente' => function ($query) {
                    $query->withCount('citas');
                },
                'signoVital.enfermero',
            ])
            ->where('medico_id', $medico->id)
            ->where('estado', '!=', 'cancelada')
            ->whereBetween('fecha', [
                $inicioCalendario->toDateString(),
                $finCalendario->toDateString(),
            ])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();
    }

    return view('dashboard.medico', compact(
        'medico',
        'citas',
    ));
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

}
