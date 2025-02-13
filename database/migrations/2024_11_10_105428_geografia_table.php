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
        Schema::create('ubigeo', function (Blueprint $table) {
            $table->bigIncrements('idubigeo');
            $table->bigInteger('id_padre')->nullable();
            $table->integer('ubigeo');
            $table->string('codigoubigeo', 20);
            $table->string('descubigeo', 100);
            $table->string('nivel', 15);
            $table->string('siglaubigeo', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubigeo');
    }
};
