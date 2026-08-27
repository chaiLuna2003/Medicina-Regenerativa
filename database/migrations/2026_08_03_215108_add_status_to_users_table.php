<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Esta migración se conserva por compatibilidad con bases existentes.
     *
     * La columna users.status ya es creada por:
     * 2026_08_03_205017_add_status_to_users_table.php
     */
    public function up(): void
    {
        // Sin cambios: la migración anterior crea users.status.
    }

    public function down(): void
    {
        // Sin cambios: esta migración no creó ninguna columna.
    }
};
