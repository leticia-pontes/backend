<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoPerfil;

class TipoPerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que não haja duplicatas ao rodar o seeder múltiplas vezes
        TipoPerfil::firstOrCreate(['nome_tipo' => 'Contratante']);
        TipoPerfil::firstOrCreate(['nome_tipo' => 'Desenvolvedor']);
    }
}
