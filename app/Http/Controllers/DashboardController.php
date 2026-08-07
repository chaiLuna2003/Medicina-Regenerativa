<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
{
    /** @var User $user */
    $user = Auth::user();

    return match ($user->role) {
        'admin' => $this->dashboardAdministrador(),
        'recepcionista' => $this->dashboardRecepcion(),
        'medico' => view('dashboard.medico'),
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
    private function dashboardRecepcion()
    {
        $hoy = Carbon::today();

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

        $citasHoy = Citas::query()
            ->with(['paciente', 'medico'])
            ->whereDate('fecha', $hoy)
            ->orderBy('hora')
            ->get();

        $proximaCita = Citas::query()
            ->with(['paciente', 'medico'])
            ->whereDate('fecha', $hoy)
            ->whereTime('hora', '>=', now()->format('H:i:s'))
            ->whereNotIn('estado', ['cancelada', 'finalizada'])
            ->orderBy('hora')
            ->first();

        return view('dashboard.recepcion', compact(
            'totalCitasHoy',
            'citasEnEspera',
            'citasConfirmadas',
            'citasCanceladas',
            'citasHoy',
            'proximaCita',
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