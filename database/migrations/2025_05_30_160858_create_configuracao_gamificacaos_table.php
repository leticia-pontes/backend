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
        Schema::create('configuracao_gamificacao', function (Blueprint $table) {
            $table->id('id_configuracao_gamificacao'); // Chave primária
            $table->string('chave')->unique()->comment('Identificador único da configuração (ex: pontos_conclusao_pedido)');
            $table->text('descricao')->nullable()->comment('Descrição da configuração');
            $table->string('valor_tipo')->comment('Tipo do valor (int, float, string, boolean)');
            $table->string('valor')->comment('Valor da configuração'); // Armazena como string para flexibilidade
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracao_gamificacao');
    }
};
