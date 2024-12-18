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
        Schema::create('novedades', function (Blueprint $table) {
            $table->increments('idnovedad');             // Identificador único
            $table->unsignedBigInteger('idassig');     // ID del personal (relación con la tabla personas)
            $table->unsignedBigInteger('idnov');         // ID de la organización
            $table->text('descripcion')->nullable();     // Descripción adicional (opcional)
            $table->date('startdate');                   // Fecha de inicio de la novedad
            $table->date('enddate')->nullable();         // Fecha de fin (si aplica)
            $table->boolean('activo')->default(true);    // Indicador de si la novedad está activa
            $table->timestamps();                        // Timestamps: created_at y updated_at
        
            // Claves foráneas
            $table->foreign('idassig')->references('idassig')->on('assignments')->onDelete('cascade');
            $table->foreign('idnov')->references('idnov')->on('tiponovedad')->onDelete('cascade');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('novedades');
    }
};
