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
        Schema::create('partesdiarias', function (Blueprint $table) {
            $table->unsignedBigInteger('idpersona'); // Relación con la tabla assignments
            $table->string('gestion', 4); // Gestión (por ejemplo, "2024")
            $table->tinyInteger('mes'); // Mes (número del 1 al 12)
            $table->string('forma_noforma', 50); // Forma o NoForma
            $table->unsignedBigInteger('idnov')->nullable(); // ID de la novedad (opcional)
            $table->timestamp('fechahora'); // Fecha y hora del parte
            $table->date('fechaparte'); // Fecha del parte
            $table->enum('estado', ['recibido', 'pendiente'])->default('pendiente'); // Estado enviado/pendiente
            $table->unsignedBigInteger('iduser'); // ID del usuario que realiza el parte
            $table->string('codigo', 20); // Código opcional
            $table->timestamps(); // created_at y updated_at

            // Relación con la tabla assignments
            $table->foreign('idpersona')->references('idpersona')->on('personas')->onDelete('cascade');
            // Relación con la tabla usuarios
            $table->foreign('iduser')->references('iduser')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partesdiarias');
    }
};
