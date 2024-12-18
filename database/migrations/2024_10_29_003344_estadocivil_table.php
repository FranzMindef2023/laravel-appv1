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
        Schema::create('statuscv', function (Blueprint $table) {
            $table->bigIncrements('idcv'); // Usar 'idcv' como clave primaria
            $table->string('name', 50)->unique(); // Nombre del estado civil
            $table->boolean('status'); // Estado activo/inactivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuscv');
    }
};
