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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->increments('id_pedido'); // Primary Key AUTO_INCREMENT
            $table->unsignedInteger('id_empresa_contratante'); // Empresa que faz o pedido
            $table->unsignedInteger('id_empresa_desenvolvedora')->nullable(); // Empresa que recebe/executa o pedido (pode ser nulo inicialmente)
            $table->string('titulo', 255)->notNullable(); // Título breve do pedido
            $table->text('descricao')->notNullable(); // Descrição detalhada do que está sendo pedido
            $table->decimal('valor_estimado', 10, 2)->nullable(); // Valor estimado do pedido (opcional)
            $table->date('data_prazo')->nullable(); // Prazo para conclusão (opcional)
            $table->date('data_pedido')->default(now()->toDateString()); // Data em que o pedido foi feito

            // Foreign Keys
            $table->foreign('id_empresa_contratante')->references('id_empresa')->on('empresas')->onDelete('cascade');
            $table->foreign('id_empresa_desenvolvedora')->references('id_empresa')->on('empresas')->onDelete('set null'); // Se a desenvolvedora for deletada, o campo fica null, não apaga o pedido

            $table->timestamps(); // Para rastrear criação e atualização do pedido
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
