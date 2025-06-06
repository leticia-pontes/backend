<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nicho;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Nicho",
 *     description="Operações relacionadas a Nichos"
 * )
 */
class NichoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/nichos",
     *     operationId="getNichosList",
     *     tags={"Nicho"},
     *     summary="Listar todos os nichos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de nichos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Nicho")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $nichos = Nicho::all();
        return response()->json($nichos);
    }

    /**
     * @OA\Post(
     *     path="/api/nichos",
     *     operationId="createNicho",
     *     tags={"Nicho"},
     *     summary="Criar novo nicho",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome_nicho"},
     *             @OA\Property(property="nome_nicho", type="string", maxLength=255, example="Exemplo de Nicho")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Nicho criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Nicho")
     *     ),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_nicho' => 'required|string|max:255',
        ]);

        $nicho = Nicho::create([
            'nome_nicho' => $request->nome_nicho,
        ]);

        return response()->json($nicho, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/nichos/{id}",
     *     operationId="getNichoById",
     *     tags={"Nicho"},
     *     summary="Mostrar um nicho específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do nicho",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do nicho",
     *         @OA\JsonContent(ref="#/components/schemas/Nicho")
     *     ),
     *     @OA\Response(response=404, description="Nicho não encontrado")
     * )
     */
    public function show($id)
    {
        $nicho = Nicho::findOrFail($id);
        return response()->json($nicho);
    }

    /**
     * @OA\Put(
     *     path="/api/nichos/{id}",
     *     operationId="updateNicho",
     *     tags={"Nicho"},
     *     summary="Atualizar nicho existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do nicho",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome_nicho"},
     *             @OA\Property(property="nome_nicho", type="string", maxLength=255, example="Nicho Atualizado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nicho atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Nicho")
     *     ),
     *     @OA\Response(response=404, description="Nicho não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome_nicho' => 'required|string|max:255',
        ]);

        $nicho = Nicho::findOrFail($id);
        $nicho->update([
            'nome_nicho' => $request->nome_nicho,
        ]);

        return response()->json($nicho);
    }

    /**
     * @OA\Delete(
     *     path="/api/nichos/{id}",
     *     operationId="deleteNicho",
     *     tags={"Nicho"},
     *     summary="Remover nicho",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do nicho",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Response(response=204, description="Nicho removido com sucesso"),
     *     @OA\Response(response=404, description="Nicho não encontrado")
     * )
     */
    public function destroy($id)
    {
        $nicho = Nicho::findOrFail($id);
        $nicho->delete();

        return response()->json(null, 204);
    }
}
