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
            TipoPerfilSeeder::class,
            NichoSeeder::class,
            TecnologiaSeeder::class,
            PerfilSeeder::class,
            EmpresaNichoSeeder::class,
            EmpresaTecnologiaSeeder::class,
            SeguidorSeeder::class,
            PlanoSeeder::class,
            ProjetoSeeder::class,
            PedidoSeeder::class,
            PedidoStatusSeeder::class,
            EmpresaPlanoSeeder::class,
            PagamentoSeeder::class,
            AvaliacaoSeeder::class,
        ]);
    }
}
