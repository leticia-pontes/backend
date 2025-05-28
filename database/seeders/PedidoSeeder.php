<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\TipoPerfil; // Para filtrar empresas
use Faker\Factory as Faker;
use Carbon\Carbon;

class PedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // Pega as empresas Contratantes
        $tipoContratante = TipoPerfil::where('nome_tipo', 'Contratante')->first();
        $empresasContratantes = Empresa::whereHas('perfil', function ($query) use ($tipoContratante) {
            $query->where('id_tipo_perfil', $tipoContratante->id_tipo_perfil);
        })->get();

        // Pega as empresas Desenvolvedoras
        $tipoDesenvolvedor = TipoPerfil::where('nome_tipo', 'Desenvolvedor')->first();
        $empresasDesenvolvedoras = Empresa::whereHas('perfil', function ($query) use ($tipoDesenvolvedor) {
            $query->where('id_tipo_perfil', $tipoDesenvolvedor->id_tipo_perfil);
        })->get();

        if ($empresasContratantes->isEmpty()) {
            $this->command->info('Nenhuma empresa do tipo "Contratante" encontrada. Execute EmpresaSeeder e PerfilSeeder primeiro.');
            return;
        }
        // Desenvolvedoras podem ser vazias para pedidos sem desenvolvedora atribuída.

        foreach ($empresasContratantes as $contratante) {
            // Cada contratante pode fazer entre 1 e 3 pedidos
            $numPedidos = rand(1, 3);
            for ($i = 0; $i < $numPedidos; $i++) {
                $dataPedido = $faker->dateTimeBetween('-1 year', 'now');
                $dataPrazo = $dataPedido->format('Y-m-d'); // Começa com o pedido e pode ser ajustado

                // 70% de chance de ter uma desenvolvedora atribuída (se houver desenvolvedoras)
                $desenvolvedoraId = null;
                if (!$empresasDesenvolvedoras->isEmpty() && $faker->boolean(70)) {
                    $desenvolvedoraId = $empresasDesenvolvedoras->random()->id_empresa;
                }

                // Ajusta o prazo se houver desenvolvedora ou se for um pedido mais antigo
                if ($desenvolvedoraId || $faker->boolean(50)) { // 50% de chance de ter prazo mesmo sem desenvolvedora
                    $dataPrazo = $faker->dateTimeBetween($dataPedido, Carbon::now()->addMonths(6))->format('Y-m-d');
                } else {
                    $dataPrazo = null; // Sem prazo definido para alguns
                }

                Pedido::create([
                    'id_empresa_contratante' => $contratante->id_empresa,
                    'id_empresa_desenvolvedora' => $desenvolvedoraId,
                    'titulo' => $faker->realText(50), // Título mais curto
                    'descricao' => $faker->paragraph(rand(3, 7)),
                    'valor_estimado' => $faker->boolean(60) ? $faker->randomFloat(2, 500, 50000) : null, // 60% chance de ter valor
                    'data_prazo' => $dataPrazo,
                    'data_pedido' => $dataPedido->format('Y-m-d'),
                ]);
            }
        }
    }
}
