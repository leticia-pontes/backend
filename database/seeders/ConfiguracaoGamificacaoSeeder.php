<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ConfiguracaoGamificacao; // Importe o Model

class ConfiguracaoGamificacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'chave' => 'pontos_pedido_concluido',
                'descricao' => 'Pontos base concedidos ao desenvolvedor por um pedido concluído.',
                'valor_tipo' => 'int',
                'valor' => '30',
            ],
            [
                'chave' => 'pontos_boa_avaliacao',
                'descricao' => 'Pontos extras por uma boa avaliação (nota >= 4) em um pedido concluído.',
                'valor_tipo' => 'int',
                'valor' => '20', // Total de 30 + 20 = 50
            ],
            [
                'chave' => 'pontos_para_proximo_nivel',
                'descricao' => 'Quantidade de pontos necessária para subir de nível.',
                'valor_tipo' => 'int',
                'valor' => '200',
            ],
            // Mais configurações...
            // [
            //     'chave' => 'numero_avaliacoes_avaliador_iniciante',
            //     'descricao' => 'Número de avaliações para o distintivo "Avaliador Iniciante".',
            //     'valor_tipo' => 'int',
            //     'valor' => '1',
            // ],
        ];

        foreach ($configs as $config) {
            ConfiguracaoGamificacao::updateOrCreate(
                ['chave' => $config['chave']],
                $config
            );
        }
    }
}
