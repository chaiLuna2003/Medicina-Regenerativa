<?php

namespace App\Http\Controllers;

use App\Models\Medicos;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\Universidad;

class MedicosController extends Controller
{
    public function index(): View
    {
        $medicos = Medicos::query()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('medicos.index', compact('medicos'));
    }

    public function create(): View
    {
        $usuariosMedicos = User::query()
            ->where('role', 'medico')
            ->whereDoesntHave('medico')
            ->orderBy('name')
            ->get();

        $universidades = Universidad::query()
            ->where('status', true)
            ->orderBy('nombre')
            ->get();

        return view('medicos.create', compact(
            'usuariosMedicos',
            'universidades'
        ));
    }

    public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'user_id' => [
            'required',
            'integer',

            Rule::exists('users', 'id')->where(
                fn ($query) =>
                    $query
                        ->where('role', 'medico')
                        ->where('status', true)
            ),

            Rule::unique('medicos', 'user_id'), 
        ],

        'especialidad' => [
            'required',
            'string',
            'max:255',
        ],

        'cedula' => [
            'required',
            'string',
            'max:20',
            'regex:/^\d{7,10}$/',
            Rule::unique('medicos', 'cedula'),
        ],

        'universidad_id' => [
            'required',
            'integer',

            Rule::exists('universidades', 'id')->where(
                fn ($query) =>
                    $query->where('status', true)
            ),
        ],

        'consultorio' => [
            'required',
            'string',
            'max:100',
        ],

        'direccion' => [
            'nullable',
            'string',
            'max:500',
        ],

        'telefono' => [
            'required',
            'string',
            'max:20',
        ],

        'status' => [
            'nullable',
            'boolean',
        ],
    ]);

    /*
     * Obtenemos la cuenta seleccionada.
     * Nombre y correo tendrán una sola fuente:
     * la tabla users.
     */
    $usuario = User::query()
        ->whereKey($validated['user_id'])
        ->where('role', 'medico')
        ->where('status', true)
        ->firstOrFail();

    /*
     * Estos campos todavía existen en medicos,
     * pero sus valores provienen automáticamente
     * de la cuenta vinculada.
     */
    $validated['nombre'] = $usuario->name;
    $validated['apellido_paterno'] = null;
    $validated['apellido_materno'] = null;
    

    $validated['status'] =
        $request->boolean('status');

    Medicos::create($validated);

    return redirect()
        ->route('medicos.index')
        ->with(
            'success',
            'Médico registrado y vinculado correctamente.'
        );
}

    public function show(Medicos $medicos): View
    {
        $medicos->load('user');

        return view('medicos.show', compact('medicos'));
    }

    public function edit(Medicos $medicos): View
    {
        $universidades = Universidad::query()
            ->where('status', true)
            ->orWhere('id', $medicos->universidad_id)
            ->orderBy('nombre')
            ->get();

        $medicos->load('user');

        $usuariosMedicos = User::query()
            ->where('role', 'medico')
            ->where(function ($query) use ($medicos) {
                $query
                    ->whereDoesntHave('medico')
                    ->orWhere('id', $medicos->user_id);
            })
            ->orderBy('name')
            ->get();

        return view('medicos.edit', compact(
            'medicos',
            'usuariosMedicos',
            'universidades'
        ));
    }

    public function update(
        Request $request,
        Medicos $medicos
    ): RedirectResponse {
        $validated = $request->validate([
            'especialidad' => [
                'required',
                'string',
                'max:255',
            ],

            'cedula' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d{7,10}$/',
                Rule::unique('medicos', 'cedula')
                    ->ignore($medicos->id),
            ],

            'universidad_id' => [
                'required',
                'integer',
                Rule::exists('universidades', 'id')
                    ->where(
                        fn($query) =>
                        $query->where('status', true)
                    ),
            ],

            'consultorio' => [
                'required',
                'string',
                'max:100',
            ],

            'direccion' => [
                'nullable',
                'string',
                'max:500',
            ],

            'telefono' => [
                'required',
                'string',
                'max:20',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['status'] = $request->boolean('status');

        $medicos->update($validated);

        return redirect()
            ->route('medicos.index')
            ->with('success', 'Médico actualizado correctamente.');
    }

    public function destroy(Medicos $medicos): RedirectResponse
    {
        $medicos->delete();

        return redirect()
            ->route('medicos.index')
            ->with('success', 'Médico eliminado correctamente.');
    }
}
