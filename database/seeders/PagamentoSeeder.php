<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\Empresa;
use App\Models\EmpresaPlano; // Novo para associar a planos
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Str; // Para gerar strings aleatórias para referencia_transacao

class PagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $pedidos = Pedido::all();
        $empresas = Empresa::all();
        $empresaPlanos = EmpresaPlano::all(); // Pega todas as assinaturas de plano

        if ($empresas->isEmpty()) {
            $this->command->info('Nenhuma empresa encontrada. Execute EmpresaSeeder primeiro.');
            return;
        }

        $metodosPagamento = ['Cartão de Crédito', 'Boleto', 'Pix', 'Transferência Bancária'];
        $statusPagamento = ['Pendente', 'Aprovado', 'Recusado', 'Estornado'];

        // Cria pagamentos de exemplo
        for ($i = 0; $i < 70; $i++) { // Aumentar o número de pagamentos para 70
            $dataPagamento = $faker->dateTimeBetween('-1 year', 'now');
            $status = $faker->randomElement($statusPagamento);
            $valor = $faker->randomFloat(2, 50, 5000);

            $idPedido = null;
            $idEmpresaPlano = null;
            $idEmpresaPagadora = $empresas->random()->id_empresa; // Sempre terá uma empresa pagadora

            // Decide se o pagamento é para um pedido ou um plano, ou nenhum (geral)
            $tipoPagamento = $faker->randomElement(['pedido', 'plano', 'geral']);

            if ($tipoPagamento === 'pedido' && !$pedidos->isEmpty()) {
                $pedidoAssociado = $pedidos->random();
                $idPedido = $pedidoAssociado->id_pedido;
                $idEmpresaPagadora = $pedidoAssociado->id_empresa_contratante; // A pagadora é a contratante do pedido
                if ($pedidoAssociado->valor_estimado) {
                    $valor = $pedidoAssociado->valor_estimado;
                }
            } elseif ($tipoPagamento === 'plano' && !$empresaPlanos->isEmpty()) {
                $empresaPlanoAssociado = $empresaPlanos->random();
                $idEmpresaPlano = $empresaPlanoAssociado->id_empresa_plano;
                $idEmpresaPagadora = $empresaPlanoAssociado->id_empresa; // A pagadora é a empresa da assinatura
                // Tenta pegar o valor do plano, se não, usa um valor aleatório
                if ($empresaPlanoAssociado->plano) {
                    $valor = $empresaPlanoAssociado->plano->valor;
                } else {
                    $valor = $faker->randomFloat(2, 20, 300); // Valor de plano genérico
                }
            }
            // Se for 'geral', manterá a idEmpresaPagadora aleatória e valor aleatório

            Pagamento::create([
                'id_empresa_pagadora' => $idEmpresaPagadora,
                'valor' => $valor,
                'data_pagamento' => $dataPagamento->format('Y-m-d H:i:s'),
                'metodo_pagamento' => $faker->randomElement($metodosPagamento),
                'status' => $status,
                'referencia_transacao' => Str::random(10) . '-' . $faker->randomNumber(5, true), // Gerar ID único
                'id_pedido' => $idPedido,
                'id_empresa_plano' => $idEmpresaPlano,
            ]);
        }
    }
}
