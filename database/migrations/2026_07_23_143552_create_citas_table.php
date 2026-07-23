<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('paciente_id')
                ->constrained('pacientes')
                ->restrictOnDelete();

            $table
                ->foreignId('medico_id')
                ->constrained('medicos')
                ->restrictOnDelete();

            $table->date('fecha');
            $table->time('hora');

            $table->string('motivo', 255);
            $table->text('notas')->nullable();

            $table->enum('estado', [
                'programada',
                'confirmada',
                'en_espera',
                'en_consulta',
                'finalizada',
                'cancelada',
            ])->default('programada');

            $table
                ->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};