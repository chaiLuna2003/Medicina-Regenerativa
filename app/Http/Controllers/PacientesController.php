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
    $pacientes->load([
        'citas' => function ($query) {
            $query
                ->with([
                    'medico.user',
                    'receta',
                    'estudios',
                    'signoVital',
                ])
                ->orderByDesc('fecha')
                ->orderByDesc('hora');
        },

        'estudios' => function ($query) {
            $query
                ->with([
                    'cita.medico.user',
                    'subidoPor',
                ])
                ->orderByDesc('fecha_estudio')
                ->orderByDesc('id');
        },

        'recetas' => function ($query) {
            $query
                ->with([
                    'cita.medico.user',
                ])
                ->orderByDesc('fecha_expedicion')
                ->orderByDesc('id');
        },

        'signosVitales' => function ($query) {
            $query
                ->with([
                    'cita.medico.user',
                ])
                ->orderByDesc('created_at');
        },
    ]);

    return view(
        'pacientes.show',
        compact('pacientes')
    );
}

    public function edit(Pacientes $pacientes)
    {
        abort_unless(
            request()->user()->isAdmin() || request()->user()->isRecepcionista(),
            403
        );

        return view('pacientes.edit', compact('pacientes'));
    }

    public function update(Request $request, Pacientes $pacientes)
    {
        abort_unless(
            $request->user()->isAdmin() || $request->user()->isRecepcionista(),
            403
        );

        if ($request->user()->isRecepcionista()) {
            $validated = $request->validate([
                'telefono' => ['nullable', 'string', 'max:20'],
                'email' => ['nullable', 'email', 'max:255'],
            ]);

            $pacientes->update($validated);

            return redirect()->route('pacientes.index')
                ->with('success', 'Datos de contacto actualizados correctamente.');
        }

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
