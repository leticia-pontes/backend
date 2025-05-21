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
        Schema::create('perfil', function (Blueprint $table) {
            $table->id('id_perfil');
            $table->string('foto')->nullable();
            $table->text('biografia')->nullable();
            $table->string('nicho_mercado')->nullable();
            $table->string('tecnologia')->nullable();
            $table->json('redes_sociais')->nullable();
            $table->unsignedBigInteger('id'); // FK

            // Relação com empresa
            $table->foreign('id')->references('id')->on('empresa')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil');
    }
};
