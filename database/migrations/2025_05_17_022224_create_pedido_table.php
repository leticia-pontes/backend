<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido', function (Blueprint $table) {
            $table->id();
            $table->date('data_pedido');
            $table->unsignedBigInteger('id');

            // Foreign key para empresa (referenciando 'id' da tabela empresa)
            $table->foreign('id')->references('id')->on('empresa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('pedido', function (Blueprint $table) {
            $table->dropForeign(['id']);
        });

        Schema::dropIfExists('pedido');
    }
};
