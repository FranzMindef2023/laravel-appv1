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
        Schema::create('organizacion', function (Blueprint $table) {
            $table->bigIncrements('idorg');  // Usamos bigIncrements para bigint como PK
            $table->string('nomorg');        // Nombre de la organización
            $table->string('sigla', 50);     // Sigla de la organización
            $table->bigInteger('idpadre')->nullable(); // idpadre como bigint, nullable si puede ser raíz
            $table->unsignedBigInteger('idubigeo');
            $table->boolean('status');   
            $table->timestamps();            // created_at y updated_at
            // Foreign keys
            $table->foreign('idubigeo')->references('idubigeo')->on('ubigeo')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizacion');
    }
};
