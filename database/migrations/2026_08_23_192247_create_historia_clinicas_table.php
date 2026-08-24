<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')
                ->unique()
                ->constrained('pacientes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->text('patologia_base')
                ->nullable();

            $table->text('padecimiento_actual')
                ->nullable();

            $table->text('tratamientos_actuales')
                ->nullable();

            $table->text('prioridad_analisis_medico')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};