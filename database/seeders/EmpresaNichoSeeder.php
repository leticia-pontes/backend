<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Nicho;
use Illuminate\Support\Facades\DB; // Para usar o DB facade

class EmpresaNichoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = Empresa::all();
        $nichos = Nicho::all();

        if ($empresas->isEmpty()) {
            $this->command->info('Nenhuma empresa encontrada. Execute EmpresaSeeder primeiro.');
            return;
        }
        if ($nichos->isEmpty()) {
            $this->command->info('Nenhum nicho de mercado encontrado. Execute NichoSeeder primeiro.');
            return;
        }

        foreach ($empresas as $empresa) {
            // Cada empresa pode ter entre 1 e 3 nichos aleatórios
            $nichosAleatorios = $nichos->random(rand(1, min(3, $nichos->count())));

            foreach ($nichosAleatorios as $nicho) {
                // Anexa o nicho à empresa. O Eloquent cuidará da tabela pivô.
                $empresa->nichos()->attach($nicho->id_nicho);
            }
        }
    }
}
