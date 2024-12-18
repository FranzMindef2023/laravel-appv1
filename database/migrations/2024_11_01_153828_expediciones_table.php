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
        Schema::create('expediciones', function (Blueprint $table) {
            $table->increments('idexpedicion');  
            $table->string('Departamento', 50); 
            $table->boolean('status');         // varchar(50)
            $table->timestamps();  // created_at & updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expediciones');
    }
};
