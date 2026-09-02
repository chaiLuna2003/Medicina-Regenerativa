<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\SignoVital;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SignosVitalesController extends Controller
{
    /**
     * Mostrar el historial de signos vitales registrados.
     */
    public function index(): View
    {
        $signosVitales = SignoVital::query()
            ->with([
                'paciente',
                'cita.medico',
                'enfermero',
            ])
            ->latest()
            ->paginate(15);

        return view(
            'signos-vitales.index',
            compact('signosVitales')
        );
    }

    /**
     * Mostrar el formulario para registrar los signos vitales de una cita.
     */
    public function create(Citas $cita): View|RedirectResponse
    {
        $cita->load([
            'paciente',
            'medico',
            'signoVital',
        ]);

        if ($cita->estado === 'cancelada') {
            abort(
                403,
                'No se pueden registrar signos vitales en una cita cancelada.'
            );
        }

        if ($cita->signoVital !== null) {
            return redirect()
                ->route('signos-vitales.show', $cita->signoVital)
                ->with(
                    'info',
                    'Esta cita ya tiene signos vitales registrados.'
                );
        }

        return view(
            'signos-vitales.create',
            compact('cita')
        );
    }

    /**
     * Guardar los signos vitales de una cita.
     */
    public function store(
        Request $request,
        Citas $cita
    ): RedirectResponse {
        if ($cita->estado === 'cancelada') {
            abort(
                403,
                'No se pueden registrar signos vitales en una cita cancelada.'
            );
        }

        $signoVitalExistente = $cita
            ->signoVital()
            ->first();

        if ($signoVitalExistente !== null) {
            if ($request->boolean('desde_dashboard_enfermeria')) {
                return redirect()
                    ->route('dashboard')
                    ->with(
                        'info',
                        'Esta cita ya tiene signos vitales registrados.'
                    );
            }

            return redirect()
                ->route(
                    'signos-vitales.show',
                    $signoVitalExistente
                )
                ->with(
                    'info',
                    'Esta cita ya tiene signos vitales registrados.'
                );
        }

        $datos = $request->validate([
            'peso' => [
                'required',
                'numeric',
                'min:0.5',
                'max:500',
            ],

            'estatura' => [
                'required',
                'numeric',
                'min:20',
                'max:250',
            ],

            'temperatura' => [
                'nullable',
                'numeric',
                'min:30',
                'max:45',
            ],

            'presion_sistolica' => [
                'nullable',
                'required_with:presion_diastolica',
                'integer',
                'min:40',
                'max:300',
            ],

            'presion_diastolica' => [
                'nullable',
                'required_with:presion_sistolica',
                'integer',
                'min:20',
                'max:200',
                'lt:presion_sistolica',
            ],

            'frecuencia_cardiaca' => [
                'nullable',
                'integer',
                'min:20',
                'max:300',
            ],

            'frecuencia_respiratoria' => [
                'nullable',
                'integer',
                'min:5',
                'max:100',
            ],

            'saturacion_oxigeno' => [
                'nullable',
                'integer',
                'min:50',
                'max:100',
            ],

            'glucosa' => [
                'nullable',
                'numeric',
                'min:20',
                'max:1000',
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'peso.required' => 'El peso es obligatorio.',
            'peso.numeric' => 'El peso debe ser un valor numérico.',

            'estatura.required' => 'La estatura es obligatoria.',
            'estatura.numeric' => 'La estatura debe ser un valor numérico.',

            'presion_sistolica.required_with' =>
            'Debes ingresar también la presión sistólica.',

            'presion_diastolica.required_with' =>
            'Debes ingresar también la presión diastólica.',

            'presion_diastolica.lt' =>
            'La presión diastólica debe ser menor que la presión sistólica.',

            'saturacion_oxigeno.max' =>
            'La saturación de oxígeno no puede ser mayor a 100%.',

            'observaciones.max' =>
            'Las observaciones no pueden superar los 2000 caracteres.',
        ]);

        $datos['paciente_id'] = $cita->paciente_id;
        $datos['cita_id'] = $cita->id;
        $datos['enfermero_id'] = auth()->id();

        $signoVital = SignoVital::create($datos);

        if ($request->boolean('desde_dashboard_enfermeria')) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'success',
                    'Los signos vitales se registraron correctamente.'
                );
        }

        return redirect()
            ->route(
                'signos-vitales.show',
                $signoVital
            )
            ->with(
                'success',
                'Los signos vitales se registraron correctamente.'
            );
    }

    /**
     * Mostrar el detalle de una valoración.
     */
    public function show(SignoVital $signoVital): View
    {
        $signoVital->load([
            'paciente',
            'cita.medico',
            'enfermero',
        ]);

        return view(
            'signos-vitales.show',
            compact('signoVital')
        );
    }
}
