<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        Pedido::create([
            'data_pedido' => now(),
            'id_empresa' => 1,
            'descricao' => 'Desenvolvimento de site',
            'valor' => 1500,
            'prazo_entrega' => now()->addMonth(),
        ]);
    }
}
