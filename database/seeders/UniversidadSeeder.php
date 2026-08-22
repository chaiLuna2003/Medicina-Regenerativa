<?php

namespace Database\Seeders;

use App\Models\Universidad;
use Illuminate\Database\Seeder;

class UniversidadSeeder extends Seeder
{
    public function run(): void
    {
        $universidades = [
            [
                'nombre' =>
                    'Universidad Nacional Autónoma de México',
                'abreviatura' => 'UNAM',
            ],
            [
                'nombre' =>
                    'Instituto Politécnico Nacional',
                'abreviatura' => 'IPN',
            ],
            [
                'nombre' =>
                    'Universidad Autónoma de Nuevo León',
                'abreviatura' => 'UANL',
            ],
            [
                'nombre' =>
                    'Universidad de Guadalajara',
                'abreviatura' => 'UDG',
            ],
            [
                'nombre' =>
                    'Tecnológico de Monterrey',
                'abreviatura' => 'ITESM',
            ],
            [
                'nombre' =>
                    'Universidad Autónoma Metropolitana',
                'abreviatura' => 'UAM',
            ],
            [
                'nombre' =>
                    'Universidad Autónoma de San Luis Potosí',
                'abreviatura' => 'UASLP',
            ],
            [
                'nombre' =>
                    'Benemérita Universidad Autónoma de Puebla',
                'abreviatura' => 'BUAP',
            ],
            [
                'nombre' =>
                    'Universidad Veracruzana',
                'abreviatura' => 'UV',
            ],
            [
                'nombre' =>
                    'Universidad Autónoma de Yucatán',
                'abreviatura' => 'UADY',
            ],
            [
                'nombre' =>
                    'Otra universidad',
                'abreviatura' => 'OTRA',
            ],
        ];

        foreach ($universidades as $universidad) {
            Universidad::updateOrCreate(
                [
                    'nombre' => $universidad['nombre'],
                ],
                [
                    'abreviatura' =>
                        $universidad['abreviatura'],

                    'logo_path' => null,
                    'status' => true,
                ]
            );
        }
    }
}