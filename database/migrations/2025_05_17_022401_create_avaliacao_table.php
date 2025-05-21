<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avaliacao', function (Blueprint $table) {
            $table->id('id_avaliacao');
            $table->integer('nota');
            $table->text('comentario')->nullable();
            $table->date('data_avaliacao');
            $table->unsignedBigInteger('id');

            // Foreign key para empresa - referência à coluna 'id'
            $table->foreign('id')->references('id')->on('empresa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('avaliacao', function (Blueprint $table) {
            $table->dropForeign(['id']);
        });

        Schema::dropIfExists('avaliacao');
    }
};
