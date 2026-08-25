<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'exploraciones_fisicas',
            function (Blueprint $table) {
                $table
                    ->json('sistemas')
                    ->nullable()
                    ->after('exploracion_fisica');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'exploraciones_fisicas',
            function (Blueprint $table) {
                $table->dropColumn('sistemas');
            }
        );
    }
};