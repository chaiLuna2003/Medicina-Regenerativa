<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\Pacientes;
use App\Models\User;
use App\Models\Medicos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CitasController extends Controller
{
    /**
     * Mostrar el listado de citas.
     */
    public function index(): View
    {
        $citas = Citas::query()
            ->with(['paciente', 'medico'])
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->paginate(15);

        return view('citas.index', compact('citas'));
    }

    /**
     * Mostrar el formulario para crear una cita.
     */
    public function create(): View
    {
        $medicos = Medicos::query()
            ->where('status', true)
            ->orderBy('nombre')
            ->orderBy('apellido_paterno')
            ->get();

        return view('citas.create', compact('medicos'));
    }

    /**
     * Guardar una nueva cita.
     */
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'paciente_id' => [
    'required',
    'integer',
    Rule::exists('pacientes', 'id'),
],
            'medico_id' => [
                'required',
                'integer',
                'exists:medicos,id',
            ],
            'fecha' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'hora' => [
                'required',
                'date_format:H:i',
            ],
            'motivo' => [
                'required',
                'string',
                'max:255',
            ],
            'notas' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'estado' => [
                'required',
                'in:programada,confirmada,en_espera,en_consulta,finalizada,cancelada',
            ],
        ]);

        $medicoValido = Medicos::query()
            ->whereKey($datos['medico_id'])
            ->where('status', true)
            ->exists();


        if (! $medicoValido) {
            return back()
                ->withErrors([
                    'medico_id' => 'El usuario seleccionado no es un médico activo.',
                ])
                ->withInput();
        }

        $horarioOcupado = Citas::query()
            ->where('medico_id', $datos['medico_id'])
            ->whereDate('fecha', $datos['fecha'])
            ->whereTime('hora', $datos['hora'])
            ->whereNotIn('estado', ['cancelada'])
            ->exists();

        if ($horarioOcupado) {
            return back()
                ->withErrors([
                    'hora' => 'El médico ya tiene una cita registrada en esa fecha y hora.',
                ])
                ->withInput();
        }

        $datos['created_by'] = auth()->id();

        Citas::create($datos);

        return redirect()
            ->route('citas.index')
            ->with('success', 'La cita se registró correctamente.');
    }

    /**
     * Mostrar una cita.
     */
    public function show(Citas $cita): View
    {
        $cita->load(['paciente', 'medico', 'creadoPor']);

        return view('citas.show', compact('cita'));
    }

    /**
     * Mostrar el formulario para editar una cita.
     */
    public function edit(Citas $cita): View
    {
        $pacientes = Pacientes::query()
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        $medicos = Medicos::query()
            ->where(function ($query) use ($cita) {
                $query->where('status', true)
                    ->orWhere('id', $cita->medico_id);
            })
            ->orderBy('nombre')
            ->orderBy('apellido_paterno')
            ->get();

        return view('citas.edit', compact(
            'cita',
            'pacientes',
            'medicos'
        ));
    }


    /**
     * Actualizar una cita.
     */
    public function update(Request $request, Citas $cita): RedirectResponse
    {
        $datos = $request->validate([
            'paciente_id' => [
                'required',
                'integer',
                'exists:pacientes,id',
            ],
            'medico_id' => [
                'required',
                'integer',
                'exists:medicos,id',
            ],
            'fecha' => [
                'required',
                'date',
            ],
            'hora' => [
                'required',
                'date_format:H:i',
            ],
            'motivo' => [
                'required',
                'string',
                'max:255',
            ],
            'notas' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'estado' => [
                'required',
                'in:programada,confirmada,en_espera,en_consulta,finalizada,cancelada',
            ],
        ]);

        $medicoValido = Medicos::query()
            ->whereKey($datos['medico_id'])
            ->where('status', true)
            ->exists();

        if (! $medicoValido) {
            return back()
                ->withErrors([
                    'medico_id' => 'El médico seleccionado no está activo.',
                ])
                ->withInput();
        }

        $horarioOcupado = Citas::query()
            ->where('medico_id', $datos['medico_id'])
            ->whereDate('fecha', $datos['fecha'])
            ->whereTime('hora', $datos['hora'])
            ->whereKeyNot($cita->id)
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if (
            $horarioOcupado &&
            $datos['estado'] !== 'cancelada'
        ) {
            return back()
                ->withErrors([
                    'hora' => 'El médico ya tiene otra cita registrada en esa fecha y hora.',
                ])
                ->withInput();
        }

        $cita->update($datos);

        return redirect()
            ->route('citas.index')
            ->with('success', 'La cita se actualizó correctamente.');
    }

    /**
     * Eliminar una cita.
     */
    public function destroy(Citas $cita): RedirectResponse
    {
        $cita->delete();

        return redirect()
            ->route('citas.index')
            ->with('success', 'La cita se eliminó correctamente.');
    }

    public function buscarPacientes(Request $request): JsonResponse
{
    $termino = trim((string) $request->query('q', ''));

    if (mb_strlen($termino) < 2) {
        return response()->json([]);
    }

    $pacientes = Pacientes::query()
        ->where(function ($query) use ($termino) {
            $query
                ->where('nombre', 'like', '%' . $termino . '%')
                ->orWhere('apellido', 'like', '%' . $termino . '%')
                ->orWhereRaw(
                    "CONCAT_WS(' ', nombre, apellido) LIKE ?",
                    ['%' . $termino . '%']
                );
        })
        ->orderBy('nombre')
        ->orderBy('apellido')
        ->limit(10)
        ->get([
            'id',
            'nombre',
            'apellido',
            'telefono',
        ])
        ->map(function (Pacientes $paciente) {
            return [
                'id' => $paciente->id,
                'nombre_completo' => trim(
                    $paciente->nombre . ' ' . $paciente->apellido
                ),
                'telefono' => $paciente->telefono,
            ];
        })
        ->values();

    return response()->json($pacientes);
}
}
