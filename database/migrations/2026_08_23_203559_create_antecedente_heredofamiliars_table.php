<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'antecedentes_heredofamiliares',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('historia_clinica_id')
                    ->unique()
                    ->constrained('historias_clinicas')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();

                $table->unsignedSmallInteger(
                    'numero_hermanos'
                )->nullable();

        /*
 * Guardará cada antecedente como un campo de texto.
 *
 * Ejemplo:
 * {
 *   "diabetes": "negado",
 *   "cancer": "Padre, cáncer pulmonar"
 * }
 */
$table->json('antecedentes')
    ->nullable();

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'antecedentes_heredofamiliares'
        );
    }
};