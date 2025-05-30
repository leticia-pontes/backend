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
        Schema::create('perfil_tecnologia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perfil_id');
            $table->unsignedBigInteger('tecnologia_id');

            $table->timestamps();

            $table->foreign('perfil_id')->references('id_perfil')->on('perfis')->onDelete('cascade');
            $table->foreign('tecnologia_id')->references('id_tecnologia')->on('tecnologias')->onDelete('cascade');

            $table->unique(['perfil_id', 'tecnologia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_tecnologia');
    }
};
