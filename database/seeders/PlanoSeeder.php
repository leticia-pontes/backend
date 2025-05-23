<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plano;
use App\Models\Empresa;

class PlanoSeeder extends Seeder
{
    public function run()
    {
        $empresas = Empresa::all();

        foreach ($empresas as $empresa) {
            Plano::create([
                'nome_plano' => 'Plano Gratuito',
                'valor' => 0.00,
                'descricao' => 'Plano básico gratuito com recursos limitados',
                'data_criacao' => now(),
                'id_empresa' => $empresa->id,
            ]);

            Plano::create([
                'nome_plano' => 'Plano Básico',
                'valor' => 49.90,
                'descricao' => 'Plano inicial para pequenas empresas',
                'data_criacao' => now(),
                'id_empresa' => $empresa->id,
            ]);

            Plano::create([
                'nome_plano' => 'Plano Premium',
                'valor' => 99.90,
                'descricao' => 'Plano completo com todos os recursos',
                'data_criacao' => now(),
                'id_empresa' => $empresa->id,
            ]);
        }
    }
}
