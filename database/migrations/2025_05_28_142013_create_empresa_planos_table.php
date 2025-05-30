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
        Schema::create('empresa_planos', function (Blueprint $table) {
            $table->id('id_empresa_plano'); // Chave primária
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_plano');
            $table->date('data_inicio')->notNullable();
            $table->date('data_fim')->nullable(); // Pode ser NULL para planos contínuos ou sem data de término definida
            $table->boolean('ativo')->default(true)->notNullable(); // Indica se a assinatura está ativa

            // Foreign Keys
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('cascade');
            $table->foreign('id_plano')->references('id_plano')->on('planos')->onDelete('restrict'); // Não permite deletar plano se houver empresas associadas

            // Não usaremos timestamps() aqui
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_planos');
    }
};
