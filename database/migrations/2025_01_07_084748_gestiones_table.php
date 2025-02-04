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
        Schema::create('gestiones', function (Blueprint $table) {
            $table->unsignedBigInteger('idpersona');   // int8, foreign key
            $table->unsignedBigInteger('code');   // int8, foreign key
            $table->date('fechaingreso'); 
            $table->date('fechadesvin')->nullable(); 
            $table->bigInteger('gestion');
            $table->string('motivo',150); 
            $table->string('motivofin',150)->nullable();  

            // Foreign keys
            $table->foreign('idpersona')->references('idpersona')->on('personas')->onDelete('cascade');
            $table->foreign('code')->references('code')->on('reparticiones')->onDelete('cascade');

            $table->timestamps();  // created_at & updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestiones');
    }
};
