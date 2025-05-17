<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_status', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->date('data_status');
            $table->unsignedBigInteger('id_pedido');

            // Foreign key para pedido (referenciando 'id' da tabela pedido)
            $table->foreign('id_pedido')->references('id')->on('pedido')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('pedido_status', function (Blueprint $table) {
            $table->dropForeign(['id_pedido']);
        });

        Schema::dropIfExists('pedido_status');
    }
};
