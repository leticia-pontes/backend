<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PedidoStatus;
use App\Models\Pedido;

class PedidoStatusSeeder extends Seeder
{
    public function run()
    {
        $pedidos = Pedido::all();

        foreach ($pedidos as $pedido) {
            PedidoStatus::create([
                'status' => 'aguardando',
                'data_status' => $pedido->data_pedido,
                'id_pedido' => $pedido->id,
            ]);

            PedidoStatus::create([
                'status' => 'em andamento',
                'data_status' => now()->addDays(3),
                'id_pedido' => $pedido->id,
            ]);
        }
    }
}
