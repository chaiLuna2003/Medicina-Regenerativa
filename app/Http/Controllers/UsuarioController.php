<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $usuarios = User::query()
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = $request->string('buscar')->toString();

                $query->where(function ($query) use ($buscar) {
                    $query->where('name', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where(
                    'role',
                    $request->string('role')->toString()
                );
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        return view('usuarios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'medico',
                    'enfermero',
                    'recepcionista',
                ]),
            ],
            'password' => ['required', 'confirmed', 'min:8'],
            'status' => ['required', 'boolean'],
        ]);

        User::create($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(
        Request $request,
        User $usuario
    ): RedirectResponse {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'medico',
                    'enfermero',
                    'recepcionista',
                ]),
            ],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'status' => ['required', 'boolean'],
        ]);

        $usuarioAutenticado = $request->user();

        if (
            $usuarioAutenticado &&
            $usuario->is($usuarioAutenticado)
        ) {
            $datos['role'] = 'admin';
            $datos['status'] = true;
        }

        $dejaDeSerAdminActivo =
            $usuario->isAdmin() &&
            $usuario->isActive() &&
            (
                $datos['role'] !== 'admin' ||
                ! $datos['status']
            );

        if ($dejaDeSerAdminActivo) {
            $hayOtroAdministrador = User::query()
                ->where('role', 'admin')
                ->where('status', true)
                ->whereKeyNot($usuario->id)
                ->exists();

            if (! $hayOtroAdministrador) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'role' =>
                            'Debe existir al menos un administrador activo.',
                    ]);
            }
        }

        if (blank($datos['password'] ?? null)) {
            unset($datos['password']);
        }

        $usuario->update($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }
}