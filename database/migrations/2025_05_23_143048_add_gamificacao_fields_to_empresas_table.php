<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGamificacaoFieldsToEmpresasTable extends Migration
{
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->unsignedInteger('nivel')->default(1);
            $table->unsignedBigInteger('pontos')->default(0);
        });
    }

    public function down()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['nivel', 'pontos']);
        });
    }
}

