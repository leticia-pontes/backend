<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagamentosTable extends Migration
{
    public function up()
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor', 10, 2);
            $table->date('data_pagamento');
            $table->string('metodo_pagamento', 50);
            $table->string('status', 50);
            $table->foreignId('id_pedido')->constrained('pedidos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagamentos');
    }
}
