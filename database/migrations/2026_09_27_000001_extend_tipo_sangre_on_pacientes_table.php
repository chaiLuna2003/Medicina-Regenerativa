<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('tipo_sangre', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('pacientes')->whereRaw('LENGTH(tipo_sangre) > 5')->exists()) {
            throw new RuntimeException('No se puede reducir tipo_sangre: existen valores de más de 5 caracteres.');
        }

        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('tipo_sangre', 5)->nullable()->change();
        });
    }
};
