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
        Schema::create('empresa_tecnologia', function (Blueprint $table) {
            $table->unsignedInteger('id_empresa');
            $table->unsignedInteger('id_tecnologia');

            // Definindo a chave primária composta
            $table->primary(['id_empresa', 'id_tecnologia']);

            // Foreign Keys
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('cascade');
            $table->foreign('id_tecnologia')->references('id_tecnologia')->on('tecnologias')->onDelete('cascade');

            // Esta tabela não precisa de created_at/updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_tecnologia');
    }
};
