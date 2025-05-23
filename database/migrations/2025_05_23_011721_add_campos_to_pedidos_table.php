<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToPedidosTable extends Migration
{
    public function up()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->text('descricao')->nullable();
            $table->decimal('valor', 10, 2)->nullable();
            $table->date('prazo_entrega')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['descricao', 'valor', 'prazo_entrega']);
        });
    }
}
