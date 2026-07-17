<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use Illuminate\Http\Request;

class PacientesController extends Controller
{
    public function index()
    {
        $pacientes = Pacientes::latest()->paginate(10);
        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notas' => 'nullable|string',
        ]);

        Pacientes::create($validated);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente creado correctamente.');
    }

    public function show(Pacientes $pacientes)
    {
        return view('pacientes.show', compact('pacientes'));
    }

    public function edit(Pacientes $pacientes)
    {
        return view('pacientes.edit', compact('pacientes'));
    }

    public function update(Request $request, Pacientes $pacientes)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notas' => 'nullable|string',
        ]);

        $pacientes->update($validated);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Pacientes $pacientes)
    {
        $pacientes->delete();

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente eliminado correctamente.');
    }
}