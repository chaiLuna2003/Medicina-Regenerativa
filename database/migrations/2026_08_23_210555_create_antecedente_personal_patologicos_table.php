<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'antecedentes_personales_patologicos',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('historia_clinica_id')
                    ->unique()
                    ->constrained('historias_clinicas')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();

                /*
                 * Guardará cada antecedente como texto.
                 *
                 * Ejemplo:
                 * {
                 *   "diabetes": "negado",
                 *   "alergias": "Penicilina",
                 *   "adicciones": "Alcohol ocasional"
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
            'antecedentes_personales_patologicos'
        );
    }
};