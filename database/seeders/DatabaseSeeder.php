<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            EmpresaSeeder::class,
            DistintivoSeeder::class,
            TipoPerfilSeeder::class,
            NichoSeeder::class,
            TecnologiaSeeder::class,
            PerfilSeeder::class,
            SeguidorSeeder::class,
            PlanoSeeder::class,
            ProjetoSeeder::class,
            PedidoSeeder::class,
            PedidoStatusSeeder::class,
            EmpresaPlanoSeeder::class,
            PagamentoSeeder::class,
            AvaliacaoSeeder::class,
            PerfilNichoSeeder::class,
            PerfilTecnologiaSeeder::class,
            ConfiguracaoGamificacaoSeeder::class
        ]);
    }
}
