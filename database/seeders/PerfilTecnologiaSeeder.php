<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Perfil;
use App\Models\Tecnologia;

class PerfilTecnologiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que existem perfis e tecnologias para relacionar
        $perfis = Perfil::all();
        $tecnologias = Tecnologia::all();

        if ($perfis->isEmpty() || $tecnologias->isEmpty()) {
            $this->command->info('Nenhum Perfil ou Tecnologia encontrado para relacionar. Certifique-se de que PerfilSeeder e TecnologiaSeeder foram executados.');
            return;
        }

        // Relaciona cada perfil com algumas tecnologias aleatórias
        foreach ($perfis as $perfil) {
            // Pega 1 a 5 tecnologias aleatórias
            $randomTecnologias = $tecnologias->random(rand(1, min(5, $tecnologias->count())));
            $perfil->tecnologias()->attach($randomTecnologias->pluck('id_tecnologia')->toArray());
        }
    }
}
