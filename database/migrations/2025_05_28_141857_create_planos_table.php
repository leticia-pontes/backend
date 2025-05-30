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
        Schema::create('planos', function (Blueprint $table) {
            $table->id('id_plano');
            $table->string('nome_plano', 50)->notNullable();
            $table->decimal('valor', 10, 2)->notNullable();
            $table->text('descricao')->notNullable();
            $table->date('data_criacao')->default(now()->toDateString());
            // Não usaremos timestamps()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};
