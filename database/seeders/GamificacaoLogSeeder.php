<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GamificacaoLog;
use App\Models\Empresa;

class GamificacaoLogSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();

        if (!$empresa) {
            $this->command->info('Nenhuma empresa encontrada para popular GamificacaoLog.');
            return;
        }

        $logs = [
            [
                'id_empresa' => $empresa->id,
                'tipo' => 'empresa_contratante',
                'evento' => 'Avaliação positiva recebida',
                'pontos' => 50,
            ],
            [
                'id_empresa' => $empresa->id,
                'tipo' => 'empresa_contratante',
                'evento' => 'Subiu para o nível 2',
                'pontos' => 0,
            ],
            [
                'id_empresa' => $empresa->id,
                'tipo' => 'desenvolvedor',
                'evento' => 'Concluiu um pedido',
                'pontos' => 100,
            ],
        ];

        foreach ($logs as $log) {
            GamificacaoLog::create($log);
        }
    }
}
