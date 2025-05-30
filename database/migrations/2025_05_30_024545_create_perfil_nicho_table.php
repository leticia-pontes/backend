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
        Schema::create('perfil_nicho', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perfil_id');
            $table->unsignedBigInteger('nicho_id');

            $table->timestamps();

            $table->foreign('perfil_id')->references('id_perfil')->on('perfis')->onDelete('cascade');
            $table->foreign('nicho_id')->references('id_nicho')->on('nichos')->onDelete('cascade');

            $table->unique(['perfil_id', 'nicho_id']); // Impede duplicatas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_nicho');
    }
};
