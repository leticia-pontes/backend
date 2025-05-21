<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo', function (Blueprint $table) {
            $table->id('id_catalogo');
            $table->string('arquivo');
            $table->string('nome_arquivo');
            $table->text('descricao')->nullable();
            $table->date('data_criacao');
            $table->string('versao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->unsignedBigInteger('id');

            // Foreign key corrigida
            $table->foreign('id')->references('id')->on('empresa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('catalogo', function (Blueprint $table) {
            $table->dropForeign(['id']);
        });

        Schema::dropIfExists('catalogo');
    }
};
