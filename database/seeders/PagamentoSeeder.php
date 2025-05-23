<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pagamento;
use App\Models\Pedido;

class PagamentoSeeder extends Seeder
{
    public function run(): void
    {
        $pedido = Pedido::first();

        Pagamento::create([
            'valor' => 1500.00,
            'data_pagamento' => now(),
            'metodo_pagamento' => 'cartão de crédito',
            'status' => 'pago',
            'id_pedido' => $pedido->id,
        ]);
    }
}
