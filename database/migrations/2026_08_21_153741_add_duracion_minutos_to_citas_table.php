<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table
                ->unsignedSmallInteger('duracion_minutos')
                ->default(15)
                ->after('hora');
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn('duracion_minutos');
        });
    }
};