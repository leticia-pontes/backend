<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Empresa;

class GamificacaoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/gamificacao/status",
     *     summary="Retorna o nível, pontos e progresso da empresa autenticada",
     *     tags={"Gamificação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Status de gamificação da empresa",
     *         @OA\JsonContent(
     *             @OA\Property(property="nivel", type="integer", example=2),
     *             @OA\Property(property="pontos", type="integer", example=380),
     *             @OA\Property(property="proximo_nivel", type="integer", example=400),
     *             @OA\Property(property="progresso", type="number", format="float", example=95.0)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Empresa não encontrada")
     * )
     */
    public function status()
    {
        $empresa = auth()->user();

        Log::info($empresa);

        if (!$empresa) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        $nivel = $empresa->nivel;
        $pontos = $empresa->pontos;
        $pontos_necessarios = $nivel * 200;
        $pontos_base_nivel_anterior = ($nivel - 1) * 200;

        $pontos_no_nivel = $pontos - $pontos_base_nivel_anterior;
        $pontos_para_proximo = $pontos_necessarios - $pontos_base_nivel_anterior;

        $progresso = ($pontos_para_proximo > 0)
            ? round(($pontos_no_nivel / $pontos_para_proximo) * 100, 2)
            : 100.0;

        return response()->json([
            'nivel' => $nivel,
            'pontos' => $pontos,
            'proximo_nivel' => $pontos_necessarios,
            'progresso' => $progresso,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/gamificacao/distintivos",
     *     summary="Retorna os distintivos desbloqueados pela empresa autenticada",
     *     tags={"Gamificação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de distintivos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="titulo", type="string", example="Avaliador Top"),
     *                 @OA\Property(property="descricao", type="string", example="5 avaliações positivas"),
     *                 @OA\Property(property="icone", type="string", example="estrela.png")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Empresa não encontrada")
     * )
     */
    public function distintivos()
    {
        $empresa = auth()->user();

        if (!$empresa) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        $distintivos = $empresa->distintivos()->get(['titulo', 'descricao', 'icone']);

        return response()->json($distintivos);
    }

    /**
     * @OA\Get(
     *     path="/api/gamificacao/ranking",
     *     summary="Retorna o ranking das empresas com mais pontos e maior nível",
     *     tags={"Gamificação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Ranking de empresas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="nome", type="string", example="Empresa Exemplo"),
     *                 @OA\Property(property="nivel", type="integer", example=3),
     *                 @OA\Property(property="pontos", type="integer", example=600)
     *             )
     *         )
     *     )
     * )
     */
    public function ranking()
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        $empresas = Empresa::select('nome', 'nivel', 'pontos')
            ->orderByDesc('nivel')
            ->orderByDesc('pontos')
            ->limit(10)
            ->get();

        return response()->json($empresas);
    }

    /**
     * @OA\Get(
     *     path="/api/gamificacao/{id}",
     *     summary="Retorna dados de gamificação de uma empresa específica",
     *     tags={"Gamificação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados de gamificação da empresa",
     *         @OA\JsonContent(
     *             @OA\Property(property="nivel", type="integer", example=2),
     *             @OA\Property(property="pontos", type="integer", example=380),
     *             @OA\Property(property="proximo_nivel", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Empresa não encontrada")
     * )
     */
    public function show($id)
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json(['message' => 'Empresa não encontrada'], 404);
        }

        return response()->json([
            'nivel' => $empresa->nivel,
            'pontos' => $empresa->pontos,
            'proximo_nivel' => ($empresa->nivel * 200),
        ]);
    }
}
