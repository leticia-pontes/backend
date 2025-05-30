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
        Schema::create('seguidor', function (Blueprint $table) {
            $table->unsignedBigInteger('id_empresa_seguidor'); // A empresa que está seguindo
            $table->unsignedBigInteger('id_empresa_seguido');  // A empresa que está sendo seguida
            $table->dateTime('data_seguida')->useCurrent(); // Quando o "seguir" aconteceu

            // Chave primária composta para evitar duplicações
            $table->primary(['id_empresa_seguidor', 'id_empresa_seguido']);

            // Foreign Keys
            $table->foreign('id_empresa_seguidor')->references('id_empresa')->on('empresas')->onDelete('cascade');
            $table->foreign('id_empresa_seguido')->references('id_empresa')->on('empresas')->onDelete('cascade');

            // Esta tabela não usará os timestamps padrão do Laravel
            // $table->timestamps(); // Removido
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguidor');
    }
};
