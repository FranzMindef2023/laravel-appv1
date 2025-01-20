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
        Schema::create('assignments', function (Blueprint $table) {
            $table->increments('idassig');                // int8, primary     // varchar(250), unique
            $table->bigInteger('gestion');  
            $table->unsignedBigInteger('idpersona');
            $table->unsignedBigInteger('idorg');
            $table->unsignedBigInteger('idpuesto');
            $table->date('startdate'); 
            $table->date('enddate')->nullable(); 
            $table->boolean('status'); 
            $table->enum('estado', ['A', 'C','D'])->default('A'); // A=ACTUAL POSICION,C=CAMBIO DE ORGANIZACION,D=DESVINCULADO
            $table->string('motivo',150); 
            $table->timestamps();                        // created_at & updated_at timestamps
            // Foreign keys
            $table->foreign('idpersona')->references('idpersona')->on('personas')->onDelete('cascade');
            $table->foreign('idorg')->references('idorg')->on('organizacion')->onDelete('cascade');
            $table->foreign('idpuesto')->references('idpuesto')->on('puestos')->onDelete('cascade');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
