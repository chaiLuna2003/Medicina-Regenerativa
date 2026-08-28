<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\Medicos;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class HojaDiariaController extends Controller
{
    /**
     * Muestra el formulario para generar la hoja diaria.
     */
    public function index(Request $request): View
    {
        $usuario = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Segunda capa de autorización
        |--------------------------------------------------------------------------
        */

        abort_unless(
            in_array(
                $usuario->role,
                [
                    'admin',
                    'recepcionista',
                    'medico',
                    'enfermero',
                ],
                true
            ),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Fecha inicial
        |--------------------------------------------------------------------------
        */

        $fechaSeleccionada = $request->filled('fecha')
            ? Carbon::parse($request->input('fecha'))
            : today();

        /*
        |--------------------------------------------------------------------------
        | Médico autenticado
        |--------------------------------------------------------------------------
        */

        $medicoAutenticado = null;

        if ($usuario->role === 'medico') {
            $medicoAutenticado = $usuario->medico;

            abort_unless(
                $medicoAutenticado,
                403,
                'Tu usuario no tiene un perfil médico asociado.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Catálogo de médicos
        |--------------------------------------------------------------------------
        |
        | El médico no necesita elegir un perfil porque el sistema
        | utilizará automáticamente el suyo.
        |
        */

        $medicos = $medicoAutenticado
            ? collect()
            : Medicos::query()
            ->with('user')
            ->where('status', true)
            ->orderBy('nombre')
            ->orderBy('apellido_paterno')
            ->get();

        return view(
            'hoja-diaria.index',
            compact(
                'fechaSeleccionada',
                'medicos',
                'medicoAutenticado'
            )
        );
    }

    /**
     * Genera la hoja diaria en formato PDF.
     */
    public function pdf(Request $request): Response
    {
        $usuario = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Segunda capa de autorización
        |--------------------------------------------------------------------------
        */

        abort_unless(
            in_array(
                $usuario->role,
                [
                    'admin',
                    'recepcionista',
                    'medico',
                    'enfermero',
                ],
                true
            ),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Validación
        |--------------------------------------------------------------------------
        */

        $datos = $request->validate([
            'fecha' => [
                'required',
                'date_format:Y-m-d',
            ],

            'medico_id' => [
                'nullable',
                'integer',
                'exists:medicos,id',
            ],
        ]);

        $fecha = Carbon::createFromFormat(
            'Y-m-d',
            $datos['fecha']
        );

        /*
        |--------------------------------------------------------------------------
        | Consulta base
        |--------------------------------------------------------------------------
        */

        $consulta = Citas::query()
            ->with([
                'paciente',
                'medico.user',
            ])
            ->whereDate(
                'fecha',
                $fecha->toDateString()
            );

        /*
        |--------------------------------------------------------------------------
        | Restricción para médicos
        |--------------------------------------------------------------------------
        |
        | Un médico nunca puede solicitar la hoja diaria
        | correspondiente a otro médico.
        |
        */

        $medicoSeleccionado = null;

        if ($usuario->role === 'medico') {
            $medico = $usuario->medico;

            abort_unless(
                $medico,
                403,
                'Tu usuario no tiene un perfil médico asociado.'
            );

            $consulta->where(
                'medico_id',
                $medico->id
            );

            $medicoSeleccionado = $medico;
        } elseif (!empty($datos['medico_id'])) {
            $medicoSeleccionado = Medicos::findOrFail(
                $datos['medico_id']
            );

            $consulta->where(
                'medico_id',
                $medicoSeleccionado->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener citas
        |--------------------------------------------------------------------------
        */

        $citas = $consulta
            ->orderBy('hora')
            ->orderBy('id')
            ->get();

        $totalCitas = $citas->count();

        $totalCitasActivas = $citas
            ->where('estado', '!=', 'cancelada')
            ->count();

        $totalCitasCanceladas = $citas
            ->where('estado', 'cancelada')
            ->count();

        $totalPacientes = $citas
            ->where('estado', '!=', 'cancelada')
            ->pluck('paciente_id')
            ->filter()
            ->unique()
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Nombre del archivo
        |--------------------------------------------------------------------------
        */

        $nombreArchivo =
            'hoja-diaria-'
            . $fecha->format('Y-m-d');

        if ($medicoSeleccionado) {
            $nombreArchivo .=
                '-medico-'
                . $medicoSeleccionado->id;
        }

        $nombreArchivo .= '.pdf';

        /*
        |--------------------------------------------------------------------------
        | Generar PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'hoja-diaria.pdf',
            compact(
                'citas',
                'fecha',
                'totalCitas',
                'totalCitasActivas',
                'totalCitasCanceladas',
                'totalPacientes',
                'medicoSeleccionado'
            )
        )->setPaper(
            'letter',
            'landscape'
        );

        return $pdf->download(
            $nombreArchivo
        );
    }
}
