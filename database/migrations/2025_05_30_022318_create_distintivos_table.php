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
        Schema::create('distintivos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->unique();
            $table->text('descricao');
            $table->string('icone')->nullable();
            $table->integer('pontos_necessarios')->nullable();
            $table->string('condicao_especifica')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distintivos');
    }
};
