<?php

namespace App\Http\Controllers;

use App\Models\Medicos;
use Illuminate\Http\Request;

class MedicosController extends Controller
{
    public function index()
    {
        $medicos = Medicos::latest()->paginate(10);
        return view('medicos.index', compact('medicos'));
    }

    public function create()
    {
        return view('medicos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'apellido_paterno' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'apellido_materno' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'especialidad' => 'required|string|max:255',
            'cedula' => 'required|string|max:50|unique:medicos,cedula',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|max:255',
            'consultorio' => 'required|string|max:100',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status');

        Medicos::create($validated);

        return redirect()->route('medicos.index')
            ->with('success', 'Médico registrado correctamente.');
    }

    public function show(Medicos $medicos)
    {
        return view('medicos.show', compact('medicos'));
    }

    public function edit(Medicos $medicos)
    {
        return view('medicos.edit', compact('medicos'));
    }

    public function update(Request $request, Medicos $medicos)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'apellido_paterno' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'apellido_materno' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'especialidad' => 'required|string|max:255',
            'cedula' => 'required|string|max:50|unique:medicos,cedula,'.$medicos->id,
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|max:255',
            'consultorio' => 'required|string|max:100',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status');

        $medicos->update($validated);

        return redirect()->route('medicos.index')
            ->with('success', 'Médico actualizado correctamente.');
    }

    public function destroy(Medicos $medicos)
    {
        $medicos->delete();

        return redirect()->route('medicos.index')
            ->with('success', 'Médico eliminado correctamente.');
    }
}