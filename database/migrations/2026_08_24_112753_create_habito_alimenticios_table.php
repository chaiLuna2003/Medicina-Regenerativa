<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'habitos_alimenticios',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger(
                    'historia_clinica_id'
                )->unique();

                $table->json('comidas')->nullable();
                $table->json('alimentos')->nullable();

                $table->timestamps();

                $table->foreign(
                    'historia_clinica_id',
                    'habitos_historia_fk'
                )
                    ->references('id')
                    ->on('historias_clinicas')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('habitos_alimenticios');
    }
};