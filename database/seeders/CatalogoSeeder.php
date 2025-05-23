<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catalogo;
use App\Models\Empresa;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();

        Catalogo::create([
            'arquivo' => 'catalogo.pdf',
            'nome_arquivo' => 'Catálogo 2025',
            'descricao' => 'Serviços de TI',
            'data_criacao' => now(),
            'versao' => '1.0',
            'ativo' => true,
            'id_empresa' => $empresa->id,
        ]);
    }
}
