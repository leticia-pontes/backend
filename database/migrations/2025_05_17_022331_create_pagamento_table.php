<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamento', function (Blueprint $table) {
            $table->id('id_pagamento');
            $table->decimal('valor', 10, 2);
            $table->date('data_pagamento');
            $table->string('metodo_pagamento');
            $table->string('status');
            $table->unsignedBigInteger('id_pedido');

            // Foreign key para pedido (referenciando a coluna 'id' da tabela 'pedido')
            $table->foreign('id_pedido')->references('id')->on('pedido')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('pagamento', function (Blueprint $table) {
            $table->dropForeign(['id_pedido']);
        });

        Schema::dropIfExists('pagamento');
    }
};
