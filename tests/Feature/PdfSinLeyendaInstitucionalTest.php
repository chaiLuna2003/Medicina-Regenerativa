<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PdfSinLeyendaInstitucionalTest extends TestCase
{
    #[DataProvider('vistasPdf')]
    public function test_las_vistas_pdf_no_incluyen_la_leyenda_institucional(
        string $vista
    ): void {
        $contenido = file_get_contents(
            resource_path($vista)
        );

        $this->assertIsString($contenido);
        $this->assertStringNotContainsString(
            'Medicina Regenerativa',
            $contenido
        );
    }

    #[DataProvider('camposPrivadosPdf')]
    public function test_los_pdf_clinicos_no_exponen_datos_privados(
        string $vista,
        array $camposProhibidos
    ): void {
        $contenido = file_get_contents(
            resource_path($vista)
        );

        $this->assertIsString($contenido);

        foreach ($camposProhibidos as $campo) {
            $this->assertStringNotContainsString(
                $campo,
                $contenido,
                "El PDF todavía contiene el campo privado: {$campo}"
            );
        }
    }

    public static function camposPrivadosPdf(): array
    {
        return [
            'historia clinica' => [
                'views/pacientes/pdf/historia-clinica.blade.php',
                [
                    '$paciente->domicilio',
                    '$paciente->ciudad',
                    '$paciente->codigo_postal',
                    'Domicilio',
                ],
            ],
            'caso clinico' => [
                'views/casos-clinicos/pdf.blade.php',
                [
                    '$paciente?->telefono',
                    '$paciente?->domicilio',
                    '$paciente?->ciudad',
                    '$paciente?->codigo_postal',
                    'Teléfono principal',
                    'Dirección',
                ],
            ],
        ];
    }

    public static function vistasPdf(): array
    {
        return [
            'hoja diaria' => [
                'views/hoja-diaria/pdf.blade.php',
            ],
            'receta medica' => [
                'views/recetas/pdf.blade.php',
            ],
            'historia clinica' => [
                'views/pacientes/pdf/historia-clinica.blade.php',
            ],
            'caso clinico' => [
                'views/casos-clinicos/pdf.blade.php',
            ],
        ];
    }
}
