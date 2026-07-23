<?php

namespace App\Http\Controllers;

use App\Models\Citas;
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
            'admin' => view('dashboard.admin'),

            'recepcionista' => $this->dashboardRecepcion(),

            'medico' => view('dashboard.medico'),

            'enfermero' => view('dashboard.enfermeria'),

            default => abort(403),
        };
    }

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
}