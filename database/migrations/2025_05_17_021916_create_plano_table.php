<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plano', function (Blueprint $table) {
            $table->id('id_plano');
            $table->string('nome_plano');
            $table->decimal('valor', 10, 2);
            $table->text('descricao')->nullable();
            $table->date('data_criacao');
            $table->unsignedBigInteger('id_empresa');

            // Chave estrangeira correta
            $table->foreign('id_empresa')->references('id')->on('empresa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('plano', function (Blueprint $table) {
            $table->dropForeign(['id_empresa']);
        });

        Schema::dropIfExists('plano');
    }
};
