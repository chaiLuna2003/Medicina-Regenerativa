<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CasoClinicoGraficaService
{
    public function generar(Collection $evoluciones): array
    {
        $registros = $evoluciones
            ->map(function ($evolucion) {
                $signos = $evolucion->cita?->signoVital;

                if (! $signos) {
                    return null;
                }

                return [
                    'fecha' => $evolucion->fecha?->format('d/m/Y')
                        ?? 'Sin fecha',
                    'peso' => $this->numero($signos->peso),
                    'imc' => $this->numero($signos->imc),
                    'presion_sistolica' => $this->numero(
                        $signos->presion_sistolica
                    ),
                    'presion_diastolica' => $this->numero(
                        $signos->presion_diastolica
                    ),
                    'frecuencia_cardiaca' => $this->numero(
                        $signos->frecuencia_cardiaca
                    ),
                    'frecuencia_respiratoria' => $this->numero(
                        $signos->frecuencia_respiratoria
                    ),
                    'temperatura' => $this->numero(
                        $signos->temperatura
                    ),
                    'saturacion_oxigeno' => $this->numero(
                        $signos->saturacion_oxigeno
                    ),
                    'glucosa' => $this->numero($signos->glucosa),
                ];
            })
            ->filter()
            ->values();

        $definiciones = [
            [
                'titulo' => 'Evolución del peso',
                'unidad' => 'kg',
                'series' => [
                    [
                        'campo' => 'peso',
                        'nombre' => 'Peso',
                        'color' => '#0D3B7F',
                    ],
                ],
            ],
            [
                'titulo' => 'Evolución del índice de masa corporal',
                'unidad' => 'kg/m²',
                'series' => [
                    [
                        'campo' => 'imc',
                        'nombre' => 'IMC',
                        'color' => '#0F766E',
                    ],
                ],
            ],
            [
                'titulo' => 'Evolución de la presión arterial',
                'unidad' => 'mmHg',
                'series' => [
                    [
                        'campo' => 'presion_sistolica',
                        'nombre' => 'Sistólica',
                        'color' => '#DC2626',
                    ],
                    [
                        'campo' => 'presion_diastolica',
                        'nombre' => 'Diastólica',
                        'color' => '#2563EB',
                    ],
                ],
            ],
            [
                'titulo' => 'Frecuencias cardiaca y respiratoria',
                'unidad' => 'por minuto',
                'series' => [
                    [
                        'campo' => 'frecuencia_cardiaca',
                        'nombre' => 'Cardiaca',
                        'color' => '#DC2626',
                    ],
                    [
                        'campo' => 'frecuencia_respiratoria',
                        'nombre' => 'Respiratoria',
                        'color' => '#7C3AED',
                    ],
                ],
            ],
            [
                'titulo' => 'Evolución de la temperatura',
                'unidad' => '°C',
                'series' => [
                    [
                        'campo' => 'temperatura',
                        'nombre' => 'Temperatura',
                        'color' => '#EA580C',
                    ],
                ],
            ],
            [
                'titulo' => 'Saturación de oxígeno',
                'unidad' => '%',
                'series' => [
                    [
                        'campo' => 'saturacion_oxigeno',
                        'nombre' => 'SpO₂',
                        'color' => '#0891B2',
                    ],
                ],
            ],
            [
                'titulo' => 'Evolución de la glucosa',
                'unidad' => 'mg/dL',
                'series' => [
                    [
                        'campo' => 'glucosa',
                        'nombre' => 'Glucosa',
                        'color' => '#9333EA',
                    ],
                ],
            ],
        ];

        return collect($definiciones)
            ->map(fn (array $definicion) => $this->crearGrafica(
                $registros,
                $definicion
            ))
            ->filter()
            ->values()
            ->all();
    }

    private function crearGrafica(
        Collection $registros,
        array $definicion
    ): ?array {
        $valores = collect($definicion['series'])
            ->flatMap(function (array $serie) use ($registros) {
                return $registros->pluck($serie['campo']);
            })
            ->filter(fn ($valor) => $valor !== null)
            ->map(fn ($valor) => (float) $valor)
            ->values();

        if ($valores->isEmpty()) {
            return null;
        }

        $ancho = 700;
        $alto = 250;
        $izquierda = 58;
        $derecha = 24;
        $superior = 48;
        $inferior = 48;
        $anchoGrafica = $ancho - $izquierda - $derecha;
        $altoGrafica = $alto - $superior - $inferior;

        $minimo = (float) $valores->min();
        $maximo = (float) $valores->max();

        if ($minimo === $maximo) {
            $margen = max(abs($minimo) * 0.1, 1);
        } else {
            $margen = ($maximo - $minimo) * 0.12;
        }

        $minimo -= $margen;
        $maximo += $margen;
        $rango = max($maximo - $minimo, 1);

        $cantidad = max($registros->count(), 1);
        $divisorX = max($cantidad - 1, 1);

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" '
            .'width="'.$ancho.'" height="'.$alto.'" '
            .'viewBox="0 0 '.$ancho.' '.$alto.'">';

        $svg .= '<rect width="100%" height="100%" '
            .'rx="10" fill="#FFFFFF"/>';

        $svg .= '<text x="'.$izquierda.'" y="25" '
            .'font-family="DejaVu Sans, sans-serif" '
            .'font-size="15" font-weight="bold" fill="#0F172A">'
            .$this->escapar($definicion['titulo'])
            .'</text>';

        for ($linea = 0; $linea <= 4; $linea++) {
            $y = $superior + ($altoGrafica / 4) * $linea;
            $valor = $maximo - ($rango / 4) * $linea;

            $svg .= '<line x1="'.$izquierda.'" y1="'.$y.'" '
                .'x2="'.($ancho - $derecha).'" y2="'.$y.'" '
                .'stroke="#E2E8F0" stroke-width="1"/>';

            $svg .= '<text x="'.($izquierda - 8).'" '
                .'y="'.($y + 4).'" text-anchor="end" '
                .'font-family="DejaVu Sans, sans-serif" '
                .'font-size="9" fill="#64748B">'
                .number_format($valor, 1)
                .'</text>';
        }

        foreach ($definicion['series'] as $indice => $serie) {
            $puntos = [];

            foreach ($registros as $posicion => $registro) {
                $valor = $registro[$serie['campo']];

                if ($valor === null) {
                    continue;
                }

                $x = $izquierda
                    + ($anchoGrafica / $divisorX) * $posicion;

                $y = $superior
                    + (($maximo - (float) $valor) / $rango)
                    * $altoGrafica;

                $puntos[] = $x.','.$y;
            }

            if (count($puntos) > 1) {
                $svg .= '<polyline points="'
                    .implode(' ', $puntos)
                    .'" fill="none" stroke="'.$serie['color'].'" '
                    .'stroke-width="2.5" '
                    .'stroke-linecap="round" '
                    .'stroke-linejoin="round"/>';
            }

            foreach ($registros as $posicion => $registro) {
                $valor = $registro[$serie['campo']];

                if ($valor === null) {
                    continue;
                }

                $x = $izquierda
                    + ($anchoGrafica / $divisorX) * $posicion;

                $y = $superior
                    + (($maximo - (float) $valor) / $rango)
                    * $altoGrafica;

                $svg .= '<circle cx="'.$x.'" cy="'.$y.'" r="3.5" '
                    .'fill="'.$serie['color'].'" stroke="#FFFFFF" '
                    .'stroke-width="1.5"/>';
            }

            $leyendaX = $izquierda + ($indice * 150);

            $svg .= '<line x1="'.$leyendaX.'" y1="39" '
                .'x2="'.($leyendaX + 18).'" y2="39" '
                .'stroke="'.$serie['color'].'" stroke-width="3"/>';

            $svg .= '<text x="'.($leyendaX + 24).'" y="42" '
                .'font-family="DejaVu Sans, sans-serif" '
                .'font-size="9" fill="#475569">'
                .$this->escapar($serie['nombre'])
                .'</text>';
        }

        foreach ($registros as $posicion => $registro) {
            $mostrarEtiqueta = $cantidad <= 6
                || $posicion === 0
                || $posicion === $cantidad - 1;

            if (! $mostrarEtiqueta) {
                continue;
            }

            $x = $izquierda
                + ($anchoGrafica / $divisorX) * $posicion;

            $svg .= '<text x="'.$x.'" '
                .'y="'.($alto - 25).'" text-anchor="middle" '
                .'font-family="DejaVu Sans, sans-serif" '
                .'font-size="8" fill="#64748B">'
                .$this->escapar($registro['fecha'])
                .'</text>';
        }

        $svg .= '<text x="'.($ancho - $derecha).'" y="24" '
            .'text-anchor="end" '
            .'font-family="DejaVu Sans, sans-serif" '
            .'font-size="9" fill="#64748B">'
            .$this->escapar($definicion['unidad'])
            .'</text>';

        $svg .= '</svg>';

        return [
            'titulo' => $definicion['titulo'],
            'imagen' => 'data:image/svg+xml;base64,'
                .base64_encode($svg),
        ];
    }

    private function numero(mixed $valor): ?float
    {
        if ($valor === null || $valor === '' || ! is_numeric($valor)) {
            return null;
        }

        return (float) $valor;
    }

    private function escapar(string $texto): string
    {
        return htmlspecialchars(
            $texto,
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        );
    }
}
