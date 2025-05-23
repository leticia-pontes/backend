<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GamificacaoDistintivo;

class GamificacaoDistintivoSeeder extends Seeder
{
    public function run()
    {
        $distintivos = [
            ['nome' => 'Estreante', 'descricao' => 'Completou o primeiro pedido com sucesso', 'icone' => 'estreante.png', 'requisito_pontos' => 10],
            ['nome' => 'Veterano', 'descricao' => 'Concluiu 5 projetos', 'icone' => 'veterano.png', 'requisito_pontos' => 300],
            ['nome' => 'Lenda', 'descricao' => 'Completou 20 projetos com sucesso', 'icone' => 'lenda.png', 'requisito_pontos' => 1000],
            ['nome' => 'Avaliador Top', 'descricao' => 'Recebeu 5 avaliações positivas', 'icone' => 'avaliador_top.png', 'requisito_pontos' => 100],
            ['nome' => 'Popular', 'descricao' => 'Recebeu 10 avaliações no total', 'icone' => 'popular.png', 'requisito_pontos' => 200],
            ['nome' => 'Excelência', 'descricao' => 'Recebeu 10 avaliações com nota máxima', 'icone' => 'excelencia.png', 'requisito_pontos' => 500],
            ['nome' => 'Pontual', 'descricao' => 'Entregou 3 projetos antes do prazo', 'icone' => 'pontual.png', 'requisito_pontos' => 150],
            ['nome' => 'Comprometido', 'descricao' => 'Não atrasou nenhum projeto em 6 meses', 'icone' => 'comprometido.png', 'requisito_pontos' => 400],
            ['nome' => 'Colaborador', 'descricao' => 'Enviou 3 feedbacks para desenvolvedores', 'icone' => 'colaborador.png', 'requisito_pontos' => 120],
            ['nome' => 'Ativo', 'descricao' => 'Acessou a plataforma 7 dias seguidos', 'icone' => 'ativo.png', 'requisito_pontos' => 80],
            ['nome' => 'Participativo', 'descricao' => 'Fez 10 interações no sistema (feedbacks, avaliações, comentários)', 'icone' => 'participativo.png', 'requisito_pontos' => 150],
            ['nome' => 'Investidor', 'descricao' => 'Contratou projetos com valor total acima de R$ 10.000', 'icone' => 'investidor.png', 'requisito_pontos' => 800],
            ['nome' => 'Pioneiro', 'descricao' => 'Primeiras 10 empresas cadastradas', 'icone' => 'pioneiro.png', 'requisito_pontos' => 0],
            ['nome' => 'Influente', 'descricao' => 'Convidou 5 novas empresas para a plataforma', 'icone' => 'influente.png', 'requisito_pontos' => 200],
        ];

        foreach ($distintivos as $item) {
            GamificacaoDistintivo::updateOrCreate(['nome' => $item['nome']], $item);
        }
    }
}
