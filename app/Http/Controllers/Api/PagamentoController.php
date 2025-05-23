<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pagamento;

/**
 * @OA\Schema(
 *     schema="Pagamento",
 *     type="object",
 *     title="Pagamento",
 *     required={"valor", "data_pagamento", "metodo_pagamento", "status", "id_pedido"},
 *     @OA\Property(property="id", type="integer", readOnly=true),
 *     @OA\Property(property="valor", type="number", format="float", example=100.50),
 *     @OA\Property(property="data_pagamento", type="string", format="date", example="2025-05-23"),
 *     @OA\Property(property="metodo_pagamento", type="string", example="cartao"),
 *     @OA\Property(property="status", type="string", example="pago"),
 *     @OA\Property(property="id_pedido", type="integer", example=1),
 * )
 */
class PagamentoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pagamentos",
     *     tags={"Pagamentos"},
     *     summary="Listar todos os pagamentos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pagamentos retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Pagamento")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $pagamentos = Pagamento::with('pedido')->get();
        return response()->json($pagamentos);
    }

    /**
     * @OA\Get(
     *     path="/api/pagamentos/{id}",
     *     tags={"Pagamentos"},
     *     summary="Mostrar detalhes de um pagamento",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pagamento",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pagamento encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Pagamento")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pagamento não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $pagamento = Pagamento::with('pedido')->find($id);

        if (!$pagamento) {
            return response()->json(['message' => 'Pagamento não encontrado'], 404);
        }

        return response()->json($pagamento);
    }

    /**
     * @OA\Post(
     *     path="/api/pagamentos",
     *     tags={"Pagamentos"},
     *     summary="Criar um novo pagamento",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"valor","data_pagamento","metodo_pagamento","status","id_pedido"},
     *             @OA\Property(property="valor", type="number", format="float", example=100.50),
     *             @OA\Property(property="data_pagamento", type="string", format="date", example="2025-05-23"),
     *             @OA\Property(property="metodo_pagamento", type="string", example="cartão de crédito"),
     *             @OA\Property(property="status", type="string", example="confirmado"),
     *             @OA\Property(property="id_pedido", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pagamento criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Pagamento")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'valor' => 'required|numeric',
            'data_pagamento' => 'required|date',
            'metodo_pagamento' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'id_pedido' => 'required|integer|exists:pedidos,id',
        ]);

        $pagamento = Pagamento::create($validated);

        return response()->json($pagamento, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/pagamentos/{id}",
     *     tags={"Pagamentos"},
     *     summary="Atualizar um pagamento existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pagamento",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="valor", type="number", format="float"),
     *             @OA\Property(property="data_pagamento", type="string", format="date"),
     *             @OA\Property(property="metodo_pagamento", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="id_pedido", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pagamento atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Pagamento")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pagamento não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $pagamento = Pagamento::find($id);

        if (!$pagamento) {
            return response()->json(['message' => 'Pagamento não encontrado'], 404);
        }

        $validated = $request->validate([
            'valor' => 'sometimes|numeric',
            'data_pagamento' => 'sometimes|date',
            'metodo_pagamento' => 'sometimes|string|max:50',
            'status' => 'sometimes|string|max:50',
            'id_pedido' => 'sometimes|integer|exists:pedidos,id',
        ]);

        $pagamento->update($validated);

        return response()->json($pagamento);
    }

    /**
     * @OA\Delete(
     *     path="/api/pagamentos/{id}",
     *     tags={"Pagamentos"},
     *     summary="Excluir um pagamento",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pagamento",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pagamento deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pagamento deletado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pagamento não encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $pagamento = Pagamento::find($id);

        if (!$pagamento) {
            return response()->json(['message' => 'Pagamento não encontrado'], 404);
        }

        $pagamento->delete();

        return response()->json(['message' => 'Pagamento deletado com sucesso']);
    }
}
