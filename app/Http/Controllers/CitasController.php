<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\Pacientes;
use App\Models\Medicos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class CitasController extends Controller
{

private const HORA_APERTURA = '09:00';
private const HORA_CIERRE = '21:00';
private const DURACION_CITA = 15;
    /**
     * Mostrar el listado de citas.
     */
    public function index(): View
    {
        $citas = Citas::query()
            ->with(['paciente', 'medico'])
            ->when(
                $this->medicoAutenticado(),
                fn ($query, Medicos $medico) => $query->where(
                    'medico_id',
                    $medico->id
                )
            )
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
        $medicoAutenticado = $this->medicoAutenticado();

        $medicos = Medicos::query()
            ->where('status', true)
            ->when(
                $medicoAutenticado,
                fn ($query, Medicos $medico) => $query->whereKey($medico->id)
            )
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
        $medicoAutenticado = $this->medicoAutenticado();

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
                ...($medicoAutenticado
                    ? [Rule::in([$medicoAutenticado->id])]
                    : []),
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

        $this->validarHorarioDisponible(
    $datos['medico_id'],
    $datos['fecha'],
    $datos['hora']
);

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
        $this->autorizarAccesoMedico($cita);

        $cita->load(['paciente', 'medico', 'creadoPor']);

        return view('citas.show', compact('cita'));
    }

    /**
     * Mostrar el formulario para editar una cita.
     */
    public function edit(Citas $cita): View
    {
        $this->autorizarAccesoMedico($cita);

        $medicoAutenticado = $this->medicoAutenticado();

        $pacientes = Pacientes::query()
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        $medicos = Medicos::query()
            ->where(function ($query) use ($cita) {
                $query->where('status', true)
                    ->orWhere('id', $cita->medico_id);
            })
            ->when(
                $medicoAutenticado,
                fn ($query, Medicos $medico) => $query->whereKey($medico->id)
            )
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
        $this->autorizarAccesoMedico($cita);

        $medicoAutenticado = $this->medicoAutenticado();

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
                ...($medicoAutenticado
                    ? [Rule::in([$medicoAutenticado->id])]
                    : []),
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

       $horarioFueModificado =
    (int) $cita->medico_id !== (int) $datos['medico_id']
    || $cita->fecha->format('Y-m-d') !== $datos['fecha']
    || Carbon::parse($cita->hora)->format('H:i') !== $datos['hora'];

$seEstaReactivando =
    $cita->estado === 'cancelada'
    && $datos['estado'] !== 'cancelada';

if (
    $datos['estado'] !== 'cancelada'
    && ($horarioFueModificado || $seEstaReactivando)
) {
    $this->validarHorarioDisponible(
        (int) $datos['medico_id'],
        $datos['fecha'],
        $datos['hora'],
        $cita->id
    );
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
        $this->autorizarAccesoMedico($cita);

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
public function horariosDisponibles(Request $request): JsonResponse
{
    $datos = $request->validate([
        'medico_id' => ['required', 'integer', 'exists:medicos,id'],
        'fecha' => ['required', 'date'],
        'ignorar_cita' => ['nullable', 'integer', 'exists:citas,id'],
    ]);

    $medicoAutenticado = $this->medicoAutenticado();

    if (
        $medicoAutenticado !== null
        && (int) $datos['medico_id'] !== (int) $medicoAutenticado->id
    ) {
        abort(403, 'No puedes consultar la agenda de otro médico.');
    }

    if (! empty($datos['ignorar_cita'])) {
        $citaIgnorada = Citas::query()->findOrFail($datos['ignorar_cita']);
        $this->autorizarAccesoMedico($citaIgnorada);
    }

    $fecha = Carbon::parse($datos['fecha'])->startOfDay();
    $ahora = now();

    $citasOcupadas = Citas::query()
        ->where('medico_id', $datos['medico_id'])
        ->whereDate('fecha', $fecha->format('Y-m-d'))
        ->where('estado', '!=', 'cancelada')
        ->when(
            ! empty($datos['ignorar_cita']),
            fn ($query) => $query->whereKeyNot($datos['ignorar_cita'])
        )
        ->pluck('hora')
        ->map(fn ($hora) => Carbon::parse($hora)->format('H:i'))
        ->all();

    $inicio = Carbon::parse(
        $fecha->format('Y-m-d') . ' ' . self::HORA_APERTURA
    );

    $cierre = Carbon::parse(
        $fecha->format('Y-m-d') . ' ' . self::HORA_CIERRE
    );

    $horarios = [];

    while ($inicio->copy()->addMinutes(self::DURACION_CITA)->lte($cierre)) {
        $hora = $inicio->format('H:i');
        $ocupado = in_array($hora, $citasOcupadas, true);
        $yaPaso = $fecha->isToday() && $inicio->lte($ahora);
        $fechaPasada = $fecha->isBefore($ahora->copy()->startOfDay());

        $horarios[] = [
            'hora' => $hora,
            'texto' => $inicio->format('h:i A'),
            'disponible' => ! $ocupado && ! $yaPaso && ! $fechaPasada,
            'ocupado' => $ocupado,
        ];

        $inicio->addMinutes(self::DURACION_CITA);
    }

    return response()->json([
        'horarios' => $horarios,
        'primer_disponible' => collect($horarios)
            ->firstWhere('disponible', true)['hora'] ?? null,
    ]);
}

private function validarHorarioDisponible(
    int $medicoId,
    string $fecha,
    string $hora,
    ?int $ignorarCitaId = null
): void {
    $inicio = Carbon::createFromFormat(
        'Y-m-d H:i',
        $fecha . ' ' . $hora
    );

    $apertura = Carbon::createFromFormat(
        'Y-m-d H:i',
        $fecha . ' ' . self::HORA_APERTURA
    );

    $cierre = Carbon::createFromFormat(
        'Y-m-d H:i',
        $fecha . ' ' . self::HORA_CIERRE
    );

    $minutosDesdeApertura = (int) $apertura->diffInMinutes($inicio, false);

    $esBloqueValido =
        $inicio->gte($apertura)
        && $inicio->copy()->addMinutes(self::DURACION_CITA)->lte($cierre)
        && $minutosDesdeApertura % self::DURACION_CITA === 0;

    if (! $esBloqueValido) {
        throw ValidationException::withMessages([
            'hora' => 'Selecciona un horario válido de 15 minutos entre las 09:00 AM y las 08:45 PM.',
        ]);
    }

    if ($inicio->lte(now())) {
        throw ValidationException::withMessages([
            'hora' => 'No puedes registrar una cita en un horario que ya pasó.',
        ]);
    }

    $horarioOcupado = Citas::query()
        ->where('medico_id', $medicoId)
        ->whereDate('fecha', $fecha)
        ->whereTime('hora', $hora)
        ->where('estado', '!=', 'cancelada')
        ->when(
            $ignorarCitaId,
            fn ($query) => $query->whereKeyNot($ignorarCitaId)
        )
        ->exists();

    if ($horarioOcupado) {
        throw ValidationException::withMessages([
            'hora' => 'Ese horario acaba de ser ocupado. Selecciona otro espacio disponible.',
        ]);
    }
}

private function medicoAutenticado(): ?Medicos
{
    $user = request()->user();

    if ($user === null || ! $user->isMedico()) {
        return null;
    }

    $medico = $user->medico;

    abort_if(
        $medico === null,
        403,
        'Tu usuario no tiene un perfil médico vinculado.'
    );

    return $medico;
}

private function autorizarAccesoMedico(Citas $cita): void
{
    $medico = $this->medicoAutenticado();

    if (
        $medico !== null
        && (int) $cita->medico_id !== (int) $medico->id
    ) {
        abort(403, 'No puedes acceder a una cita asignada a otro médico.');
    }
}

}
