<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signos_vitales', function (Blueprint $table) {
            /*
             * Elimina la restricción actual que utiliza nullOnDelete().
             */
            $table->dropForeign(['cita_id']);
        });

        Schema::table('signos_vitales', function (Blueprint $table) {
            /*
             * Impide eliminar una cita cuando tiene signos vitales.
             */
            $table->foreign('cita_id')
                ->references('id')
                ->on('citas')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('signos_vitales', function (Blueprint $table) {
            $table->dropForeign(['cita_id']);
        });

        Schema::table('signos_vitales', function (Blueprint $table) {
            /*
             * Restaura el comportamiento anterior.
             */
            $table->foreign('cita_id')
                ->references('id')
                ->on('citas')
                ->nullOnDelete();
        });
    }
};