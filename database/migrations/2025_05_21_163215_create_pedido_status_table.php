<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoStatusTable extends Migration
{
    public function up()
    {
        Schema::create('pedido_status', function (Blueprint $table) {
            $table->id();
            $table->string('status', 50);
            $table->date('data_status');
            $table->foreignId('id_pedido')->constrained('pedidos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedido_status');
    }
}
