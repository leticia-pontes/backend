<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perfil;
use App\Models\Empresa;

class PerfilSeeder extends Seeder
{
    public function run()
    {
        $empresas = Empresa::all();

        foreach ($empresas as $empresa) {
            Perfil::create([
                'foto' => null,
                'biografia' => 'Biografia da empresa ' . $empresa->nome,
                'nicho_mercado' => 'Tecnologia',
                'tecnologia' => 'Laravel, Vue.js',
                'redes_sociais' => json_encode(['facebook' => 'fb.com/'.$empresa->nome, 'linkedin' => 'linkedin.com/'.$empresa->nome]),
                'id_empresa' => $empresa->id,
            ]);
        }
    }
}
