<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracaoGamificacao;

class ConfiguracaoGamificacaoSeeder extends Seeder
{
    public function run(): void
    {
        ConfiguracaoGamificacao::insert([
            ['chave' => 'pontos_por_avaliacao', 'valor' => 10],
            ['chave' => 'pontos_por_pedido_concluido', 'valor' => 50],
            ['chave' => 'pontos_por_distintivo', 'valor' => 100],
        ]);
    }
}
