<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AntecedenteHeredofamiliar;
use App\Models\AntecedentePersonalPatologico;
use App\Models\AntecedentePersonalNoPatologico;
use App\Models\HabitoAlimenticio;
use App\Models\ExploracionFisica;
use Illuminate\Validation\Rule;

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
        /*
    |--------------------------------------------------------------------------
    | Validación
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

            'sexo' => [
                'required',
                Rule::in(
                    array_keys(Pacientes::SEXOS)
                ),
            ],

            'categoria' => [
                'required',
                Rule::in(
                    array_keys(Pacientes::CATEGORIAS)
                ),
            ],

            /*
        |--------------------------------------------------------------------------
        | Contacto
        |--------------------------------------------------------------------------
        */

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'telefono_fijo' => [
                'nullable',
                'string',
                'max:20',
            ],

            'telefono_secundario' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            /*
        |--------------------------------------------------------------------------
        | Domicilio
        |--------------------------------------------------------------------------
        */

            'domicilio' => [
                'nullable',
                'string',
                'max:500',
            ],

            'ciudad' => [
                'nullable',
                'string',
                'max:150',
            ],

            'estado' => [
                'nullable',
                'string',
                'max:150',
            ],

            'codigo_postal' => [
                'nullable',
                'string',
                'max:10',
            ],

            'lugar_nacimiento' => [
                'nullable',
                'string',
                'max:200',
            ],

            /*
        |--------------------------------------------------------------------------
        | Información complementaria
        |--------------------------------------------------------------------------
        */

            'ocupacion' => [
                'nullable',
                'string',
                'max:200',
            ],

            'religion' => [
                'nullable',
                'string',
                'max:150',
            ],

            'estado_civil' => [
                'nullable',
                Rule::in(array_keys(Pacientes::ESTADOS_CIVILES)),
            ],

            'escolaridad' => [
                'nullable',
                Rule::in(array_keys(Pacientes::ESCOLARIDADES)),
            ],

            'tipo_sangre' => [
                'nullable',
                Rule::in(array_keys(Pacientes::TIPOS_SANGRE)),
            ],

            'alergias' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'costo_consulta_personalizado' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'finado' => [
                'nullable',
                'boolean',
            ],

            /*
        |--------------------------------------------------------------------------
        | Campos existentes
        |--------------------------------------------------------------------------
        */

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

        /*
    |--------------------------------------------------------------------------
    | Normalización
    |--------------------------------------------------------------------------
    */

        $validated['finado'] =
            $request->boolean('finado');

        $validated['categoria'] =
            $validated['categoria']
            ?? 'sin_categoria';

        /*
    |--------------------------------------------------------------------------
    | Fotografía
    |--------------------------------------------------------------------------
    */

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request
                ->file('foto')
                ->store(
                    'pacientes',
                    'public'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Crear paciente
    |--------------------------------------------------------------------------
    */

        Pacientes::create($validated);

        return redirect()
            ->route('pacientes.index')
            ->with(
                'success',
                'Paciente creado correctamente.'
            );
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

        /*
    |--------------------------------------------------------------------------
    | Cargar relaciones clínicas
    |--------------------------------------------------------------------------
    */

        $pacientes->load([
            'historiaClinica.antecedentesHeredofamiliares',
            'historiaClinica.antecedentesPersonalesPatologicos',
            'historiaClinica.antecedentesPersonalesNoPatologicos',
            'historiaClinica.habitoAlimenticio',
            'historiaClinica.antecedenteGinecoobstetrico',

            'historiaClinica.exploracionesFisicas' =>
            function ($query) {
                $query
                    ->with([
                        'cita.signoVital',
                        'medico.user',
                    ])
                    ->orderByDesc('created_at')
                    ->orderByDesc('id');
            },

            'citas' => function ($query) {
                $query
                    ->with([
                        'medico.user',
                        'receta',
                        'estudios',
                        'signoVital',
                        'exploracionFisica',
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
     * Exploraciones físicas
     */

        $exploracionesFisicas = $pacientes
            ->historiaClinica
            ?->exploracionesFisicas
            ?? collect();

        foreach ($exploracionesFisicas as $exploracion) {
            if ($exploracion->created_at) {
                $actividades->push([
                    'tipo' => 'exploracion_fisica',
                    'titulo' => 'Exploración física',
                    'fecha' => $exploracion->created_at,
                ]);
            }
        }

        /*
     * Seleccionar actividad más reciente
     */

        $ultimaActividad = $actividades
            ->sortByDesc('fecha')
            ->first();

        /*
    |--------------------------------------------------------------------------
    | Catálogos para la vista
    |--------------------------------------------------------------------------
    */

        $camposHeredofamiliares =
            AntecedenteHeredofamiliar::CAMPOS;

        $camposPersonalesPatologicos =
            AntecedentePersonalPatologico::CAMPOS;

        $camposPersonalesNoPatologicos =
            AntecedentePersonalNoPatologico::CAMPOS;

        $comidasHabitosAlimenticios =
            HabitoAlimenticio::COMIDAS;

        $camposHabitosAlimenticios =
            HabitoAlimenticio::ALIMENTOS;

        $camposExploracionFisica =
            ExploracionFisica::CAMPOS;

        $sistemasExploracionFisica =
            ExploracionFisica::SISTEMAS;

        /*
    |--------------------------------------------------------------------------
    | Mostrar ficha
    |--------------------------------------------------------------------------
    */

        return view(
            'pacientes.show',
            compact(
                'pacientes',
                'ultimaActividad',
                'camposHeredofamiliares',
                'camposPersonalesPatologicos',
                'camposPersonalesNoPatologicos',
                'comidasHabitosAlimenticios',
                'camposHabitosAlimenticios',
                'camposExploracionFisica',
                'sistemasExploracionFisica'
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
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
                || $user->isRecepcionista(),
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Modal de contacto
    |--------------------------------------------------------------------------
    */

        if ($request->input('seccion') === 'contacto') {
            $validated = $request->validate([
                'telefono' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'telefono_fijo' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'telefono_secundario' => [
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
                ->route(
                    'pacientes.show',
                    $pacientes
                )
                ->with(
                    'success',
                    'Información de contacto actualizada correctamente.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Modal de notas
    |--------------------------------------------------------------------------
    */

        if ($request->input('seccion') === 'notas') {
            abort_unless(
                $user->isAdmin(),
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
                ->route(
                    'pacientes.show',
                    $pacientes
                )
                ->with(
                    'success',
                    'Notas actualizadas correctamente.'
                );
        }

        /*
|--------------------------------------------------------------------------
| Modal de datos generales
|--------------------------------------------------------------------------
*/

        if ($request->input('seccion') === 'generales') {
            abort_unless(
                $user->isAdmin(),
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

                'sexo' => [
                    'required',
                    Rule::in(
                        array_keys(Pacientes::SEXOS)
                    ),
                ],

                'categoria' => [
                    'required',
                    Rule::in(
                        array_keys(Pacientes::CATEGORIAS)
                    ),
                ],

                'lugar_nacimiento' => [
                    'nullable',
                    'string',
                    'max:200',
                ],

                'ocupacion' => [
                    'nullable',
                    'string',
                    'max:200',
                ],

                'religion' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'estado_civil' => [
                    'nullable',
                    Rule::in(array_keys(Pacientes::ESTADOS_CIVILES)),
                ],

                'escolaridad' => [
                    'nullable',
                    Rule::in(array_keys(Pacientes::ESCOLARIDADES)),
                ],

                'tipo_sangre' => [
                    'nullable',
                    Rule::in(array_keys(Pacientes::TIPOS_SANGRE)),
                ],

                'alergias' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

                'status' => [
                    'required',
                    'boolean',
                ],

                'finado' => [
                    'required',
                    'boolean',
                ],
            ]);

            $validated['finado'] =
                $request->boolean('finado');

            $pacientes->update($validated);

            return redirect()
                ->route(
                    'pacientes.show',
                    $pacientes
                )
                ->with(
                    'success',
                    'Datos generales actualizados correctamente.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Edición tradicional para recepción
    |--------------------------------------------------------------------------
    |
    | Recepción modifica información administrativa y de contacto,
    | pero no nombre, apellidos, fecha de nacimiento ni sexo.
    |--------------------------------------------------------------------------
    */

        if ($user->isRecepcionista()) {
            $validated = $request->validate([
                'categoria' => [
                    'required',
                    Rule::in(array_keys(Pacientes::CATEGORIAS)),
                ],

                'status' => [
                    'required',
                    'boolean',
                ],

                'telefono' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'telefono_fijo' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'telefono_secundario' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],

                'domicilio' => [
                    'nullable',
                    'string',
                    'max:500',
                ],

                'ciudad' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'estado' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'codigo_postal' => [
                    'nullable',
                    'string',
                    'max:10',
                ],

                'lugar_nacimiento' => [
                    'nullable',
                    'string',
                    'max:200',
                ],

                'ocupacion' => [
                    'nullable',
                    'string',
                    'max:200',
                ],

                'religion' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'estado_civil' => [
                    'nullable',
                    Rule::in(array_keys(Pacientes::ESTADOS_CIVILES)),
                ],

                'escolaridad' => [
                    'nullable',
                    Rule::in(array_keys(Pacientes::ESCOLARIDADES)),
                ],

                'tipo_sangre' => [
                    'nullable',
                    Rule::in(array_keys(Pacientes::TIPOS_SANGRE)),
                ],

                'alergias' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

                'costo_consulta_personalizado' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:99999999.99',
                ],

                'finado' => [
                    'nullable',
                    'boolean',
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

            $validated['finado'] =
                $request->boolean('finado');

            if ($request->hasFile('foto')) {
                $fotoAnterior = $pacientes->foto;

                $validated['foto'] = $request
                    ->file('foto')
                    ->store('pacientes', 'public');

                if ($fotoAnterior) {
                    Storage::disk('public')
                        ->delete($fotoAnterior);
                }
            }

            $pacientes->update($validated);

            return redirect()
                ->route(
                    'pacientes.show',
                    $pacientes
                )
                ->with(
                    'success',
                    'Información administrativa actualizada correctamente.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Edición completa para Administración
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

            'sexo' => [
                'required',
                Rule::in(
                    array_keys(Pacientes::SEXOS)
                ),
            ],

            'categoria' => [
                'required',
                Rule::in(
                    array_keys(Pacientes::CATEGORIAS)
                ),
            ],

            'status' => [
                'required',
                'boolean',
            ],

            /*
        |--------------------------------------------------------------------------
        | Contacto
        |--------------------------------------------------------------------------
        */

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'telefono_fijo' => [
                'nullable',
                'string',
                'max:20',
            ],

            'telefono_secundario' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            /*
        |--------------------------------------------------------------------------
        | Domicilio
        |--------------------------------------------------------------------------
        */

            'domicilio' => [
                'nullable',
                'string',
                'max:500',
            ],

            'ciudad' => [
                'nullable',
                'string',
                'max:150',
            ],

            'estado' => [
                'nullable',
                'string',
                'max:150',
            ],

            'codigo_postal' => [
                'nullable',
                'string',
                'max:10',
            ],

            'lugar_nacimiento' => [
                'nullable',
                'string',
                'max:200',
            ],

            /*
        |--------------------------------------------------------------------------
        | Información complementaria
        |--------------------------------------------------------------------------
        */

            'ocupacion' => [
                'nullable',
                'string',
                'max:200',
            ],

            'religion' => [
                'nullable',
                'string',
                'max:150',
            ],

            'estado_civil' => [
                'nullable',
                Rule::in(array_keys(Pacientes::ESTADOS_CIVILES)),
            ],

            'escolaridad' => [
                'nullable',
                Rule::in(array_keys(Pacientes::ESCOLARIDADES)),
            ],

            'tipo_sangre' => [
                'nullable',
                Rule::in(array_keys(Pacientes::TIPOS_SANGRE)),
            ],

            'alergias' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'costo_consulta_personalizado' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'finado' => [
                'nullable',
                'boolean',
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

        $validated['finado'] =
            $request->boolean('finado');

        /*
    |--------------------------------------------------------------------------
    | Reemplazar fotografía
    |--------------------------------------------------------------------------
    */

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
            ->route(
                'pacientes.show',
                $pacientes
            )
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