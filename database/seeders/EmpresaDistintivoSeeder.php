<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmpresaDistintivo;
use App\Models\Empresa;
use App\Models\GamificacaoDistintivo;

class EmpresaDistintivoSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();
        $distintivo = GamificacaoDistintivo::first();

        EmpresaDistintivo::create([
            'id_empresa' => $empresa->id,
            'id_distintivo' => $distintivo->id,
            'data_conquista' => now(),
        ]);
    }
}
