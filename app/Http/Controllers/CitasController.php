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
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Log;
use Throwable;


class CitasController extends Controller
{

    private const HORA_APERTURA = '09:00';
    private const HORA_CIERRE = '21:00';
    private const DURACION_CITA = 15;
    /**
     * Mostrar el listado de citas.
     */
    public function index(Request $request): View
{
    $medicoAutenticado =
        $this->medicoAutenticado();

    $citas = Citas::query()
        ->with([
            'paciente',
            'medico.user',
        ])

        /*
         * Filtrar por nombre o apellido del paciente.
         */
        ->when(
            $request->filled('buscar'),
            function ($query) use ($request) {
                $buscar = trim(
                    $request->string('buscar')->toString()
                );

                $query->whereHas(
                    'paciente',
                    function ($pacienteQuery) use ($buscar) {
                        $pacienteQuery->where(
                            function ($nombreQuery) use ($buscar) {
                                $nombreQuery
                                    ->where(
                                        'nombre',
                                        'like',
                                        "%{$buscar}%"
                                    )
                                    ->orWhere(
                                        'apellido',
                                        'like',
                                        "%{$buscar}%"
                                    );
                            }
                        );
                    }
                );
            }
        )

        /*
         * El médico solo puede consultar sus propias citas.
         */
        ->when(
            $medicoAutenticado,
            fn ($query, Medicos $medico) =>
                $query->where(
                    'medico_id',
                    $medico->id
                )
        )

        /*
         * Administración y recepción pueden
         * filtrar por médico.
         */
        ->when(
            !$medicoAutenticado
                && $request->filled('medico_id'),
            fn ($query) =>
                $query->where(
                    'medico_id',
                    $request->integer('medico_id')
                )
        )

        /*
         * Filtrar por modalidad.
         */
        ->when(
            in_array(
                $request->input('modalidad'),
                [
                    'presencial',
                    'videoconsulta',
                ],
                true
            ),
            fn ($query) =>
                $query->where(
                    'modalidad',
                    $request->input('modalidad')
                )
        )

        /*
         * Mostrar primero las citas creadas
         * más recientemente, sin importar la
         * fecha para la cual fueron programadas.
         */
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->paginate(15)
        ->withQueryString();

    /*
     * Lista de médicos para el filtro.
     * El médico autenticado no necesita este selector.
     */
    $medicos = $medicoAutenticado
        ? collect()
        : Medicos::query()
            ->with('user')
            ->get()
            ->sortBy(
                fn (Medicos $medico) =>
                    $medico->user?->name
            )
            ->values();

    return view(
        'citas.index',
        compact(
            'citas',
            'medicos'
        )
    );
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
                fn($query, Medicos $medico) => $query->whereKey($medico->id)
            )
            ->orderBy('nombre')
            ->orderBy('apellido_paterno')
            ->get();

        return view('citas.create', compact('medicos'));
    }

    /**
     * Guardar una nueva cita.
     */
    public function store(
        Request $request,
        GoogleCalendarService $googleCalendar
    ): RedirectResponse {
        $medicoAutenticado =
            $this->medicoAutenticado();

        $datos = $request->validate([
            'paciente_id' => [
                'required',
                'integer',
                Rule::exists(
                    'pacientes',
                    'id'
                ),
            ],

            'medico_id' => [
                'required',
                'integer',
                'exists:medicos,id',

                ...(
                    $medicoAutenticado
                    ? [
                        Rule::in([
                            $medicoAutenticado->id,
                        ]),
                    ]
                    : []
                ),
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

            'modalidad' => [
                'required',
                Rule::in([
                    'presencial',
                    'videoconsulta',
                ]),
            ],

            'motivo' => [
                'required',
                Rule::in([
                    'consulta_inicial',
                    'consulta_subsecuente',
                    'consulta_emergencia',
                ]),
            ],

            'notas' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'estado' => [
                'required',
                'in:programada,confirmada,'
                    . 'en_espera,en_consulta,'
                    . 'finalizada,cancelada',
            ],
        ]);

        $medicoValido = Medicos::query()
            ->whereKey(
                $datos['medico_id']
            )
            ->where(
                'status',
                true
            )
            ->exists();

        if (!$medicoValido) {
            return back()
                ->withErrors([
                    'medico_id' =>
                    'El usuario seleccionado '
                        . 'no es un médico activo.',
                ])
                ->withInput();
        }

        /*
     * La modalidad no modifica la comprobación
     * de horarios. Una cita presencial y una
     * videoconsulta ocupan igualmente al médico.
     */
        $this->validarHorarioDisponible(
            (int) $datos['medico_id'],
            $datos['fecha'],
            $datos['hora']
        );

        $datos['created_by'] =
            auth()->id();

        $datos['estado_videoconferencia'] =
            $datos['modalidad']
            === 'videoconsulta'
            ? 'pendiente'
            : 'no_aplica';

        /*
     * La cita se guarda primero para no perderla
     * si Google presenta un error temporal.
     */
        $cita = Citas::create(
            $datos
        );

        if (
            $cita->modalidad
            === 'videoconsulta'
        ) {
            try {
                $meet =
                    $googleCalendar
                    ->crearVideoconsulta(
                        $cita
                    );

                $cita->update([
                    'google_event_id' =>
                    $meet['event_id'],

                    'google_meet_url' =>
                    $meet['meet_url'],

                    'google_calendar_url' =>
                    $meet['calendar_url'],

                    'estado_videoconferencia' =>
                    $meet['meet_url']
                        ? 'disponible'
                        : 'pendiente',

                    'meet_generado_at' =>
                    $meet['meet_url']
                        ? now()
                        : null,
                ]);
            } catch (Throwable $exception) {
                $cita->update([
                    'estado_videoconferencia' =>
                    'fallido',
                ]);

                Log::error(
                    'No se pudo generar Google Meet.',
                    [
                        'cita_id' => $cita->id,
                        'error' =>
                        $exception->getMessage(),
                    ]
                );

                return redirect()
                    ->route(
                        'citas.show',
                        $cita
                    )
                    ->with(
                        'error',
                        'La cita se guardó, pero '
                            . 'no se pudo generar el enlace '
                            . 'de Google Meet.'
                    );
            }
        }

        $mensaje =
            $cita->modalidad
            === 'videoconsulta'
            ? (
                $cita->google_meet_url
                ? 'La videoconsulta y su enlace '
                . 'de Google Meet se crearon '
                . 'correctamente.'
                : 'La videoconsulta se creó y '
                . 'Google está generando '
                . 'el enlace de Meet.'
            )
            : 'La cita se registró correctamente.';

        return redirect()
            ->route(
                'citas.show',
                $cita
            )
            ->with(
                'success',
                $mensaje
            );
    }

    /**
     * Recupera o vuelve a generar el enlace
     * de una videoconsulta.
     */
    public function generarMeet(
        Citas $cita,
        GoogleCalendarService $googleCalendar
    ): RedirectResponse {
        abort_unless(
            in_array(
                auth()->user()->role,
                [
                    'admin',
                    'recepcionista',
                ],
                true
            ),
            403
        );

        abort_unless(
            $cita->modalidad
                === 'videoconsulta',
            404
        );

        try {
            if ($cita->google_event_id) {
                $meet =
                    $googleCalendar
                    ->consultarVideoconsulta(
                        $cita
                    );
            } else {
                $creado =
                    $googleCalendar
                    ->crearVideoconsulta(
                        $cita
                    );

                $cita->google_event_id =
                    $creado['event_id'];

                $meet = [
                    'meet_url' =>
                    $creado['meet_url'],

                    'calendar_url' =>
                    $creado['calendar_url'],
                ];
            }

            $cita->fill([
                'google_meet_url' =>
                $meet['meet_url'],

                'google_calendar_url' =>
                $meet['calendar_url'],

                'estado_videoconferencia' =>
                $meet['meet_url']
                    ? 'disponible'
                    : 'pendiente',

                'meet_generado_at' =>
                $meet['meet_url']
                    ? now()
                    : null,
            ])->save();

            if (!$meet['meet_url']) {
                return back()->with(
                    'error',
                    'Google todavía está generando '
                        . 'el enlace. Intenta nuevamente '
                        . 'en unos segundos.'
                );
            }

            return back()->with(
                'success',
                'El enlace de Google Meet '
                    . 'está disponible.'
            );
        } catch (Throwable $exception) {
            $cita->update([
                'estado_videoconferencia' =>
                'fallido',
            ]);

            Log::error(
                'No se pudo recuperar Google Meet.',
                [
                    'cita_id' => $cita->id,
                    'error' =>
                    $exception->getMessage(),
                ]
            );

            return back()->with(
                'error',
                'No fue posible generar el enlace. '
                    . 'Verifica la conexión de Google.'
            );
        }
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
                fn($query, Medicos $medico) => $query->whereKey($medico->id)
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
    public function update(
        Request $request,
        Citas $cita,
        GoogleCalendarService $googleCalendar
    ): RedirectResponse {
        $this->autorizarAccesoMedico(
            $cita
        );

        $medicoAutenticado =
            $this->medicoAutenticado();

        /*
     * Conservamos los valores anteriores para
     * decidir qué operación realizar en Google.
     */
        $modalidadAnterior =
            $cita->modalidad;

        $estadoAnterior =
            $cita->estado;

        /*
     * Las citas nuevas y clasificadas solamente
     * pueden utilizar estos tres motivos.
     */
        $motivosPermitidos = [
            'consulta_inicial',
            'consulta_subsecuente',
            'consulta_emergencia',
        ];

        /*
     * Si la cita contiene un motivo histórico,
     * permitimos conservar exactamente ese valor.
     */
        if (
            filled($cita->motivo)
            && !in_array(
                $cita->motivo,
                $motivosPermitidos,
                true
            )
        ) {
            $motivosPermitidos[] =
                $cita->motivo;
        }

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

                ...(
                    $medicoAutenticado
                    ? [
                        Rule::in([
                            $medicoAutenticado->id,
                        ]),
                    ]
                    : []
                ),
            ],

            'fecha' => [
                'required',
                'date',
            ],

            'hora' => [
                'required',
                'date_format:H:i',
            ],

            'modalidad' => [
                'required',
                Rule::in([
                    'presencial',
                    'videoconsulta',
                ]),
            ],

            'motivo' => [
                'required',
                'string',
                'max:255',
                Rule::in(
                    $motivosPermitidos
                ),
            ],

            'notas' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'estado' => [
                'required',
                'in:programada,confirmada,'
                    . 'en_espera,en_consulta,'
                    . 'finalizada,cancelada',
            ],
        ]);

        $medicoValido = Medicos::query()
            ->whereKey(
                $datos['medico_id']
            )
            ->where(
                'status',
                true
            )
            ->exists();

        if (!$medicoValido) {
            return back()
                ->withErrors([
                    'medico_id' =>
                    'El médico seleccionado '
                        . 'no está activo.',
                ])
                ->withInput();
        }

        /*
     * No permitimos alterar responsables cuando
     * ya existe información clínica.
     */
        $cambiaResponsablesClinicos =
            (int) $cita->paciente_id
            !== (int) $datos['paciente_id']
            || (int) $cita->medico_id
            !== (int) $datos['medico_id'];

        $tieneInformacionClinica =
            $cita->receta()->exists()
            || $cita->signoVital()->exists()
            || $cita->estudios()->exists();

        if (
            $cambiaResponsablesClinicos
            && $tieneInformacionClinica
        ) {
            return back()
                ->withErrors([
                    'cita' =>
                    'No puedes cambiar el paciente '
                        . 'ni el médico de una cita que '
                        . 'ya contiene información clínica.',
                ])
                ->withInput();
        }

        /*
     * Comprobamos nuevamente el horario solamente
     * cuando se modifica o reactiva la cita.
     */
        $horarioFueModificado =
            (int) $cita->medico_id
            !== (int) $datos['medico_id']
            || $cita->fecha->format('Y-m-d')
            !== $datos['fecha']
            || Carbon::parse(
                $cita->hora
            )->format('H:i')
            !== $datos['hora'];

        $seEstaReactivando =
            $cita->estado === 'cancelada'
            && $datos['estado'] !== 'cancelada';

        if (
            $datos['estado'] !== 'cancelada'
            && (
                $horarioFueModificado
                || $seEstaReactivando
            )
        ) {
            $this->validarHorarioDisponible(
                (int) $datos['medico_id'],
                $datos['fecha'],
                $datos['hora'],
                $cita->id
            );
        }

        /*
     * Colocamos los valores nuevos en memoria.
     * Todavía no se guardan en MySQL.
     */
        $cita->fill(
            $datos
        );

        /*
     * Obliga a Eloquent a recargar paciente
     * y médico si alguno fue modificado.
     */
        $cita->unsetRelation(
            'paciente'
        );

        $cita->unsetRelation(
            'medico'
        );

        try {
            /*
         * Cambió de videoconsulta a presencial:
         * eliminamos el evento de Google.
         */
            if (
                $modalidadAnterior === 'videoconsulta'
                && $datos['modalidad'] === 'presencial'
            ) {
                $googleCalendar
                    ->cancelarVideoconsulta(
                        $cita
                    );

                $cita->fill([
                    'google_event_id' => null,
                    'google_meet_url' => null,
                    'google_calendar_url' => null,
                    'estado_videoconferencia' =>
                    'no_aplica',
                    'meet_generado_at' => null,
                ]);
            }

            /*
         * La cita seguirá siendo o se convertirá
         * en videoconsulta.
         */
            if (
                $datos['modalidad']
                === 'videoconsulta'
            ) {
                /*
             * Si se cancela, también eliminamos
             * el evento de Google Calendar.
             */
                if (
                    $datos['estado']
                    === 'cancelada'
                ) {
                    $googleCalendar
                        ->cancelarVideoconsulta(
                            $cita
                        );

                    $cita->fill([
                        'google_event_id' => null,
                        'google_meet_url' => null,
                        'google_calendar_url' => null,
                        'estado_videoconferencia' =>
                        'cancelado',
                        'meet_generado_at' => null,
                    ]);
                } else {
                    /*
                 * Crea un evento nuevo cuando:
                 * - antes era presencial;
                 * - estaba cancelada;
                 * - o perdió su evento de Google.
                 */
                    $necesitaEventoNuevo =
                        $modalidadAnterior
                        !== 'videoconsulta'
                        || $estadoAnterior
                        === 'cancelada'
                        || blank(
                            $cita->google_event_id
                        );

                    if ($necesitaEventoNuevo) {
                        $meet =
                            $googleCalendar
                            ->crearVideoconsulta(
                                $cita
                            );

                        $cita->fill([
                            'google_event_id' =>
                            $meet['event_id'],

                            'google_meet_url' =>
                            $meet['meet_url'],

                            'google_calendar_url' =>
                            $meet['calendar_url'],

                            'estado_videoconferencia' =>
                            $meet['meet_url']
                                ? 'disponible'
                                : 'pendiente',

                            'meet_generado_at' =>
                            $meet['meet_url']
                                ? now()
                                : null,
                        ]);
                    } else {
                        /*
                     * Conserva el mismo enlace y
                     * actualiza fecha, hora y asistentes.
                     */
                        $googleCalendar
                            ->actualizarVideoconsulta(
                                $cita
                            );

                        $cita->estado_videoconferencia =
                            $cita->google_meet_url
                            ? 'disponible'
                            : 'pendiente';
                    }
                }
            }

            /*
         * MySQL se actualiza después de que Google
         * confirma la operación.
         */
            $cita->save();
        } catch (Throwable $exception) {
            Log::error(
                'No se pudo sincronizar '
                    . 'la videoconsulta.',
                [
                    'cita_id' => $cita->id,
                    'error' =>
                    $exception->getMessage(),
                ]
            );

            return back()
                ->withErrors([
                    'videoconsulta' =>
                    'No fue posible sincronizar '
                        . 'el cambio con Google Calendar. '
                        . 'La cita no fue modificada.',
                ])
                ->withInput();
        }

        return redirect()
            ->route(
                'citas.show',
                $cita
            )
            ->with(
                'success',
                'La cita y Google Calendar '
                    . 'se actualizaron correctamente.'
            );
    }

    /**
     * Eliminar una cita que todavía no contiene información clínica.
     */
    public function destroy(Citas $cita): RedirectResponse
    {
        $this->autorizarAccesoMedico($cita);

        /*
     * Las citas con recetas o signos vitales forman parte
     * del historial clínico y no pueden eliminarse.
     */
        $tieneInformacionClinica =
            $cita->receta()->exists()
            || $cita->signoVital()->exists()
            || $cita->estudios()->exists();

        if ($tieneInformacionClinica) {
            return back()->with(
                'error',
                'Esta cita no puede eliminarse porque ya contiene información clínica.'
            );
        }

        $cita->delete();

        return redirect()
            ->route('citas.index')
            ->with(
                'success',
                'La cita se eliminó correctamente.'
            );
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
                fn($query) => $query->whereKeyNot($datos['ignorar_cita'])
            )
            ->pluck('hora')
            ->map(fn($hora) => Carbon::parse($hora)->format('H:i'))
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
                fn($query) => $query->whereKeyNot($ignorarCitaId)
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
