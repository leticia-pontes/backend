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
        Schema::create('projetos', function (Blueprint $table) {
            $table->increments('id_projeto'); // Chave primária
            $table->string('nome_projeto', 200)->notNullable();
            $table->text('descricao')->nullable(); // Descrição pode ser opcional
            $table->date('data_inicio')->nullable(); // Pode ser nulo se não houver data de início definida
            $table->date('data_fim')->nullable(); // Pode ser nulo para projetos em andamento
            $table->string('status', 50)->default('Em Andamento')->notNullable(); // Ex: 'Em Andamento', 'Concluído'
            $table->string('url_projeto', 255)->nullable(); // Link para o projeto em produção/repositório
            $table->string('imagem_destaque_url', 255)->nullable(); // URL de uma imagem representativa do projeto

            $table->unsignedInteger('id_empresa'); // Chave estrangeira para a empresa proprietária do projeto
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('cascade');

            // Adicionamos timestamps para rastrear criação e atualização do registro do projeto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
