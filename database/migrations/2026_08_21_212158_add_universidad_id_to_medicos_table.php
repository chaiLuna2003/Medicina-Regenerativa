<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('medicos', function (Blueprint $table) {
        $table->foreignId('universidad_id')
            ->nullable()
            ->after('cedula')
            ->constrained('universidades')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('medicos', function (Blueprint $table) {
        $table->dropConstrainedForeignId(
            'universidad_id'
        );
    });
}
};
