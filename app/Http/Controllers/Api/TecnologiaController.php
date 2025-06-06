<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tecnologia;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Tecnologia",
 *     type="object",
 *     @OA\Property(property="id_tecnologia", type="integer", example=1),
 *     @OA\Property(property="nome_tecnologia", type="string", example="Laravel")
 * )
 */
class TecnologiaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tecnologias",
     *     operationId="getTecnologiasList",
     *     tags={"Tecnologia"},
     *     summary="Listar todas as tecnologias",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tecnologias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Tecnologia")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $tecnologias = Tecnologia::all();
        return response()->json($tecnologias);
    }

    /**
     * @OA\Post(
     *     path="/api/tecnologias",
     *     operationId="createTecnologia",
     *     tags={"Tecnologia"},
     *     summary="Criar nova tecnologia",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome_tecnologia"},
     *             @OA\Property(property="nome_tecnologia", type="string", maxLength=255, example="Laravel")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tecnologia criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Tecnologia")
     *     ),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_tecnologia' => 'required|string|max:255',
        ]);

        $tecnologia = Tecnologia::create([
            'nome_tecnologia' => $request->nome_tecnologia,
        ]);

        return response()->json($tecnologia, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tecnologias/{id}",
     *     operationId="getTecnologiaById",
     *     tags={"Tecnologia"},
     *     summary="Mostrar uma tecnologia específica",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tecnologia",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da tecnologia",
     *         @OA\JsonContent(ref="#/components/schemas/Tecnologia")
     *     ),
     *     @OA\Response(response=404, description="Tecnologia não encontrada")
     * )
     */
    public function show($id)
    {
        $tecnologia = Tecnologia::findOrFail($id);
        return response()->json($tecnologia);
    }

    /**
     * @OA\Put(
     *     path="/api/tecnologias/{id}",
     *     operationId="updateTecnologia",
     *     tags={"Tecnologia"},
     *     summary="Atualizar tecnologia existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tecnologia",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome_tecnologia"},
     *             @OA\Property(property="nome_tecnologia", type="string", maxLength=255, example="Vue.js Atualizado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tecnologia atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Tecnologia")
     *     ),
     *     @OA\Response(response=404, description="Tecnologia não encontrada"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome_tecnologia' => 'required|string|max:255',
        ]);

        $tecnologia = Tecnologia::findOrFail($id);
        $tecnologia->update([
            'nome_tecnologia' => $request->nome_tecnologia,
        ]);

        return response()->json($tecnologia);
    }

    /**
     * @OA\Delete(
     *     path="/api/tecnologias/{id}",
     *     operationId="deleteTecnologia",
     *     tags={"Tecnologia"},
     *     summary="Remover tecnologia",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tecnologia",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Response(response=204, description="Tecnologia removida com sucesso"),
     *     @OA\Response(response=404, description="Tecnologia não encontrada")
     * )
     */
    public function destroy($id)
    {
        $tecnologia = Tecnologia::findOrFail($id);
        $tecnologia->delete();

        return response()->json(null, 204);
    }
}
