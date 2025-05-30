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
        Schema::create('pedido_status', function (Blueprint $table) {
            $table->id('id_pedido_status'); // Primary Key AUTO_INCREMENT
            $table->unsignedBigInteger('id_pedido'); // Chave estrangeira para o Pedido
            $table->string('status', 50)->notNullable(); // O status atual (ex: 'Pendente', 'Aceito', 'Em Andamento', 'Concluído', 'Cancelado')
            $table->text('observacao')->nullable(); // Alguma observação sobre a mudança de status
            $table->timestamp('data_status')->useCurrent(); // Data e hora da mudança de status

            // Foreign Key
            $table->foreign('id_pedido')->references('id_pedido')->on('pedidos')->onDelete('cascade');

            // Esta tabela não precisa de created_at/updated_at separados, pois data_status já serve
            // $table->timestamps(); // Removido
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_status');
    }
};
