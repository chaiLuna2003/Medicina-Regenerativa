<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'antecedentes_ginecoobstetricos',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('historia_clinica_id')
                    ->unique();

                /*
                |--------------------------------------------------------------------------
                | Historia menstrual y sexual
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedTinyInteger('edad_menarca')
                    ->nullable();

                $table
                    ->string('ritmo_menstrual', 100)
                    ->nullable();

                $table
                    ->unsignedTinyInteger(
                        'duracion_menstruacion_dias'
                    )
                    ->nullable();

                $table
                    ->date('fecha_ultima_menstruacion')
                    ->nullable();

                $table
                    ->unsignedTinyInteger(
                        'edad_inicio_vida_sexual'
                    )
                    ->nullable();

                $table
                    ->unsignedSmallInteger(
                        'numero_parejas_sexuales'
                    )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Historia obstétrica
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedSmallInteger('gestas')
                    ->nullable();

                $table
                    ->unsignedSmallInteger('partos')
                    ->nullable();

                $table
                    ->unsignedSmallInteger('cesareas')
                    ->nullable();

                $table
                    ->unsignedSmallInteger('abortos')
                    ->nullable();

                $table
                    ->unsignedSmallInteger(
                        'embarazos_ectopicos'
                    )
                    ->nullable();

                $table
                    ->unsignedSmallInteger('hijos_vivos')
                    ->nullable();

                $table
                    ->boolean('embarazo_actual')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Anticoncepción y menopausia
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('metodo_anticonceptivo', 255)
                    ->nullable();

                $table
                    ->boolean('menopausia')
                    ->nullable();

                $table
                    ->unsignedTinyInteger('edad_menopausia')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Prevención y antecedentes
                |--------------------------------------------------------------------------
                */

                $table
                    ->date('fecha_ultimo_papanicolaou')
                    ->nullable();

                $table
                    ->text('resultado_papanicolaou')
                    ->nullable();

                $table
                    ->date('fecha_ultima_mastografia')
                    ->nullable();

                $table
                    ->text('resultado_mastografia')
                    ->nullable();

                $table
                    ->text('infecciones_transmision_sexual')
                    ->nullable();

                $table
                    ->text('observaciones')
                    ->nullable();

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Llave foránea con nombre corto para MySQL
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreign(
                        'historia_clinica_id',
                        'gineco_historia_fk'
                    )
                    ->references('id')
                    ->on('historias_clinicas')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'antecedentes_ginecoobstetricos'
        );
    }
};