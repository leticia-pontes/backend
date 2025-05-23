<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Avaliacao;
use App\Models\Empresa;

class AvaliacaoSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();

        Avaliacao::create([
            'nota' => 5,
            'comentario' => 'Excelente serviço!',
            'data_avaliacao' => now(),
            'id_empresa' => $empresa->id,
        ]);
    }
}
