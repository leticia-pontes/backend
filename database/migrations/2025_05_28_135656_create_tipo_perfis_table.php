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
        Schema::create('tipo_perfis', function (Blueprint $table) {
            $table->id('id_tipo_perfil'); // Primary Key AUTO_INCREMENT
            $table->string('nome_tipo', 50)->unique()->notNullable(); // Nome único para o tipo de perfil
            // Não usaremos timestamps() aqui
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_perfis');
    }
};
