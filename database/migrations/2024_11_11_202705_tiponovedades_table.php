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
        Schema::create('tiponovedad', function (Blueprint $table) {
            $table->increments('idnov');        // Identificador único
            $table->string('novedad', 100); // Tipo de novedad (Permiso, Vacaciones, etc.)
            $table->boolean('status');    
            $table->timestamps();               // created_at & updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiponovedad');
    }
};
