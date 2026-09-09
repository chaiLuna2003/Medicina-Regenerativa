<?php

namespace App\Http\Controllers;

use App\Models\CasoClinico;
use App\Services\CasoClinicoGraficaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CasoClinicoPdfController extends Controller
{
    /**
     * Genera el expediente PDF completo de un caso clínico.
     */
    public function descargar(
        CasoClinico $casoClinico,
        CasoClinicoGraficaService $graficaService
    ): Response {
        /*
         * Reutiliza la autorización existente:
         * - Administración puede consultar cualquier caso.
         * - El médico necesita una relación clínica válida
         *   con el paciente.
         * - Recepción y enfermería no pueden descargarlo.
         */
        Gate::authorize(
            'view',
            $casoClinico
        );

        /*
         * Cargar toda la información que formará
         * parte del expediente del caso clínico.
         */
        $casoClinico->load([
            'paciente.historiaClinica.antecedentesPersonalesPatologicos',

            'creadoPor',

            'cerradoPor',

            'evoluciones' => function ($query) {
                $query
                    ->with([
                        'medico.user',

                        'creadoPor',

                        'aparatos',

                        'cita.signoVital.registradoPor',

                        'cita.estudios',

                        'cita.exploracionFisica',
                    ])
                    ->orderBy('fecha')
                    ->orderBy('id');
            },
        ]);

        $graficas = $graficaService->generar(
            $casoClinico->evoluciones
        );

        $nombrePaciente = Str::slug(
            trim(
                ($casoClinico->paciente?->nombre ?? '')
                    .' '
                    .($casoClinico->paciente?->apellido ?? '')
            )
        );

        if ($nombrePaciente === '') {
            $nombrePaciente = 'paciente';
        }

        $nombreArchivo =
            'caso-clinico-'
            .$casoClinico->id
            .'-'
            .$nombrePaciente
            .'.pdf';

        return Pdf::loadView(
            'casos-clinicos.pdf',
            compact('casoClinico', 'graficas')
        )
            ->setPaper(
                'letter',
                'portrait'
            )
            ->download($nombreArchivo);
    }
}
