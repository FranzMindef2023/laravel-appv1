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
        Schema::create('reglas_vacaciones', function (Blueprint $table) {
            $table->id('id_regla');
            $table->integer('anios_servicio_min')->notNull();
            $table->integer('anios_servicio_max')->notNull();
            $table->integer('dias_vacaciones')->notNull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglas_vacaciones');
    }
};
