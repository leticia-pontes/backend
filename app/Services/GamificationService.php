<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\Distintivo;
use App\Models\ConfiguracaoGamificacao;
use App\Enums\PedidoStatusEnum;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GamificationService
{
    /**
     * Adiciona pontos a uma empresa e recalcula seu nível.
     * Os valores de pontos são obtidos de ConfiguracaoGamificacao.
     *
     * @param Empresa $empresa
     * @param int $pontosToAdd Os pontos a serem adicionados
     * @return void
     */
    public function addPoints(Empresa $empresa, int $pontosToAdd): void
    {
        $empresa->pontos += $pontosToAdd;

        $pontosParaProximoNivel = ConfiguracaoGamificacao::getValor('pontos_para_proximo_nivel', 200);
        $empresa->nivel = floor($empresa->pontos / $pontosParaProximoNivel) + 1;

        $empresa->save();
        Log::info("Empresa {$empresa->id_empresa} ganhou {$pontosToAdd} pontos. Total: {$empresa->pontos}, Nível: {$empresa->nivel}");
    }

    /**
     * Remove pontos de uma empresa e recalcula seu nível.
     * Garante que os pontos não fiquem negativos.
     *
     * @param Empresa $empresa
     * @param int $pontosToRemove Os pontos a serem removidos
     * @return void
     */
    public function removePoints(Empresa $empresa, int $pontosToRemove): void
    {
        // Garante que os pontos não fiquem negativos
        $empresa->pontos = max(0, $empresa->pontos - $pontosToRemove);

        $pontosParaProximoNivel = ConfiguracaoGamificacao::getValor('pontos_para_proximo_nivel', 200);
        $empresa->nivel = floor($empresa->pontos / $pontosParaProximoNivel) + 1;

        $empresa->save();
        Log::info("Empresa {$empresa->id_empresa} perdeu {$pontosToRemove} pontos. Total: {$empresa->pontos}, Nível: {$empresa->nivel}");
    }

    /**
     * Concede distintivos a uma empresa com base nas condições.
     * Usa o ID do distintivo para as condições.
     *
     * @param Empresa $empresa
     * @return array<string> Títulos dos distintivos recém-conquistados
     */
    public function awardBadges(Empresa $empresa): array
    {
        $newlyAwardedBadges = [];
        $allDistintivos = Distintivo::all();

        $mapaTitulosIds = [
            'Avaliador Iniciante' => 1,
            'Avaliador Experiente' => 2,
            'Conquistador de Projetos' => 3,
            'Mestre da Gamificação' => 4,
            'Colaborador Fiel' => 5,
            'Primeiro Pedido' => 6,
            'Gigante das Avaliações' => 7,
        ];

        $ID_AVALIADOR_INICIANTE     = $mapaTitulosIds['Avaliador Iniciante'];
        $ID_AVALIADOR_EXPERIENTE    = $mapaTitulosIds['Avaliador Experiente'];
        $ID_CONQUISTADOR_PROJETOS   = $mapaTitulosIds['Conquistador de Projetos'];
        $ID_MESTRE_GAMIFICACAO      = $mapaTitulosIds['Mestre da Gamificação'];
        $ID_COLABORADOR_FIEL        = $mapaTitulosIds['Colaborador Fiel'];
        $ID_PRIMEIRO_PEDIDO         = $mapaTitulosIds['Primeiro Pedido'];
        $ID_GIGANTE_AVALIACOES      = $mapaTitulosIds['Gigante das Avaliações'];


        foreach ($allDistintivos as $distintivo) {
            if ($empresa->distintivos->contains($distintivo->id_distintivo)) {
                continue;
            }

            $awarded = false;

            switch ($distintivo->id_distintivo) {
                case $ID_AVALIADOR_INICIANTE:
                    if ($empresa->avaliacoes()->count() >= 1) {
                        $awarded = true;
                    }
                    break;

                case $ID_AVALIADOR_EXPERIENTE:
                    if ($empresa->avaliacoes()->where('nota', '>=', 4)->count() >= 5) {
                        $awarded = true;
                    }
                    break;

                case $ID_CONQUISTADOR_PROJETOS:
                    // Verifica projetos com status 'concluido' no histórico
                    if ($empresa->pedidosDesenvolvidos()->whereHas('statusHistorico', function ($query) {
                        $query->where('status', PedidoStatusEnum::Concluido->value);
                    })->count() >= 1) {
                        $awarded = true;
                    }
                    break;

                case $ID_MESTRE_GAMIFICACAO:
                    if ($empresa->nivel >= 5) {
                        $awarded = true;
                    }
                    break;

                case $ID_COLABORADOR_FIEL:
                    // Distintivo concedido se a empresa estiver ativa por 1 ano
                    if ($empresa->created_at && $empresa->created_at->diffInYears(Carbon::now()) >= 1) {
                        $awarded = true;
                    }
                    break;

                case $ID_PRIMEIRO_PEDIDO:
                    // Distintivo concedido se a empresa contratante realizou 1 pedido
                    if ($empresa->pedidosContratados()->count() >= 1) {
                        $awarded = true;
                    }
                    break;

                case $ID_GIGANTE_AVALIACOES:
                    // Distintivo concedido se a empresa realizou 100 ou mais avaliações
                    if ($empresa->avaliacoes()->count() >= 100) {
                        $awarded = true;
                    }
                    break;

                default:
                    // Lógica para distintivos que dependem apenas de 'pontos_necessarios'
                    if ($distintivo->pontos_necessarios !== null && $empresa->pontos >= $distintivo->pontos_necessarios) {
                        $awarded = true;
                    }
                    break;
            }

            if ($awarded) {
                $empresa->distintivos()->attach($distintivo->id_distintivo, ['data_conquista' => now()]);
                $newlyAwardedBadges[] = $distintivo->titulo;
                Log::info("Empresa {$empresa->id_empresa} conquistou o distintivo: {$distintivo->titulo} (ID: {$distintivo->id_distintivo})");
            }
        }

        return $newlyAwardedBadges;
    }
}
