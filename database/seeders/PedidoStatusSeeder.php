<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\PedidoStatus;
use Carbon\Carbon;
use Faker\Factory as Faker;

class PedidoStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pedidos = Pedido::all();
        $faker = Faker::create('pt_BR');

        if ($pedidos->isEmpty()) {
            $this->command->info('Nenhum pedido encontrado. Execute PedidoSeeder primeiro.');
            return;
        }

        $statusPossiveis = [
            'Pendente', 'Aceito', 'Em Andamento', 'Aguardando Aprovação', 'Concluído', 'Cancelado'
        ];

        foreach ($pedidos as $pedido) {
            $currentDate = Carbon::parse($pedido->data_pedido); // Começa com a data do pedido
            $totalstatus = rand(1, 4); // Cada pedido terá entre 1 e 4 atualizações de status

            // Sempre adiciona o status inicial "Pendente"
            PedidoStatus::create([
                'id_pedido' => $pedido->id_pedido,
                'status' => 'Pendente',
                'observacao' => 'Pedido criado.',
                'data_status' => $currentDate,
            ]);

            // Simula a progressão de status para os pedidos
            for ($i = 1; $i < $totalstatus; $i++) {
                $currentDate = $currentDate->addDays(rand(1, 30)); // Avança a data

                $statusAleatorio = $faker->randomElement(array_diff($statusPossiveis, ['Pendente'])); // Não repete "Pendente"
                if ($statusAleatorio === 'Concluído' || $statusAleatorio === 'Cancelado') {
                    // Se for um status final, para de adicionar mais status
                    PedidoStatus::create([
                        'id_pedido' => $pedido->id_pedido,
                        'status' => $statusAleatorio,
                        'observacao' => "Status finalizado como '{$statusAleatorio}'.",
                        'data_status' => $currentDate,
                    ]);
                    break;
                }

                PedidoStatus::create([
                    'id_pedido' => $pedido->id_pedido,
                    'status' => $statusAleatorio,
                    'observacao' => $faker->sentence(rand(3, 8)),
                    'data_status' => $currentDate,
                ]);
            }
        }
    }
}
