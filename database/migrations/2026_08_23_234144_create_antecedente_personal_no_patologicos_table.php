<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'antecedentes_personales_no_patologicos',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger(
                    'historia_clinica_id'
                );

                $table->unique(
                    'historia_clinica_id',
                    'ant_no_pat_historia_unique'
                );

                $table->foreign(
                    'historia_clinica_id',
                    'ant_no_pat_historia_foreign'
                )
                    ->references('id')
                    ->on('historias_clinicas')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();

                $table->json('antecedentes')
                    ->nullable();

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'antecedentes_personales_no_patologicos'
        );
    }
};