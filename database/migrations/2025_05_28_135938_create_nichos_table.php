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
        Schema::create('nichos', function (Blueprint $table) {
            $table->id('id_nicho'); // Primary Key AUTO_INCREMENT
            $table->string('nome_nicho', 50)->unique()->notNullable(); // Nome único para o nicho
            // Não usaremos timestamps() aqui
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nichos');
    }
};
