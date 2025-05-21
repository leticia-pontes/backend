<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerfisTable extends Migration
{
    public function up()
    {
        Schema::create('perfis', function (Blueprint $table) {
            $table->id();
            $table->string('foto')->nullable();
            $table->text('biografia')->nullable();
            $table->string('nicho_mercado', 50)->nullable();
            $table->string('tecnologia', 50)->nullable();
            $table->json('redes_sociais')->nullable();
            $table->foreignId('id_empresa')->constrained('empresas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('perfis');
    }
}
