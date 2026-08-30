<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'evolucion_aparatos',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId(
                    'evolucion_clinica_id'
                )
                    ->constrained(
                        'evoluciones_clinicas'
                    )
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                $table->string(
                    'aparato',
                    60
                );

                $table->string(
                    'estado',
                    30
                )->default('no_evaluado');

                $table->text(
                    'observaciones'
                )->nullable();

                $table->timestamps();

                /*
                 * Un aparato solamente puede aparecer una vez
                 * dentro de la misma evolución.
                 */
                $table->unique(
                    [
                        'evolucion_clinica_id',
                        'aparato',
                    ],
                    'evolucion_aparato_uq'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'evolucion_aparatos'
        );
    }
};