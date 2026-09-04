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
        ];
    }
}
