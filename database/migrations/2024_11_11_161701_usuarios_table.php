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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('iduser');                // int8, primary key
            $table->string('ci');   
            $table->string('grado', 50);                     // varchar, no length specified, but could be added
            $table->string('nombres', 50);               // varchar(50)
            $table->string('appaterno', 50);             // varchar(50)
            $table->string('apmaterno', 50);             // varchar(50)
            $table->string('email', 250)->unique();      // varchar(250), unique
            $table->bigInteger('celular');               // int8
            $table->string('usuario', 30);               // varchar(30)
            $table->string('password', 250);             // varchar(250)
            $table->boolean('status');                   // bool
            $table->string('token')->nullable();         // varchar, nullable
            $table->timestamp('last_login')->nullable(); // Timestamp for last login, nullable
            $table->unsignedBigInteger('idorg');
            $table->unsignedBigInteger('idpuesto');
            $table->timestamps();                        // created_at & updated_at timestamps
            // Foreign keys
            $table->foreign('idorg')->references('idorg')->on('organizacion')->onDelete('cascade');
            $table->foreign('idpuesto')->references('idpuesto')->on('puestos')->onDelete('cascade');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
