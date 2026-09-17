<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\ControlPeso;
use App\Models\Pacientes;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ControlPesoController extends Controller
{
    public function store(Request $request, Pacientes $pacientes): RedirectResponse
    {
        $datos = $this->validar($request, $pacientes);
        $cita = $pacientes->citas()->findOrFail($datos['cita_id']);
        $this->autorizarEdicion($request, $pacientes, $cita);

        $pacientes->controlesPeso()->create([
            ...$datos,
            'creado_por' => $request->user()->id,
        ]);

        return redirect()->route('pacientes.show', $pacientes)
            ->with('success', 'Tratamiento de precisión registrado correctamente.');
    }

    public function update(Request $request, Pacientes $pacientes, ControlPeso $controlPeso): RedirectResponse
    {
        $this->verificarPaciente($pacientes, $controlPeso);
        $this->autorizarEdicion($request, $pacientes, $controlPeso->cita);

        $datos = $this->validar($request, $pacientes);
        $cita = $pacientes->citas()->findOrFail($datos['cita_id']);
        $this->autorizarEdicion($request, $pacientes, $cita);

        $controlPeso->update([
            ...$datos,
            'actualizado_por' => $request->user()->id,
        ]);

        return redirect()->route('pacientes.show', $pacientes)
            ->with('success', 'Tratamiento de precisión actualizado correctamente.');
    }

    public function pdf(Request $request, Pacientes $pacientes, ControlPeso $controlPeso): Response
    {
        $this->verificarPaciente($pacientes, $controlPeso);
        Gate::authorize('view', $pacientes);

        $controlPeso->load('cita');

        $nombre = Str::slug(trim($pacientes->nombre.' '.$pacientes->apellido)) ?: 'paciente';

        return Pdf::loadView('pacientes.pdf.control-peso', [
            'control' => $controlPeso,
            'paciente' => $pacientes,
        ])->setPaper('letter', 'portrait')
            ->download("tratamiento-precision-{$controlPeso->id}-{$nombre}.pdf");
    }

    private function verificarPaciente(Pacientes $pacientes, ControlPeso $controlPeso): void
    {
        abort_unless($controlPeso->paciente_id === $pacientes->id, 404);
    }

    private function autorizarEdicion(Request $request, Pacientes $pacientes, Citas $cita): void
    {
        $usuario = $request->user();

        abort_unless(
            $usuario->isEnfermero()
                || ($usuario->isMedico()
                    && $usuario->medico !== null
                    && $cita->medico_id === $usuario->medico->id),
            403
        );

        Gate::authorize('view', $pacientes);
    }

    private function validar(Request $request, Pacientes $pacientes): array
    {
        $datos = $request->validate([
            'cita_id' => ['required', 'integer', Rule::exists('citas', 'id')
                ->where('paciente_id', $pacientes->id)],
            'diagnostico' => ['nullable', 'string', 'max:2000'],
            'peso' => ['required', 'numeric', 'gt:0', 'lte:999.99'],
            'talla' => ['required', 'numeric', 'gt:0', 'lte:300'],
            'imc' => ['required', 'numeric', 'gt:0', 'lte:200'],
            'porcentaje_grasa' => ['nullable', 'numeric', 'between:0,100'],
            'porcentaje_musculo' => ['nullable', 'numeric', 'between:0,100'],
            'porcentaje_agua' => ['nullable', 'numeric', 'between:0,100'],
            'porcentaje_hueso' => ['nullable', 'numeric', 'between:0,100'],
            'indice_antioxidante' => ['nullable', 'string', 'max:100'],
            'objetivos' => ['nullable', 'array', 'max:3'],
            'objetivos.*' => ['nullable', 'string', 'max:500'],
            'tratamiento_base' => ['nullable', 'string', 'max:5000'],
            'tratamiento_complementario' => ['nullable', 'string', 'max:5000'],
            'peptidos' => ['nullable', 'array', 'max:3'],
            'peptidos.*' => ['array:nombre,dosis,tiempo'],
            'peptidos.*.nombre' => ['nullable', 'string', 'max:200'],
            'peptidos.*.dosis' => ['nullable', 'string', 'max:200'],
            'peptidos.*.tiempo' => ['nullable', 'string', 'max:200'],
            'suplementos' => ['nullable', 'array', 'max:3'],
            'suplementos.*' => ['nullable', 'string', 'max:500'],
            'indicacion_dietetica' => ['nullable', 'string', 'max:5000'],
            'actividad_fisica' => ['nullable', 'string', 'max:5000'],
        ]);

        foreach (['objetivos', 'suplementos'] as $campo) {
            $datos[$campo] = array_values(array_filter(
                array_map(fn ($valor) => trim((string) $valor), $datos[$campo] ?? []),
                fn (string $valor) => $valor !== ''
            ));
        }

        $datos['peptidos'] = array_values(array_filter(
            array_map(
                fn (array $fila) => [
                    'nombre' => trim($fila['nombre'] ?? ''),
                    'dosis' => trim($fila['dosis'] ?? ''),
                    'tiempo' => trim($fila['tiempo'] ?? ''),
                ],
                $datos['peptidos'] ?? []
            ),
            fn (array $fila) => implode('', $fila) !== ''
        ));

        return $datos;
    }
}
