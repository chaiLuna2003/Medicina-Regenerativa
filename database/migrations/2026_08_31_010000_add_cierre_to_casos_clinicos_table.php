<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'casos_clinicos',
            function (Blueprint $table) {
                $table->dateTime(
                    'fecha_cierre'
                )->nullable();

                $table->foreignId(
                    'cerrado_por'
                )
                    ->nullable()
                    ->constrained('users')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                $table->text(
                    'motivo_cierre'
                )->nullable();

                $table->index(
                    [
                        'estado',
                        'fecha_cierre',
                    ],
                    'casos_estado_cierre_idx'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'casos_clinicos',
            function (Blueprint $table) {
                $table->dropIndex(
                    'casos_estado_cierre_idx'
                );

                $table->dropConstrainedForeignId(
                    'cerrado_por'
                );

                $table->dropColumn([
                    'fecha_cierre',
                    'motivo_cierre',
                ]);
            }
        );
    }
};