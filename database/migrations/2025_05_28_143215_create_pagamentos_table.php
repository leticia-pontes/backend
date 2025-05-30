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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id('id_pagamento'); // Primary Key AUTO_INCREMENT
            $table->unsignedBigInteger('id_empresa_pagadora'); // A empresa que realizou o pagamento
            $table->decimal('valor', 10, 2)->notNullable();
            $table->dateTime('data_pagamento')->useCurrent(); // Data e hora do pagamento efetivo
            $table->string('metodo_pagamento', 50)->notNullable(); // Ex: 'Cartão de Crédito', 'Boleto', 'Pix'
            $table->string('status', 50)->notNullable(); // Ex: 'Pendente', 'Aprovado', 'Recusado', 'Estornado'
            $table->string('referencia_transacao', 100)->nullable()->unique(); // ID de transação do gateway de pagamento, fatura, etc.

            // Chaves estrangeiras opcionais para ligar o pagamento a uma transação específica
            $table->unsignedBigInteger('id_pedido')->nullable(); // Pagamento referente a um pedido
            $table->unsignedBigInteger('id_empresa_plano')->nullable(); // Pagamento referente a uma assinatura de plano

            // Foreign Keys
            $table->foreign('id_empresa_pagadora')->references('id_empresa')->on('empresas')->onDelete('cascade');
            $table->foreign('id_pedido')->references('id_pedido')->on('pedidos')->onDelete('set null');
            $table->foreign('id_empresa_plano')->references('id_empresa_plano')->on('empresa_planos')->onDelete('set null');

            $table->timestamps(); // created_at e updated_at para controle do registro no sistema
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
