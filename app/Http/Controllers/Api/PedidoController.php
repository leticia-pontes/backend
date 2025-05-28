<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoStatus;
use App\Models\ConfiguracaoGamificacao;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Pedido",
 *     type="object",
 *     title="Pedido",
 *     required={"id", "data_pedido", "descricao", "valor", "prazo_entrega", "id_empresa"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="data_pedido", type="string", format="date", example="2025-05-22"),
 *     @OA\Property(property="descricao", type="string", example="Desenvolvimento de site"),
 *     @OA\Property(property="valor", type="number", format="float", example=1500.00),
 *     @OA\Property(property="prazo_entrega", type="string", format="date", example="2025-06-22"),
 *     @OA\Property(property="id_empresa", type="integer", example=1),
 *     @OA\Property(
 *         property="status",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"status", "data_status"},
 *             @OA\Property(property="status", type="string", example="aguardando"),
 *             @OA\Property(property="data_status", type="string", format="date-time", example="2025-05-22T14:55:00Z")
 *         )
 *     ),
 *     @OA\Property(property="pagamento", type="object", nullable=true,
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="id_pedido", type="integer", example=1),
 *         @OA\Property(property="status", type="string", example="pago"),
 *         @OA\Property(property="valor_pago", type="number", format="float", example=1500.00),
 *         @OA\Property(property="data_pagamento", type="string", format="date-time", example="2025-05-23T10:00:00Z")
 *     )
 * )
 */
class PedidoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pedidos",
     *     summary="Lista todos os pedidos do usuário autenticado.",
     *     tags={"Pedido"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pedidos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="pedidos", type="array",
     *                 @OA\Items(ref="#/components/schemas/Pedido")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $empresa = auth()->user();

        $pedidos = Pedido::with('status')
            ->where('id_empresa', $empresa->id)
            ->get();

        return response()->json(['pedidos' => $pedidos]);
    }

    /**
     * @OA\Post(
     *     path="/api/pedidos",
     *     summary="Cria um novo pedido.",
     *     tags={"Pedido"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"data_pedido","descricao","valor","prazo_entrega"},
     *             @OA\Property(property="data_pedido", type="string", format="date", example="2025-05-22"),
     *             @OA\Property(property="descricao", type="string", example="Desenvolvimento de site"),
     *             @OA\Property(property="valor", type="number", format="float", example=1500.00),
     *             @OA\Property(property="prazo_entrega", type="string", format="date", example="2025-06-22")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pedido criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Pedido")
     *     ),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_pedido' => 'required|date',
            'descricao' => 'required|string',
            'valor' => 'required|numeric',
            'prazo_entrega' => 'required|date',
        ]);

        $pedido = Pedido::create([
            'data_pedido' => $request->data_pedido,
            'descricao' => $request->descricao,
            'valor' => $request->valor,
            'prazo_entrega' => $request->prazo_entrega,
            'id_empresa' => auth()->id(),
        ]);

        PedidoStatus::create([
            'status' => 'aguardando',
            'data_status' => now(),
            'id_pedido' => $pedido->id,
        ]);

        return response()->json(['pedido' => $pedido], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/pedidos/{id}",
     *     summary="Mostra um pedido específico.",
     *     tags={"Pedido"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do pedido",
     *         @OA\JsonContent(ref="#/components/schemas/Pedido")
     *     ),
     *     @OA\Response(response=403, description="Acesso negado"),
     *     @OA\Response(response=404, description="Pedido não encontrado")
     * )
     */
    public function show($id)
    {
        $pedido = Pedido::with(['status', 'pagamento'])->findOrFail($id);
        $empresa = auth()->user();

        if ($pedido->id_empresa !== $empresa->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        return response()->json($pedido);
    }

    /**
     * @OA\Put(
     *     path="/api/pedidos/{id}",
     *     summary="Atualiza um pedido.",
     *     tags={"Pedido"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="descricao", type="string"),
     *             @OA\Property(property="valor", type="number", format="float"),
     *             @OA\Property(property="prazo_entrega", type="string", format="date"),
     *             @OA\Property(property="progresso", type="string"),
     *             @OA\Property(property="comentarios", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido atualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Pedido")
     *     ),
     *     @OA\Response(response=403, description="Sem permissão para editar esse pedido"),
     *     @OA\Response(response=404, description="Pedido não encontrado")
     * )
     */
    public function update(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        $empresa = auth()->user();

        if ($empresa->id === $pedido->id_empresa) {
            $pedido->update($request->only(['descricao', 'valor', 'prazo_entrega']));
        } else {
            return response()->json(['message' => 'Sem permissão para editar esse pedido.'], 403);
        }

        return response()->json(['pedido' => $pedido]);
    }

    /**
     * @OA\Post(
     *     path="/api/pedidos/{id}/cancelar",
     *     summary="Cancela um pedido.",
     *     tags={"Pedido"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Pedido cancelado com sucesso"),
     *     @OA\Response(response=400, description="Pedido já finalizado"),
     *     @OA\Response(response=403, description="Sem permissão para cancelar"),
     *     @OA\Response(response=404, description="Pedido não encontrado")
     * )
     */
    public function cancelar($id)
    {
        $pedido = Pedido::findOrFail($id);
        $empresa = auth()->user();

        if ($empresa->id !== $pedido->id_empresa) {
            return response()->json(['message' => 'Sem permissão para cancelar.'], 403);
        }

        $statusAtual = $pedido->statusAtual();

        if (in_array($statusAtual->status, ['cancelado', 'concluido'])) {
            return response()->json(['message' => 'Pedido já está finalizado.'], 400);
        }

        PedidoStatus::create([
            'status' => 'cancelado',
            'data_status' => now(),
            'id_pedido' => $pedido->id,
        ]);

        return response()->json(['message' => 'Pedido cancelado com sucesso.']);
    }

    /**
     * @OA\Post(
     *     path="/api/pedidos/{id}/concluir",
     *     summary="Conclui um pedido.",
     *     tags={"Pedido"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Pedido concluído com sucesso"),
     *     @OA\Response(response=400, description="Pedido já finalizado"),
     *     @OA\Response(response=403, description="Sem permissão para concluir"),
     *     @OA\Response(response=404, description="Pedido não encontrado")
     * )
     */
    public function concluir($id)
    {
        $pedido = Pedido::findOrFail($id);
        $empresa = auth()->user();

        if ($empresa->id !== $pedido->id_empresa) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        PedidoStatus::create([
            'status' => 'concluido',
            'data_status' => now(),
            'id_pedido' => $pedido->id,
        ]);

        $pontos = ConfiguracaoGamificacao::getValor('pontos_pedido_concluido', 30);

        if ($pedido->avaliacao && $pedido->avaliacao->nota >= 4) {
            $pontos = ConfiguracaoGamificacao::getValor('pontos_pedido_avaliacao_boa', 50);
        }

        $empresa->pontos += $pontos;

        $pontosParaNivel = ConfiguracaoGamificacao::getValor('pontos_para_subir_nivel', 200);
        $empresa->nivel = floor($empresa->pontos / $pontosParaNivel) + 1;

        $empresa->save();

        return response()->json(['message' => 'Pedido concluído com sucesso.']);
    }
}
