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
        Schema::create('personas', function (Blueprint $table) {
            $table->increments('idpersona');                // int8, primary key
            $table->string('nombres', 50);               // varchar(50)
            $table->string('appaterno', 50)->nullable();             // varchar(50)
            $table->string('apmaterno', 50)->nullable();             // varchar(50)
            $table->bigInteger('ci');  
            $table->string('complemento')->nullable();                      // varchar, no length specified, but could be added
            $table->string('codper')->nullable();
            $table->string('carnetmil')->nullable();
            $table->string('carnetseg')->nullable();
            $table->string('email', 250)->unique();      // varchar(250), unique
            $table->bigInteger('celular');             // int8             
            $table->date('fechnacimeinto');
            $table->date('fechaegreso');  
            $table->string('gsanguineo',50);
            $table->string('tipoper',5);//CIVIL o MILITAR 
            $table->string('estserv',5);//ACTIVO o PASIVO  

            $table->unsignedBigInteger('idfuerza');
            $table->unsignedBigInteger('idespecialidad');
            $table->unsignedBigInteger('idgrado');
            $table->unsignedBigInteger('idsexo');
            $table->unsignedBigInteger('idarma');
            $table->unsignedBigInteger('idcv');

            $table->unsignedBigInteger('idsituacion');
            $table->unsignedBigInteger('idexpedicion');
            $table->boolean('status'); 
            $table->timestamps();                        // created_at & updated_at timestamps
            // Foreign keys
            $table->foreign('idfuerza')->references('idfuerza')->on('fuerzas')->onDelete('cascade');
            $table->foreign('idespecialidad')->references('idespecialidad')->on('especialidades')->onDelete('cascade');
            $table->foreign('idgrado')->references('idgrado')->on('grados')->onDelete('cascade');
            $table->foreign('idsexo')->references('idsexo')->on('sexos')->onDelete('cascade');
            $table->foreign('idarma')->references('idarma')->on('armas')->onDelete('cascade');
            $table->foreign('idcv')->references('idcv')->on('statuscvs')->onDelete('cascade');

            $table->foreign('idsituacion')->references('idsituacion')->on('situaciones')->onDelete('cascade');
            $table->foreign('idexpedicion')->references('idexpedicion')->on('expediciones')->onDelete('cascade');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
