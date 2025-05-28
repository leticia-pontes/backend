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
            $table->increments('id_avaliacao'); // Primary Key AUTO_INCREMENT
            $table->unsignedInteger('id_empresa_avaliador'); // Empresa que está avaliando
            $table->unsignedInteger('id_empresa_avaliado');  // Empresa que está sendo avaliada
            $table->integer('nota')->notNullable(); // Nota da avaliação (ex: 1 a 5)
            $table->text('comentario')->nullable(); // Comentário da avaliação (pode ser opcional)
            $table->dateTime('data_avaliacao')->useCurrent(); // Data e hora da avaliação

            // Chave primária composta opcional para evitar que a mesma empresa avalie outra duas vezes
            // $table->unique(['id_empresa_avaliador', 'id_empresa_avaliado']);

            // Foreign Keys
            $table->foreign('id_empresa_avaliador')->references('id_empresa')->on('empresas')->onDelete('cascade');
            $table->foreign('id_empresa_avaliado')->references('id_empresa')->on('empresas')->onDelete('cascade');

            $table->timestamps(); // created_at e updated_at
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
