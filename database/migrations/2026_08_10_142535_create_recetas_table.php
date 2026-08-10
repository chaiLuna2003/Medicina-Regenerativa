<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cita_id')
                ->unique()
                ->constrained('citas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->longText('contenido');

            $table->timestamp('fecha_expedicion');

            $table->timestamps();

            $table->index('fecha_expedicion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};