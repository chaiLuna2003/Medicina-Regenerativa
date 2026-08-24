<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AntecedenteHeredofamiliar;
use App\Models\AntecedentePersonalPatologico;

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
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'apellido' => [
                'required',
                'string',
                'max:255',
            ],

            'fecha_nacimiento' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'notas' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'foto' => [
                'nullable',
                'image',
                'max:4096',
            ],

            'status' => [
                'required',
                'boolean',
            ],
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

        $user = request()->user();

        /*
    |--------------------------------------------------------------------------
    | Acceso a la ficha del paciente
    |--------------------------------------------------------------------------
    */

        if ($user->isMedico()) {
            abort_unless(
                $user->medico,
                403,
                'Tu usuario no tiene un perfil médico asociado.'
            );

            $tieneRelacionClinica = $pacientes
                ->citas()
                ->where(
                    'medico_id',
                    $user->medico->id
                )
                ->where(
                    'estado',
                    '!=',
                    'cancelada'
                )
                ->exists();

            abort_unless(
                $tieneRelacionClinica,
                403,
                'No tienes autorización para consultar este paciente.'
            );
        }

        $pacientes->load([
            'historiaClinica.antecedentesHeredofamiliares',
            'historiaClinica.antecedentesPersonalesPatologicos',

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

        /*
|--------------------------------------------------------------------------
| Última actividad clínica del paciente
|--------------------------------------------------------------------------
*/

        $actividades = collect();

        /*
 * Citas
 */
        foreach ($pacientes->citas as $cita) {
            if ($cita->fecha) {
                $fechaCita = \Carbon\Carbon::parse(
                    $cita->fecha->format('Y-m-d')
                        . ' '
                        . ($cita->hora ?? '00:00:00')
                );

                $actividades->push([
                    'tipo' => 'cita',
                    'titulo' => 'Cita médica',
                    'fecha' => $fechaCita,
                ]);
            }
        }

        /*
 * Estudios clínicos
 */
        foreach ($pacientes->estudios as $estudio) {
            if ($estudio->fecha_estudio) {
                $actividades->push([
                    'tipo' => 'estudio',
                    'titulo' => 'Estudio clínico',
                    'fecha' => \Carbon\Carbon::parse(
                        $estudio->fecha_estudio
                    ),
                ]);
            }
        }

        /*
 * Recetas
 */
        foreach ($pacientes->recetas as $receta) {
            if ($receta->fecha_expedicion) {
                $actividades->push([
                    'tipo' => 'receta',
                    'titulo' => 'Receta médica',
                    'fecha' => \Carbon\Carbon::parse(
                        $receta->fecha_expedicion
                    ),
                ]);
            }
        }

        /*
 * Signos vitales
 */
        foreach ($pacientes->signosVitales as $signo) {
            if ($signo->created_at) {
                $actividades->push([
                    'tipo' => 'signos_vitales',
                    'titulo' => 'Signos vitales',
                    'fecha' => $signo->created_at,
                ]);
            }
        }

        /*
 * Seleccionamos la actividad más reciente.
 */
        $ultimaActividad = $actividades
            ->sortByDesc('fecha')
            ->first();

        $camposHeredofamiliares =
            AntecedenteHeredofamiliar::CAMPOS;

        $camposPersonalesPatologicos =
            AntecedentePersonalPatologico::CAMPOS;

        return view(
            'pacientes.show',
            compact(
                'pacientes',
                'ultimaActividad',
                'camposHeredofamiliares',
                'camposPersonalesPatologicos'
            )
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

    public function update(
        Request $request,
        Pacientes $pacientes
    ) {
        abort_unless(
            $request->user()->isAdmin()
                || $request->user()->isRecepcionista(),
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Actualizar información de contacto
    |--------------------------------------------------------------------------
    */
        if ($request->input('seccion') === 'contacto') {
            $validated = $request->validate([
                'telefono' => [
                    'nullable',
                    'string',
                    'max:20',
                ],
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],


            ]);

            $pacientes->update($validated);

            return redirect()
                ->route('pacientes.show', $pacientes)
                ->with(
                    'success',
                    'Información de contacto actualizada correctamente.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Actualizar notas
    |--------------------------------------------------------------------------
    */
        if ($request->input('seccion') === 'notas') {
            abort_unless(
                $request->user()->isAdmin(),
                403
            );

            $validated = $request->validate([
                'notas' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],
            ]);

            $pacientes->update($validated);

            return redirect()
                ->route('pacientes.show', $pacientes)
                ->with(
                    'success',
                    'Notas actualizadas correctamente.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Actualizar datos generales desde el modal
    |--------------------------------------------------------------------------
    */
        if ($request->input('seccion') === 'generales') {
            abort_unless(
                $request->user()->isAdmin(),
                403
            );

            $validated = $request->validate([
                'nombre' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'apellido' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'fecha_nacimiento' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],
                'status' => [
                    'required',
                    'boolean',
                ],
            ]);

            $pacientes->update($validated);

            return redirect()
                ->route('pacientes.show', $pacientes)
                ->with(
                    'success',
                    'Datos generales actualizados correctamente.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Edición tradicional para recepción
    |--------------------------------------------------------------------------
    */
        if ($request->user()->isRecepcionista()) {
            $validated = $request->validate([
                'telefono' => [
                    'nullable',
                    'string',
                    'max:20',
                ],
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],
            ]);

            $pacientes->update($validated);

            return redirect()
                ->route('pacientes.show', $pacientes)
                ->with(
                    'success',
                    'Datos de contacto actualizados correctamente.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Edición completa para administrador
    |--------------------------------------------------------------------------
    */
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],
            'apellido' => [
                'required',
                'string',
                'max:255',
            ],
            'fecha_nacimiento' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'edad' => [
                'nullable',
                'integer',
                'min:0',
                'max:120',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'notas' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'foto' => [
                'nullable',
                'image',
                'max:4096',
            ],
        ]);

        if ($request->hasFile('foto')) {
            if ($pacientes->foto) {
                Storage::disk('public')
                    ->delete($pacientes->foto);
            }

            $validated['foto'] = $request
                ->file('foto')
                ->store(
                    'pacientes',
                    'public'
                );
        }

        $pacientes->update($validated);

        return redirect()
            ->route('pacientes.show', $pacientes)
            ->with(
                'success',
                'Paciente actualizado correctamente.'
            );
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
