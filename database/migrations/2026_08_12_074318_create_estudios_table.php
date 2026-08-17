<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudios', function (Blueprint $table) {
            $table->id();

            /*
             * Cita a la que pertenece el estudio.
             *
             * No permitimos eliminar una cita si tiene
             * estudios clínicos asociados.
             */
            $table->foreignId('cita_id')
                ->constrained('citas')
                ->restrictOnDelete();

            /*
             * Usuario que realizó la carga.
             *
             * Aunque inicialmente será Recepción/Admin,
             * guardamos el user_id para auditoría.
             */
            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Información clínica/descriptiva.
             */
            $table->string('nombre', 150);

            $table->text('descripcion')
                ->nullable();

            $table->date('fecha_estudio');

            /*
             * Información del PDF.
             *
             * archivo_path contiene la ubicación privada.
             * archivo_original conserva el nombre
             * que tenía el documento al subirlo.
             */
            $table->string('archivo_path');

            $table->string('archivo_original');

            $table->string('mime_type', 100)
                ->default('application/pdf');

            $table->unsignedBigInteger('archivo_size')
                ->nullable();

            $table->timestamps();

            /*
             * Ayudará al consultar estudios por cita
             * y ordenarlos posteriormente por fecha.
             */
            $table->index([
                'cita_id',
                'fecha_estudio',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudios');
    }
};