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
        Schema::create('empresas', function (Blueprint $table) {
            $table->increments('id_empresa'); // Primary Key AUTO_INCREMENT
            $table->string('nome', 100)->notNullable();
            $table->string('cnpj', 14)->unique()->notNullable(); // CNPJ único
            $table->string('email', 100)->unique()->notNullable(); // Email único
            $table->string('senha', 100)->notNullable();
            $table->string('telefone', 15)->nullable(); // Telefone pode ser nulo
            $table->string('endereco', 200)->nullable(); // Endereço pode ser nulo
            $table->dateTime('data_cadastro')->useCurrent();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
