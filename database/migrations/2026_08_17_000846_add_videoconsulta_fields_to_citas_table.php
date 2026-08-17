<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table
                ->enum('modalidad', [
                    'presencial',
                    'videoconsulta',
                ])
                ->default('presencial')
                ->after('hora');

            $table
                ->string('google_event_id')
                ->nullable()
                ->unique()
                ->after('modalidad');

            $table
                ->string('google_meet_url')
                ->nullable()
                ->after('google_event_id');

            $table
                ->string('google_calendar_url')
                ->nullable()
                ->after('google_meet_url');

            $table
                ->enum('estado_videoconferencia', [
                    'no_aplica',
                    'pendiente',
                    'disponible',
                    'fallido',
                    'cancelado',
                ])
                ->default('no_aplica')
                ->after('google_calendar_url');

            $table
                ->timestamp('meet_generado_at')
                ->nullable()
                ->after('estado_videoconferencia');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropUnique([
                'google_event_id',
            ]);

            $table->dropColumn([
                'modalidad',
                'google_event_id',
                'google_meet_url',
                'google_calendar_url',
                'estado_videoconferencia',
                'meet_generado_at',
            ]);
        });
    }
};