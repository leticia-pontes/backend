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
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id('id_avaliacao'); // Primary Key

            $table->unsignedBigInteger('id_empresa_avaliadora'); // Empresa que está avaliando
            $table->unsignedBigInteger('id_empresa_avaliada');  // Empresa que está sendo avaliada

            $table->integer('nota')->notNullable(); // Nota da avaliação (ex: 1 a 5)
            $table->text('comentario')->nullable(); // Comentário da avaliação (pode ser opcional)
            $table->dateTime('data_avaliacao')->useCurrent(); // Data e hora da avaliação

            // Foreign Keys
            $table->foreign('id_empresa_avaliadora')->references('id_empresa')->on('empresas')->onDelete('cascade');
            $table->foreign('id_empresa_avaliada')->references('id_empresa')->on('empresas')->onDelete('cascade');

            $table->unique(['id_empresa_avaliadora', 'id_empresa_avaliada']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
    }
};
