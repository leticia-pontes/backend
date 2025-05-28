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
        Schema::create('perfis', function (Blueprint $table) {
            $table->increments('id_perfil'); // Primary Key AUTO_INCREMENT
            $table->binary('foto')->nullable(); // BLOB para a foto do perfil (considerar armazenamento em sistema de arquivos para produção)
            $table->text('biografia')->nullable();
            $table->string('redes_sociais', 255)->nullable(); // URLs ou handles das redes sociais
            $table->integer('seguidores_cache')->default(0); // Cache da contagem de seguidores (otimização de leitura)

            $table->unsignedInteger('id_empresa')->unique(); // Chave estrangeira para Empresa, UNIQUE para 1:1
            $table->unsignedInteger('id_tipo_perfil'); // Chave estrangeira para Tipo_Perfil

            // Foreign Keys
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('cascade');
            $table->foreign('id_tipo_perfil')->references('id_tipo_perfil')->on('tipo_perfis')->onDelete('restrict');

            // Não usaremos timestamps() aqui
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfis');
    }
};
