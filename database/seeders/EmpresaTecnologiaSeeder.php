<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Tecnologia;
use Illuminate\Support\Facades\DB; // Para usar o DB facade

class EmpresaTecnologiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = Empresa::all();
        $tecnologias = Tecnologia::all();

        if ($empresas->isEmpty()) {
            $this->command->info('Nenhuma empresa encontrada. Execute EmpresaSeeder primeiro.');
            return;
        }
        if ($tecnologias->isEmpty()) {
            $this->command->info('Nenhuma tecnologia encontrada. Execute TecnologiaSeeder primeiro.');
            return;
        }

        foreach ($empresas as $empresa) {
            // Cada empresa pode ter entre 2 e 5 tecnologias aleatórias
            $tecnologiasAleatorias = $tecnologias->random(rand(2, min(5, $tecnologias->count())));

            foreach ($tecnologiasAleatorias as $tecnologia) {
                // Anexa a tecnologia à empresa. O Eloquent cuidará da tabela pivô.
                $empresa->tecnologias()->attach($tecnologia->id_tecnologia);
            }
        }
    }
}
