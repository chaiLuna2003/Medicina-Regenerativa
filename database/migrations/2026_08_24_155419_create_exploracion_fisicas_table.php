<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'exploraciones_fisicas',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger(
                    'historia_clinica_id'
                );

                $table->unsignedBigInteger(
                    'cita_id'
                )->unique();

                $table->unsignedBigInteger(
                    'medico_id'
                );

                $table->longText(
                    'interrogatorio'
                )->nullable();

                $table->longText(
                    'anotaciones'
                )->nullable();

                $table->longText(
                    'exploracion_fisica'
                )->nullable();

                $table->longText(
                    'recomendaciones'
                )->nullable();

                $table->timestamps();

                $table->foreign(
                    'historia_clinica_id',
                    'exploraciones_historia_fk'
                )
                    ->references('id')
                    ->on('historias_clinicas')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();

                $table->foreign(
                    'cita_id',
                    'exploraciones_cita_fk'
                )
                    ->references('id')
                    ->on('citas')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                $table->foreign(
                    'medico_id',
                    'exploraciones_medico_fk'
                )
                    ->references('id')
                    ->on('medicos')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                $table->index(
                    'historia_clinica_id',
                    'exploraciones_historia_idx'
                );

                $table->index(
                    'medico_id',
                    'exploraciones_medico_idx'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('exploraciones_fisicas');
    }
};