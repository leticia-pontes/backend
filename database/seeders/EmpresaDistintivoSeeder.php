<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Faker\Factory as Faker;

class EmpresaDistintivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // Busca os IDs existentes nas tabelas empresas e distintivos
        $empresaIds = DB::table('empresas')->pluck('id_empresa')->toArray();
        $distintivoIds = DB::table('distintivos')->pluck('id_distintivo')->toArray();

        // Criar associações aleatórias
        foreach ($empresaIds as $empresaId) {
            // Cada empresa pode ter de 1 a 5 distintivos diferentes
            $numDistintivos = rand(1, 5);
            $distintivosSelecionados = Arr::random($distintivoIds, $numDistintivos);

            foreach ($distintivosSelecionados as $distintivoId) {
                DB::table('empresa_distintivo')->insert([
                    'empresa_id' => $empresaId,
                    'distintivo_id' => $distintivoId,
                    'data_conquista' => $faker->dateTimeBetween('-1 year', 'now'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
