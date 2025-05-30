<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Avaliacao;
use App\Models\Empresa;
use Faker\Factory as Faker;
use Carbon\Carbon;

class AvaliacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $empresas = Empresa::all();

        if ($empresas->count() < 2) {
            $this->command->info('São necessárias pelo menos 2 empresas para criar avaliações.');
            return;
        }

        // Criar algumas avaliações
        for ($i = 0; $i < 50; $i++) { // Gerar 50 avaliações de exemplo
            $empresaAvaliador = $empresas->random();
            $empresaAvaliado = $empresas->random();

            // Garante que uma empresa não se avalie
            if ($empresaAvaliador->id_empresa === $empresaAvaliado->id_empresa) {
                // Tenta encontrar outra empresa para ser avaliada
                $outrasEmpresas = $empresas->where('id_empresa', '!=', $empresaAvaliador->id_empresa);
                if ($outrasEmpresas->isEmpty()) {
                    continue; // Não há outras empresas para avaliar
                }
                $empresaAvaliado = $outrasEmpresas->random();
            }

            // Garante que não haja avaliações duplicadas
            if (Avaliacao::where('id_empresa_avaliadora', $empresaAvaliador->id_empresa)
                        ->where('id_empresa_avaliada', $empresaAvaliado->id_empresa)
                        ->exists()) {
                continue; // Já existe uma avaliação entre essas duas empresas
            }

            Avaliacao::create([
                'id_empresa_avaliadora' => $empresaAvaliador->id_empresa,
                'id_empresa_avaliada' => $empresaAvaliado->id_empresa,
                'nota' => $faker->numberBetween(1, 5), // Nota de 1 a 5
                'comentario' => $faker->boolean(80) ? $faker->paragraph(rand(1, 3)) : null, // 80% de chance de ter comentário
                'data_avaliacao' => $faker->dateTimeBetween('-6 months', 'now'),
            ]);
        }
    }
}
