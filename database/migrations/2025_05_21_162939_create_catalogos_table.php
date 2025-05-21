<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogosTable extends Migration
{
    public function up()
    {
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();
            $table->binary('arquivo');
            $table->string('nome_arquivo', 100);
            $table->text('descricao');
            $table->date('data_criacao');
            $table->string('versao', 20);
            $table->boolean('ativo');
            $table->foreignId('id_empresa')->constrained('empresas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalogos');
    }
}
