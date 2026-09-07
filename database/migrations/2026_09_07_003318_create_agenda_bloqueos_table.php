<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_bloqueos', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('medico_id')
                ->constrained('medicos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table
                ->foreignId('creado_por')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table
                ->string('motivo', 500);

            /*
             * Al liberar un bloqueo lo conservaremos
             * como registro histórico.
             */
            $table->softDeletes();
            $table->timestamps();

            /*
             * Optimiza la consulta diaria de bloqueos
             * pertenecientes a cada médico.
             */
            $table->index([
                'medico_id',
                'fecha',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_bloqueos');
    }
};
