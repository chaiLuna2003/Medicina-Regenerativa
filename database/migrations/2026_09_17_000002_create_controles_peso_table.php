<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controles_peso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->restrictOnDelete();
            $table->foreignId('cita_id')->constrained('citas')->restrictOnDelete();
            $table->foreignId('creado_por')->constrained('users')->restrictOnDelete();
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('diagnostico')->nullable();
            $table->decimal('peso', 6, 2);
            $table->decimal('talla', 5, 2);
            $table->decimal('imc', 5, 2);
            $table->decimal('porcentaje_grasa', 5, 2)->nullable();
            $table->decimal('porcentaje_musculo', 5, 2)->nullable();
            $table->decimal('porcentaje_agua', 5, 2)->nullable();
            $table->decimal('porcentaje_hueso', 5, 2)->nullable();
            $table->string('indice_antioxidante', 100)->nullable();
            $table->json('objetivos')->nullable();
            $table->text('tratamiento_base')->nullable();
            $table->text('tratamiento_complementario')->nullable();
            $table->json('peptidos')->nullable();
            $table->json('suplementos')->nullable();
            $table->text('indicacion_dietetica')->nullable();
            $table->text('actividad_fisica')->nullable();
            $table->timestamps();
            $table->index(['paciente_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controles_peso');
    }
};
