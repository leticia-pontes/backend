<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GamificacaoPonto;
use App\Models\Empresa;

class GamificacaoPontoSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();

        GamificacaoPonto::create([
            'id_empresa' => $empresa->id,
            'tipo' => 'empresa_contratante',
            'pontos' => 380,
            'nivel' => 2,
        ]);
    }
}
