<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Distintivo;

class DistintivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distintivos = [
            [
                'titulo' => 'Avaliador Iniciante',
                'descricao' => 'Primeira avaliação realizada com sucesso.',
                'icone' => 'badge_avaliador_iniciante.png',
                'pontos_necessarios' => 10,
                'condicao_especifica' => null,
            ],
            [
                'titulo' => 'Avaliador Experiente',
                'descricao' => 'Realizou 5 avaliações positivas.',
                'icone' => 'badge_avaliador_experiente.png',
                'pontos_necessarios' => 50,
                'condicao_especifica' => '5 avaliações com nota >= 4',
            ],
            [
                'titulo' => 'Conquistador de Projetos',
                'descricao' => 'Concluiu seu primeiro projeto.',
                'icone' => 'badge_conquistador_projeto.png',
                'pontos_necessarios' => 100,
                'condicao_especifica' => '1 projeto com status "concluído"',
            ],
            [
                'titulo' => 'Mestre da Gamificação',
                'descricao' => 'Atingiu o nível 5 de gamificação.',
                'icone' => 'badge_mestre_gamificacao.png',
                'pontos_necessarios' => 500,
                'condicao_especifica' => 'atingir nivel 5',
            ],
            [
                'titulo' => 'Colaborador Fiel',
                'descricao' => 'Permaneceu ativo na plataforma por 1 ano.',
                'icone' => 'badge_fiel.png',
                'pontos_necessarios' => null,
                'condicao_especifica' => '1 ano de cadastro ativo',
            ],
            [
                'titulo' => 'Primeiro Pedido',
                'descricao' => 'Empresa realizou seu primeiro pedido na plataforma.',
                'icone' => 'badge_primeiro_pedido.png',
                'pontos_necessarios' => 20,
                'condicao_especifica' => '1 pedido realizado',
            ],
            [
                'titulo' => 'Gigante das Avaliações',
                'descricao' => 'Realizou 100 avaliações ou mais.',
                'icone' => 'badge_gigante_avaliacoes.png',
                'pontos_necessarios' => 1000,
                'condicao_especifica' => '100+ avaliações',
            ],
        ];

        foreach ($distintivos as $distintivo) {
            Distintivo::updateOrCreate(
                ['titulo' => $distintivo['titulo']],
                $distintivo
            );
        }
    }
}
