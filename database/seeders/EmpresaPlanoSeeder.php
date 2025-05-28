<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Plano;
use App\Models\EmpresaPlano;
use Carbon\Carbon;
use Faker\Factory as Faker;

class EmpresaPlanoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = Empresa::all();
        $planos = Plano::all();

        if ($empresas->isEmpty()) {
            $this->command->info('Nenhuma empresa encontrada. Execute EmpresaSeeder primeiro.');
            return;
        }
        if ($planos->isEmpty()) {
            $this->command->info('Nenhum plano encontrado. Execute PlanoSeeder primeiro.');
            return;
        }

        // Recupera os IDs dos planos específicos
        $planoGratuitoId = $planos->firstWhere('nome_plano', 'Gratuito')->id_plano ?? null;
        $planoStartupId = $planos->firstWhere('nome_plano', 'Startup')->id_plano ?? null;
        $planoCorporativoId = $planos->firstWhere('nome_plano', 'Corporativo')->id_plano ?? null;

        if (!$planoGratuitoId || !$planoStartupId || !$planoCorporativoId) {
            $this->command->info('Um ou mais planos não foram encontrados. Verifique se PlanoSeeder foi executado corretamente.');
            return;
        }

        $faker = Faker::create('pt_BR');

        foreach ($empresas as $empresa) {
            // Aleatoriamente, atribui um dos planos
            $planoAleatorioId = $faker->randomElement([
                $planoGratuitoId,
                $planoStartupId,
                $planoCorporativoId
            ]);

            $dataInicio = Carbon::now()->subDays(rand(1, 365)); // Data de início no último ano
            $dataFim = null;
            $ativo = true;

            // Se for um plano pago, há uma chance de ter terminado ou estar inativo
            if ($planoAleatorioId !== $planoGratuitoId) {
                // 30% de chance de ter terminado ou estar inativo
                if (rand(0, 9) < 3) { // 0,1,2 (30% chance)
                    $dataFim = $faker->dateTimeBetween($dataInicio, 'now');
                    $ativo = false; // Se tiver data fim, já está inativo
                } else {
                    // Para planos ativos, simula duração de 1 a 12 meses
                    $dataFim = $dataInicio->copy()->addMonths(rand(1, 12));
                }
            }

            EmpresaPlano::create([
                'id_empresa' => $empresa->id_empresa,
                'id_plano' => $planoAleatorioId,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'ativo' => $ativo,
            ]);
        }
    }
}
