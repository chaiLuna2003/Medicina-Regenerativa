<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'pacientes',
            function (Blueprint $table) {
                /*
                |--------------------------------------------------------------------------
                | Identificación
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('sexo', 30)
                    ->nullable()
                    ->index();

                $table
                    ->string('categoria', 50)
                    ->default('sin_categoria')
                    ->index();

                /*
                |--------------------------------------------------------------------------
                | Ubicación
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('domicilio', 500)
                    ->nullable();

                $table
                    ->string('ciudad', 150)
                    ->nullable();

                $table
                    ->string('estado', 150)
                    ->nullable();

                $table
                    ->string('codigo_postal', 10)
                    ->nullable();

                $table
                    ->string('lugar_nacimiento', 200)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Contacto adicional
                |--------------------------------------------------------------------------
                |
                | El campo existente "telefono" continuará funcionando
                | como celular principal y número de WhatsApp.
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('telefono_fijo', 20)
                    ->nullable();

                $table
                    ->string('telefono_secundario', 20)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Información complementaria
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('ocupacion', 200)
                    ->nullable();

                $table
                    ->string('religion', 150)
                    ->nullable();

                $table
                    ->decimal(
                        'costo_consulta_personalizado',
                        10,
                        2
                    )
                    ->nullable();

                $table
                    ->boolean('finado')
                    ->default(false)
                    ->index();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'pacientes',
            function (Blueprint $table) {
                $table->dropIndex([
                    'sexo',
                ]);

                $table->dropIndex([
                    'categoria',
                ]);

                $table->dropIndex([
                    'finado',
                ]);

                $table->dropColumn([
                    'sexo',
                    'categoria',
                    'domicilio',
                    'ciudad',
                    'estado',
                    'codigo_postal',
                    'lugar_nacimiento',
                    'telefono_fijo',
                    'telefono_secundario',
                    'ocupacion',
                    'religion',
                    'costo_consulta_personalizado',
                    'finado',
                ]);
            }
        );
    }
};