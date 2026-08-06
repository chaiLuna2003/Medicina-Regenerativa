<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        Schema::create('signos_vitales', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relaciones
            |--------------------------------------------------------------------------
            */

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnDelete();

            $table->foreignId('cita_id')
                ->nullable()
                ->constrained('citas')
                ->nullOnDelete();

            $table->foreignId('enfermero_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Signos vitales
            |--------------------------------------------------------------------------
            */

            $table->decimal('peso', 6, 2);

            /*
             * La estatura se guardará en centímetros.
             * Ejemplo: 175.50
             */
            $table->decimal('estatura', 5, 2);

            $table->decimal('temperatura', 4, 1)->nullable();

            $table->unsignedSmallInteger('presion_sistolica')->nullable();

            $table->unsignedSmallInteger('presion_diastolica')->nullable();

            $table->unsignedSmallInteger('frecuencia_cardiaca')->nullable();

            $table->unsignedSmallInteger('frecuencia_respiratoria')->nullable();

            $table->unsignedTinyInteger('saturacion_oxigeno')->nullable();

            $table->decimal('glucosa', 6, 2)->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();

            /*
             * Una cita solo puede tener un registro de signos vitales.
             */
            $table->unique('cita_id');
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('signos_vitales');
    }
};