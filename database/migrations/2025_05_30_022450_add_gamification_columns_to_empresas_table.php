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
        Schema::table('empresas', function (Blueprint $table) {
            $table->integer('nivel')->default(1)->after('data_cadastro');
            $table->integer('pontos')->default(0)->after('nivel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Remove as colunas em caso de rollback
            $table->dropColumn(['nivel', 'pontos']);
        });
    }
};
