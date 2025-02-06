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
        Schema::create('asignacion_vacaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('idpersona');
            $table->integer('gestion');
            $table->integer('anios_servicio')->notNull();
            $table->integer('dias_asignados')->notNull();
            $table->integer('dias_utilizados')->default(0);
            $table->timestamps();

            $table->foreign('idpersona')->references('idpersona')->on('personas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_vacaciones');
    }
};
