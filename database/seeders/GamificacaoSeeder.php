<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GamificacaoLog;
use App\Models\Empresa;

class GamificacaoLogSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();

        GamificacaoLog::create([
            'id_empresa' => $empresa->id,
            'tipo' => 'ponto',
            'evento' => 'Pedido concluído',
            'pontos' => 50,
        ]);
    }
}
