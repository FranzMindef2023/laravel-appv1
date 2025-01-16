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
        Schema::create('horas', function (Blueprint $table) {
            // New columns for start and end time
            $table->time('horainicial'); // Column for initial hour
            $table->time('horafinal');   // Column for final hour
            $table->boolean('status'); // Estado activo/inactivo
            $table->timestamps();  // created_at & updated_at timestamps
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horas');
    }
};
