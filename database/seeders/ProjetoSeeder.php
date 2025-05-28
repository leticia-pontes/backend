<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Projeto;
use App\Models\TipoPerfil; // Para filtrar empresas Desenvolvedoras
use Faker\Factory as Faker;
use Carbon\Carbon;

class ProjetoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pega as empresas que são do tipo 'Desenvolvedor'
        $tipoDesenvolvedor = TipoPerfil::where('nome_tipo', 'Desenvolvedor')->first();

        if (!$tipoDesenvolvedor) {
            $this->command->info('Tipo de perfil "Desenvolvedor" não encontrado. Execute TipoPerfilSeeder primeiro.');
            return;
        }

        $empresasDesenvolvedoras = Empresa::whereHas('perfil', function ($query) use ($tipoDesenvolvedor) {
            $query->where('id_tipo_perfil', $tipoDesenvolvedor->id_tipo_perfil);
        })->get();


        if ($empresasDesenvolvedoras->isEmpty()) {
            $this->command->info('Nenhuma empresa do tipo "Desenvolvedor" encontrada. Execute EmpresaSeeder e PerfilSeeder primeiro.');
            return;
        }

        $faker = Faker::create('pt_BR');

        foreach ($empresasDesenvolvedoras as $empresa) {
            // Cada empresa desenvolvedora cria entre 1 e 5 projetos
            $numProjetos = rand(1, 5);
            for ($i = 0; $i < $numProjetos; $i++) {
                $dataInicio = $faker->dateTimeBetween('-2 years', 'now');
                $dataFim = null;
                $status = $faker->randomElement(['Em Andamento', 'Concluído', 'Pausado']);

                if ($status === 'Concluído') {
                    $dataFim = $faker->dateTimeBetween($dataInicio, 'now');
                }

                Projeto::create([
                    'nome_projeto' => $faker->sentence(3, true) . ' ' . $faker->word . ' App', // Gera uma frase de 3 palavras
                    'descricao' => $faker->paragraph(rand(2, 5)),
                    'data_inicio' => $dataInicio->format('Y-m-d'),
                    'data_fim' => $dataFim ? $dataFim->format('Y-m-d') : null,
                    'status' => $status,
                    'url_projeto' => $faker->boolean(70) ? $faker->url : null, // 70% de chance de ter uma URL
                    'imagem_destaque_url' => $faker->boolean(60) ? $faker->imageUrl(640, 480, 'software', true) : null, // 60% de chance de ter imagem
                    'id_empresa' => $empresa->id_empresa,
                ]);
            }
        }
    }
}
