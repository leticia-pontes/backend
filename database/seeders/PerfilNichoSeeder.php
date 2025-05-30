<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Perfil;
use App\Models\Nicho;

class PerfilNichoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perfis = Perfil::all();
        $nichos = Nicho::all();

        if ($perfis->isEmpty() || $nichos->isEmpty()) {
            $this->command->info('Nenhum Perfil ou Nicho encontrado para relacionar. Certifique-se de que PerfilSeeder e NichoSeeder foram executados.');
            return;
        }

        // Relaciona cada perfil com alguns nichos aleatórios
        foreach ($perfis as $perfil) {
            // Pega 1 a 3 nichos aleatórios
            $randomNichos = $nichos->random(rand(1, min(3, $nichos->count())));
            $perfil->nichos()->attach($randomNichos->pluck('id_nicho')->toArray());
        }
    }
}
