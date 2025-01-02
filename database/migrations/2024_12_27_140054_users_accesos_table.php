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
        Schema::create('user_accesos', function (Blueprint $table) {
            $table->unsignedBigInteger('iduser');   // int8, foreign key
            $table->unsignedBigInteger('idorg');    // int8, foreign key

            // Foreign keys
            $table->foreign('iduser')->references('iduser')->on('users')->onDelete('cascade');
            $table->foreign('idorg')->references('idorg')->on('organizacion')->onDelete('cascade');

            $table->timestamps();  // created_at & updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accesos');
    }
};
