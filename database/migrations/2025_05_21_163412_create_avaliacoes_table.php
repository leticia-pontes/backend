<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvaliacoesTable extends Migration
{
    public function up()
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->integer('nota');
            $table->text('comentario');
            $table->date('data_avaliacao');
            $table->foreignId('id_empresa')->constrained('empresas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('avaliacoes');
    }
}
