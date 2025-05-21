<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanosTable extends Migration
{
    public function up()
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_plano', 50);
            $table->decimal('valor', 10, 2);
            $table->text('descricao');
            $table->date('data_criacao');
            $table->foreignId('id_empresa')->constrained('empresas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('planos');
    }
}
