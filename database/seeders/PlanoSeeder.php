<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plano;

class PlanoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plano::firstOrCreate([
            'nome_plano' => 'Gratuito',
            'valor' => 0.00,
            'descricao' => 'Plano gratuito com funcionalidades básicas para empresas iniciantes.',
            'data_criacao' => now()->toDateString(),
        ]);

        Plano::firstOrCreate([
            'nome_plano' => 'Startup',
            'valor' => 99.90,
            'descricao' => 'Plano ideal para microempresas e startups, com mais recursos e suporte.',
            'data_criacao' => now()->toDateString(),
        ]);

        Plano::firstOrCreate([
            'nome_plano' => 'Corporativo',
            'valor' => 299.90,
            'descricao' => 'Plano completo para grandes empresas, com todos os recursos e suporte prioritário.',
            'data_criacao' => now()->toDateString(),
        ]);
    }
}
