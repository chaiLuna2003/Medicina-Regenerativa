<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PacientesController extends Controller
{
    public function index(Request $request)
    {
        $pacientes = Pacientes::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

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
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'edad' => 'nullable|integer|min:0|max:120',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notas' => 'nullable|string',
            'foto' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('pacientes', 'public');
        }

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
            'fecha_nacimiento' => [
    'required',
    'date',
    'before_or_equal:today',
],
            'edad' => 'nullable|integer|min:0|max:120',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notas' => 'nullable|string',
            'foto' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('foto')) {
            if ($pacientes->foto) {
                Storage::disk('public')->delete($pacientes->foto);
            }
            $validated['foto'] = $request->file('foto')->store('pacientes', 'public');
        }

        $pacientes->update($validated);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Pacientes $pacientes)
    {
        if ($pacientes->foto) {
            Storage::disk('public')->delete($pacientes->foto);
        }

        $pacientes->delete();

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente eliminado correctamente.');
    }
}