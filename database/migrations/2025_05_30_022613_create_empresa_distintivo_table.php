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
        Schema::create('empresa_distintivo', function (Blueprint $table) {
            $table->unsignedInteger('empresa_id');
            $table->foreign('empresa_id')->references('id_empresa')->on('empresas')->onDelete('cascade');
            $table->foreignId('distintivo_id')->constrained('distintivos')->onDelete('cascade');
            $table->timestamp('data_conquista')->nullable();
            $table->primary(['empresa_id', 'distintivo_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_distintivo');
    }
};
