<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Avaliações",
 *     description="Gerencia as avaliações feitas pelas empresas"
 * )
 */
class AvaliacaoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/avaliacoes",
     *     tags={"Avaliações"},
     *     summary="Listar todas as avaliações",
     *     @OA\Response(response=200, description="Lista de avaliações")
     * )
     */
    public function index()
    {
        return Avaliacao::all();
    }

    /**
     * @OA\Get(
     *     path="/api/avaliacoes/{id}",
     *     tags={"Avaliações"},
     *     summary="Exibir uma avaliação específica",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Avaliação encontrada"),
     *     @OA\Response(response=404, description="Avaliação não encontrada")
     * )
     */
    public function show($id)
    {
        return Avaliacao::findOrFail($id);
    }

    /**
     * @OA\Post(
     *     path="/api/avaliacoes",
     *     tags={"Avaliações"},
     *     summary="Criar uma nova avaliação",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nota", "comentario", "data_avaliacao", "id_empresa"},
     *             @OA\Property(property="nota", type="integer", example=5),
     *             @OA\Property(property="comentario", type="string", example="Muito bom"),
     *             @OA\Property(property="data_avaliacao", type="string", format="date", example="2025-05-23"),
     *             @OA\Property(property="id_empresa", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Avaliação criada")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string',
            'data_avaliacao' => 'required|date',
            'id_empresa' => 'required|exists:empresas,id',
        ]);

        return Avaliacao::create($validated);
    }

    /**
     * @OA\Put(
     *     path="/api/avaliacoes/{id}",
     *     tags={"Avaliações"},
     *     summary="Atualizar uma avaliação",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nota", type="integer", example=4),
     *             @OA\Property(property="comentario", type="string", example="Atualização do comentário"),
     *             @OA\Property(property="data_avaliacao", type="string", format="date", example="2025-06-01")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Avaliação atualizada")
     * )
     */
    public function update(Request $request, $id)
    {
        $avaliacao = Avaliacao::findOrFail($id);
        $avaliacao->update($request->only(['nota', 'comentario', 'data_avaliacao']));
        return $avaliacao;
    }

    /**
     * @OA\Delete(
     *     path="/api/avaliacoes/{id}",
     *     tags={"Avaliações"},
     *     summary="Remover uma avaliação",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Avaliação removida")
     * )
     */
    public function destroy($id)
    {
        $avaliacao = Avaliacao::findOrFail($id);
        $avaliacao->delete();
        return response()->noContent();
    }
}
