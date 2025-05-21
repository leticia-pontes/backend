<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cnpj')->unique();
            $table->string('perfil')->nullable();
            $table->integer('seguidores')->default(0);
            $table->string('email')->unique();
            $table->string('senha');
            $table->string('telefone')->nullable();
            $table->string('endereco')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('empresas');
    }
}
