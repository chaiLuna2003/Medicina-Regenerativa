<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('casos_clinicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nombre', 150);
            $table->longText('descripcion_inicial')->nullable();
            $table->date('fecha_inicio');
            $table->string('estado', 20)->default('activo');

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(
                ['paciente_id', 'estado'],
                'casos_paciente_estado_idx'
            );

            $table->index(
                ['paciente_id', 'fecha_inicio'],
                'casos_paciente_fecha_idx'
            );

            $table->unique(
                ['id', 'paciente_id'],
                'casos_id_paciente_uq'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casos_clinicos');
    }
};