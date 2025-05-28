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
        Schema::create('empresa_nicho', function (Blueprint $table) {
            $table->unsignedInteger('id_empresa');
            $table->unsignedInteger('id_nicho');

            // Definindo a chave primária composta
            $table->primary(['id_empresa', 'id_nicho']);

            // Foreign Keys
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('cascade');
            $table->foreign('id_nicho')->references('id_nicho')->on('nichos')->onDelete('cascade');

            // Esta tabela não precisa de created_at/updated_at
            // $table->timestamps(); // Removido
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_nicho');
    }
};
