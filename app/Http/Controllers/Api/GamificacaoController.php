<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;

class GamificacaoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/gamificacao/status",
     *     summary="Retorna o nível, pontos e progresso da empresa",
     *     tags={"Gamificação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Status de gamificação da empresa",
     *         @OA\JsonContent(
     *             @OA\Property(property="nivel", type="integer", example=2),
     *             @OA\Property(property="pontos", type="integer", example=380),
     *             @OA\Property(property="proximo_nivel", type="integer", example=400)
     *         )
     *     )
     * )
     */
    public function status()
    {
        $empresa = auth()->user();
        return response()->json([
            'nivel' => $empresa->nivel,
            'pontos' => $empresa->pontos,
            'proximo_nivel' => ($empresa->nivel * 200),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/gamificacao/ranking",
     *     summary="Retorna o ranking das empresas com mais pontos",
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
        $empresas = Empresa::orderByDesc('pontos')->limit(10)->get(['nome', 'nivel', 'pontos']);
        return response()->json($empresas);
    }

    /**
     * @OA\Get(
     *     path="/api/gamificacao/distintivos",
     *     summary="Retorna os distintivos desbloqueados pela empresa",
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
     *     )
     * )
     */
    public function distintivos()
    {
        $empresa = auth()->user();
        $distintivos = $empresa->distintivos()->get();
        return response()->json($distintivos);
    }
}
