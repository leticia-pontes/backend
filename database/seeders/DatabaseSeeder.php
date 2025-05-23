<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(EmpresaSeeder::class);

        $this->call(PerfilSeeder::class);

        $this->call(PlanoSeeder::class);

        $this->call(AvaliacaoSeeder::class);

        $this->call(CatalogoSeeder::class);

        $this->call(GamificacaoDistintivoSeeder::class);
        $this->call(GamificacaoPontoSeeder::class);
        $this->call(GamificacaoLogSeeder::class);
        $this->call(EmpresaDistintivoSeeder::class);

        $this->call(PedidoSeeder::class);
        $this->call(PedidoStatusSeeder::class);
        $this->call(PagamentoSeeder::class);

        $this->call(ConfiguracaoGamificacaoSeeder::class);
    }
}
