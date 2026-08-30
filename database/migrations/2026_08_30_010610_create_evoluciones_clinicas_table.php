<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Índices requeridos para comprobar que el paciente,
         * médico y fecha coincidan exactamente con la cita.
         */
        Schema::table('citas', function (Blueprint $table) {
            $table->unique(
                ['id', 'paciente_id'],
                'citas_id_paciente_uq'
            );

            $table->unique(
                ['id', 'medico_id'],
                'citas_id_medico_uq'
            );

            $table->unique(
                ['id', 'fecha'],
                'citas_id_fecha_uq'
            );
        });

        Schema::create(
            'evoluciones_clinicas',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger(
                    'caso_clinico_id'
                );

                $table->unsignedBigInteger(
                    'cita_id'
                )->unique();

                $table->unsignedBigInteger(
                    'paciente_id'
                );

                $table->unsignedBigInteger(
                    'medico_id'
                );

                $table->date('fecha');

                $table->longText(
                    'evolucion_clinica'
                );

                $table->longText(
                    'diagnostico'
                )->nullable();

                $table->longText(
                    'tratamiento'
                )->nullable();

                $table->longText(
                    'plan_recomendaciones'
                )->nullable();

                $table->longText(
                    'indicaciones_enfermeria'
                )->nullable();

                $table->longText(
                    'observaciones'
                )->nullable();

                $table->foreignId('created_by')
                    ->constrained('users')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                $table->timestamps();

                /*
                 * El caso debe pertenecer al mismo paciente.
                 */
                $table->foreign(
                    [
                        'caso_clinico_id',
                        'paciente_id',
                    ],
                    'evolucion_caso_paciente_fk'
                )
                    ->references([
                        'id',
                        'paciente_id',
                    ])
                    ->on('casos_clinicos')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                /*
                 * El paciente debe coincidir con la cita.
                 */
                $table->foreign(
                    [
                        'cita_id',
                        'paciente_id',
                    ],
                    'evolucion_cita_paciente_fk'
                )
                    ->references([
                        'id',
                        'paciente_id',
                    ])
                    ->on('citas')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                /*
                 * El médico debe coincidir con la cita.
                 */
                $table->foreign(
                    [
                        'cita_id',
                        'medico_id',
                    ],
                    'evolucion_cita_medico_fk'
                )
                    ->references([
                        'id',
                        'medico_id',
                    ])
                    ->on('citas')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                /*
                 * La fecha debe coincidir con la cita.
                 */
                $table->foreign(
                    [
                        'cita_id',
                        'fecha',
                    ],
                    'evolucion_cita_fecha_fk'
                )
                    ->references([
                        'id',
                        'fecha',
                    ])
                    ->on('citas')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                $table->index(
                    [
                        'caso_clinico_id',
                        'fecha',
                    ],
                    'evolucion_caso_fecha_idx'
                );

                $table->index(
                    [
                        'paciente_id',
                        'fecha',
                    ],
                    'evolucion_paciente_fecha_idx'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'evoluciones_clinicas'
        );

        Schema::table('citas', function (Blueprint $table) {
            $table->dropUnique(
                'citas_id_paciente_uq'
            );

            $table->dropUnique(
                'citas_id_medico_uq'
            );

            $table->dropUnique(
                'citas_id_fecha_uq'
            );
        });
    }
};