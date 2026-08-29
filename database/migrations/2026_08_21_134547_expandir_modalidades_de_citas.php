<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
    Schema::table('citas', function (Blueprint $table) {
        $table
            ->string('modalidad')
            ->default('presencial')
            ->change();
    });

    return;
}

        DB::statement("
            ALTER TABLE citas
            MODIFY modalidad ENUM(
                'presencial',
                'telefonica',
                'videoconsulta',
                'fuera_instalaciones'
            )
            NOT NULL DEFAULT 'presencial'
        ");
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        /*
         * Convertimos las nuevas modalidades antes
         * de reducir nuevamente las opciones.
         */
        DB::table('citas')
            ->whereIn('modalidad', [
                'telefonica',
                'fuera_instalaciones',
            ])
            ->update([
                'modalidad' => 'presencial',
            ]);

        DB::statement("
            ALTER TABLE citas
            MODIFY modalidad ENUM(
                'presencial',
                'videoconsulta'
            )
            NOT NULL DEFAULT 'presencial'
        ");
    }
};