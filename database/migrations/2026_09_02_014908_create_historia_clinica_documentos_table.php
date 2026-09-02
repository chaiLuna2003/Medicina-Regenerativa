<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'historia_clinica_documentos',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('paciente_id')
                    ->constrained('pacientes')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table
                    ->foreignId('generado_por')
                    ->constrained('users')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->string(
                    'archivo_path',
                    500
                );

                $table->string(
                    'archivo_nombre',
                    255
                );

                $table
                    ->string('mime_type', 100)
                    ->default('application/pdf');

                $table
                    ->unsignedBigInteger('archivo_size');

                $table->timestamp(
                    'generado_en'
                );

                $table->timestamps();

                $table->index([
                    'paciente_id',
                    'generado_en',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'historia_clinica_documentos'
        );
    }
};