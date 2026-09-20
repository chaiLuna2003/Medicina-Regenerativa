<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ClasificacionesController extends Controller
{
    public function index(Request $request): View
    {
        $datos = $request->validate([
            'clasificacion' => ['nullable', Rule::in(array_keys(Pacientes::CLASIFICACIONES))],
        ]);

        $seleccionada = $datos['clasificacion'] ?? null;
        $totales = [];

        foreach (Pacientes::CLASIFICACIONES as $clave => $nombre) {
            $totales[$clave] = Pacientes::query()
                ->whereJsonContains('clasificaciones', $clave)
                ->count();
        }

        $pacientes = $seleccionada
            ? Pacientes::query()
                ->whereJsonContains('clasificaciones', $seleccionada)
                ->orderBy('apellido')
                ->orderBy('nombre')
                ->paginate(20)
                ->withQueryString()
            : null;

        return view('clasificaciones.index', compact('totales', 'seleccionada', 'pacientes'));
    }
}
