<?php

namespace App\Http\Controllers\Api; // <-- Namespace atualizado

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Empresa;
use App\Models\PedidoStatus;
use Illuminate\Http\Request;
use App\Services\GamificationService;
use App\Models\ConfiguracaoGamificacao;
use App\Enums\PedidoStatusEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA; // Importe as anotações OA

class PedidoController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * @OA\Get(
     * path="/api/pedidos",
     * operationId="getPedidosList",
     * tags={"Pedidos"},
     * summary="Obtém a lista de todos os pedidos",
     * description="Retorna uma lista paginada de pedidos com seus contratantes, desenvolvedores e status atuais.",
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Número da página para paginação",
     * required=false,
     * @OA\Schema(
     * type="integer",
     * format="int32",
     * default=1
     * )
     * ),
     * @OA\Parameter(
     * name="per_page",
     * in="query",
     * description="Número de itens por página",
     * required=false,
     * @OA\Schema(
     * type="integer",
     * format="int32",
     * default=10
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Operação bem-sucedida",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(
     * @OA\Property(property="id_pedido", type="integer", example=1),
     * @OA\Property(property="titulo", type="string", example="Desenvolvimento de E-commerce"),
     * @OA\Property(property="descricao", type="string", example="Criar uma loja virtual completa."),
     * @OA\Property(property="valor_estimado", type="number", format="float", example=5000.00),
     * @OA\Property(property="data_prazo", type="string", format="date", example="2025-12-31"),
     * @OA\Property(property="data_pedido", type="string", format="date", example="2025-05-20"),
     * @OA\Property(property="current_status", type="string", example="pendente"),
     * @OA\Property(property="contratante", type="object", example={"id_empresa": 1, "nome": "Empresa Contratante A"}),
     * @OA\Property(property="desenvolvedora", type="object", example={"id_empresa": 2, "nome": "Empresa Desenvolvedora B"})
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * ),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function index()
    {
        $pedidos = Pedido::with([
                            'contratante',
                            'desenvolvedora',
                            'statusHistorico' => function($query) {
                                $query->latest('data_status')->take(1);
                            }
                        ])
                         ->paginate(10);

        $pedidos->getCollection()->transform(function ($pedido) {
            $pedido->current_status = $pedido->currentStatus ? $pedido->currentStatus->status : null;
            unset($pedido->statusHistorico);
            return $pedido;
        });

        return response()->json($pedidos);
    }

    /**
     * @OA\Post(
     * path="/api/pedidos",
     * operationId="createPedido",
     * tags={"Pedidos"},
     * summary="Cria um novo pedido",
     * description="Cria um novo pedido e associa distintivos à empresa contratante.",
     * @OA\RequestBody(
     * required=true,
     * description="Dados do pedido para criação",
     * @OA\JsonContent(
     * required={"titulo", "descricao", "valor_estimado", "id_empresa_contratante"},
     * @OA\Property(property="titulo", type="string", example="Desenvolvimento de Landing Page"),
     * @OA\Property(property="descricao", type="string", example="Criar uma landing page para nova campanha de marketing."),
     * @OA\Property(property="valor_estimado", type="number", format="float", example=1500.00),
     * @OA\Property(property="data_prazo", type="string", format="date", nullable=true, example="2025-06-30"),
     * @OA\Property(property="id_empresa_contratante", type="integer", example=1, description="ID da empresa que está contratando o pedido.")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Pedido criado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Pedido criado com sucesso!"),
     * @OA\Property(property="pedido", type="object", example={"id_pedido": 1, "titulo": "Desenvolvimento de Landing Page", "status": "pendente"})
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Erro de validação"
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * ),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'valor_estimado' => 'required|numeric|min:0',
            'data_prazo' => 'nullable|date',
            'id_empresa_contratante' => 'required|exists:empresas,id_empresa',
        ]);

        $validatedData['data_pedido'] = now();

        $pedido = Pedido::create($validatedData);

        PedidoStatus::create([
            'id_pedido' => $pedido->id_pedido,
            'status' => PedidoStatusEnum::Pendente->value,
            'data_status' => now(),
        ]);

        $contratante = Empresa::find($validatedData['id_empresa_contratante']);

        if ($contratante) {
            $newBadges = $this->gamificationService->awardBadges($contratante);

            if (!empty($newBadges)) {
                Log::info("Distintivos concedidos à contratante {$contratante->id_empresa}: " . implode(', ', $newBadges));
            }
        }

        return response()->json(['message' => 'Pedido criado com sucesso!', 'pedido' => $pedido], 201);
    }

    /**
     * @OA\Get(
     * path="/api/pedidos/{id}",
     * operationId="getPedidoById",
     * tags={"Pedidos"},
     * summary="Obtém detalhes de um pedido específico",
     * description="Retorna os detalhes de um pedido pelo seu ID.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do pedido",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * format="int64"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Operação bem-sucedida",
     * @OA\JsonContent(
     * @OA\Property(property="id_pedido", type="integer", example=1),
     * @OA\Property(property="titulo", type="string", example="Desenvolvimento de E-commerce"),
     * @OA\Property(property="descricao", type="string", example="Criar uma loja virtual completa."),
     * @OA\Property(property="valor_estimado", type="number", format="float", example=5000.00),
     * @OA\Property(property="data_prazo", type="string", format="date", example="2025-12-31"),
     * @OA\Property(property="data_pedido", type="string", format="date", example="2025-05-20"),
     * @OA\Property(property="current_status", type="string", example="em_andamento"),
     * @OA\Property(property="contratante", type="object", example={"id_empresa": 1, "nome": "Empresa Contratante A"}),
     * @OA\Property(property="desenvolvedora", type="object", example={"id_empresa": 2, "nome": "Empresa Desenvolvedora B"})
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Pedido não encontrado"
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * ),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function show(Pedido $pedido)
    {
        $pedido->load(['contratante', 'desenvolvedora', 'statusHistorico']);
        $pedido->current_status = $pedido->currentStatus ? $pedido->currentStatus->status : null;

        return response()->json($pedido);
    }

    /**
     * @OA\Put(
     * path="/api/pedidos/{id}",
     * operationId="updatePedido",
     * tags={"Pedidos"},
     * summary="Atualiza um pedido existente",
     * description="Atualiza os dados de um pedido específico. Use PATCH para atualização parcial.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do pedido a ser atualizado",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * format="int64"
     * )
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Dados do pedido para atualização",
     * @OA\JsonContent(
     * @OA\Property(property="titulo", type="string", example="Novo Título do Pedido"),
     * @OA\Property(property="descricao", type="string", example="Nova descrição do projeto atualizado."),
     * @OA\Property(property="valor_estimado", type="number", format="float", example=2000.00),
     * @OA\Property(property="data_prazo", type="string", format="date", nullable=true, example="2026-01-15"),
     * @OA\Property(property="id_empresa_desenvolvedora", type="integer", nullable=true, example=2, description="ID da empresa desenvolvedora atribuída.")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Pedido atualizado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Pedido atualizado com sucesso!"),
     * @OA\Property(property="pedido", type="object", example={"id_pedido": 1, "titulo": "Novo Título do Pedido"})
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Pedido não encontrado"
     * ),
     * @OA\Response(
     * response=422,
     * description="Erro de validação"
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * ),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function update(Request $request, Pedido $pedido)
    {
        $validatedData = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descricao' => 'sometimes|string',
            'valor_estimado' => 'sometimes|numeric|min:0',
            'data_prazo' => 'sometimes|nullable|date',
            'id_empresa_desenvolvedora' => 'sometimes|nullable|exists:empresas,id_empresa',
        ]);

        $pedido->update($validatedData);

        return response()->json(['message' => 'Pedido atualizado com sucesso!', 'pedido' => $pedido]);
    }

    /**
     * @OA\Patch(
     * path="/api/pedidos/{id}/aceitar",
     * operationId="aceitarPedido",
     * tags={"Pedidos - Status"},
     * summary="Aceita um pedido",
     * description="Marca um pedido como 'aceito' pela empresa desenvolvedora autenticada. Só pode ser aceito se o status atual for 'pendente'.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do pedido a ser aceito",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * format="int64"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Pedido aceito com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Pedido aceito com sucesso!"),
     * @OA\Property(property="pedido", type="object", example={"id_pedido": 1, "status": "aceito", "id_empresa_desenvolvedora": 2})
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Pedido não pode ser aceito neste status ou não tem desenvolvedora atribuída."
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado ou empresa desenvolvedora não encontrada."
     * ),
     * @OA\Response(
     * response=404,
     * description="Pedido não encontrado."
     * ),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function aceitar(Pedido $pedido)
    {
        $currentStatus = $pedido->currentStatus ? $pedido->currentStatus->status : null;

        if ($currentStatus !== PedidoStatusEnum::Pendente->value) {
            return response()->json(['message' => 'Pedido não pode ser aceito neste status.'], 400);
        }

        $desenvolvedor = Empresa::find(Auth::id());

        if (!$desenvolvedor) {
            return response()->json(['message' => 'Empresa desenvolvedora não autenticada.'], 401);
        }

        $pedido->id_empresa_desenvolvedora = $desenvolvedor->id_empresa;
        $pedido->save();

        PedidoStatus::create([
            'id_pedido' => $pedido->id_pedido,
            'status' => PedidoStatusEnum::Aceito->value,
            'data_status' => now(),
        ]);

        $pedido->refresh();

        $pontosAceitar = ConfiguracaoGamificacao::getValor('pontos_aceitar_pedido', 5);
        $this->gamificationService->addPoints($desenvolvedor, $pontosAceitar);
        $newBadges = $this->gamificationService->awardBadges($desenvolvedor);

        return response()->json(['message' => 'Pedido aceito com sucesso!', 'pedido' => $pedido]);
    }

    /**
     * @OA\Patch(
     * path="/api/pedidos/{id}/em-andamento",
     * operationId="emAndamentoPedido",
     * tags={"Pedidos - Status"},
     * summary="Marca um pedido como em andamento",
     * description="Marca um pedido como 'em_andamento'. A transição é permitida a partir de 'aceito' ou 'aguardando'.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do pedido",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * format="int64"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Pedido marcado como 'em andamento' com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Pedido marcado como 'em andamento' com sucesso!"),
     * @OA\Property(property="pedido", type="object", example={"id_pedido": 1, "status": "em_andamento"})
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Pedido não pode ir para 'em andamento' neste status ou não tem desenvolvedora atribuída."
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * ),
     * @OA\Response(
     * response=404,
     * description="Pedido não encontrado"
     * ),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function emAndamento(Pedido $pedido)
    {
        $currentStatus = $pedido->currentStatus ? $pedido->currentStatus->status : null;

        if (!in_array($currentStatus, [PedidoStatusEnum::Aceito->value, PedidoStatusEnum::Aguardando->value])) {
            return response()->json(['message' => 'Pedido não pode ir para "em andamento" neste status.'], 400);
        }

        if (!$pedido->id_empresa_desenvolvedora) {
             return response()->json(['message' => 'Este pedido não possui uma empresa desenvolvedora atribuída.'], 400);
        }

        PedidoStatus::create([
            'id_pedido' => $pedido->id_pedido,
            'status' => PedidoStatusEnum::EmAndamento->value,
            'data_status' => now(),
        ]);

        $pedido->refresh();

        return response()->json(['message' => 'Pedido marcado como "em andamento" com sucesso!', 'pedido' => $pedido]);
    }

    /**
     * @OA\Patch(
     * path="/api/pedidos/{id}/aguardar",
     * operationId="aguardarPedido",
     * tags={"Pedidos - Status"},
     * summary="Marca um pedido como aguardando",
     * description="Marca um pedido como 'aguardando'. A transição é permitida a partir de 'em_andamento'.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do pedido",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * format="int64"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Pedido marcado como 'aguardando' com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Pedido marcado como 'aguardando' com sucesso!"),
     * @OA\Property(property="pedido", type="object", example={"id_pedido": 1, "status": "aguardando"})
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Pedido não pode ser marcado como 'aguardando' neste status ou não tem desenvolvedora atribuída."
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * ),
     * @OA\Response(
     * response=404,
     * description="Pedido não encontrado"
     * ),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function aguardar(Pedido $pedido)
    {
        $currentStatus = $pedido->currentStatus ? $pedido->currentStatus->status : null;

        if ($currentStatus !== PedidoStatusEnum::EmAndamento->value) {
            return response()->json(['message' => 'Pedido não pode ser marcado como "aguardando" neste status.'], 400);
        }

        if (!$pedido->id_empresa_desenvolvedora) {
             return response()->json(['message' => 'Este pedido não possui uma empresa desenvolvedora atribuída.'], 400);
        }

        PedidoStatus::create([
            'id_pedido' => $pedido->id_pedido,
            'status' => PedidoStatusEnum::Aguardando->value,
            'data_status' => now(),
        ]);

        $pedido->refresh();

        return response()->json(['message' => 'Pedido marcado como "aguardando" com sucesso!', 'pedido' => $pedido]);
    }

    /**
     * @OA\Patch(
     * path="/api/pedidos/{id}/concluir",
     * operationId="concluirPedido",
     * tags={"Pedidos - Status"},
     * summary="Conclui um pedido",
     * description="Marca um pedido como 'concluido' e concede pontos e distintivos à empresa desenvolvedora. A transição é permitida a partir de 'em_andamento' ou 'aguardando'.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do pedido a ser concluído",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * format="int64"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Pedido concluído com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Pedido concluído com sucesso! Distintivos conquistados!"),
     * @OA\Property(property="new_badges", type="array", @OA\Items(type="string"), example={"Conquistador de Projetos"}),
     * @OA\Property(property="empresa_pontos", type="integer", example=150),
     * @OA\Property(property="empresa_nivel", type="integer", example=1)
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Pedido não pode ser concluído neste status ou não tem desenvolvedora atribuída."
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * ),
     * @OA\Response(
     * response=404,
     * description="Pedido não encontrado ou empresa desenvolvedora não encontrada."
     * ),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function concluir(Request $request, Pedido $pedido)
    {
        $currentStatus = $pedido->currentStatus ? $pedido->currentStatus->status : null;

        if (!in_array($currentStatus, [PedidoStatusEnum::EmAndamento->value, PedidoStatusEnum::Aguardando->value])) {
            return response()->json(['message' => 'Pedido não pode ser concluído neste status.'], 400);
        }

        if (!$pedido->id_empresa_desenvolvedora) {
             return response()->json(['message' => 'Este pedido não possui uma empresa desenvolvedora atribuída.'], 400);
        }

        $desenvolvedor = Empresa::find($pedido->id_empresa_desenvolvedora);

        if (!$desenvolvedor) {
            return response()->json(['message' => 'Empresa desenvolvedora não encontrada.'], 404);
        }

        PedidoStatus::create([
            'id_pedido' => $pedido->id_pedido,
            'status' => PedidoStatusEnum::Concluido->value,
            'data_status' => now(),
        ]);

        $pedido->refresh();

        $pontosConclusao = ConfiguracaoGamificacao::getValor('pontos_pedido_concluido', 30);
        $this->gamificationService->addPoints($desenvolvedor, $pontosConclusao);

        $newBadges = $this->gamificationService->awardBadges($desenvolvedor);

        $responseMessage = 'Pedido concluído com sucesso!';
        if (!empty($newBadges)) {
            $responseMessage .= ' Distintivos conquistados!';
            Log::info("Distintivos concedidos à desenvolvedora {$desenvolvedor->id_empresa}: " . implode(', ', $newBadges));
        }

        return response()->json([
            'message' => $responseMessage,
            'new_badges' => $newBadges,
            'empresa_pontos' => $desenvolvedor->pontos,
            'empresa_nivel' => $desenvolvedor->nivel
        ], 200);
    }

    /**
     * @OA\Patch(
     * path="/api/pedidos/{id}/cancelar",
     * operationId="cancelarPedido",
     * tags={"Pedidos - Status"},
     * summary="Cancela um pedido",
     * description="Marca um pedido como 'cancelado' e pode remover pontos do desenvolvedor. Não pode ser cancelado se já estiver 'concluido' ou 'cancelado'.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do pedido a ser cancelado",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * format="int64"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Pedido cancelado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Pedido cancelado com sucesso!"),
     * @OA\Property(property="pedido", type="object", example={"id_pedido": 1, "status": "cancelado"})
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Pedido não pode ser cancelado neste status."
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * ),
     * @OA\Response(
     * response=404,
     * description="Pedido não encontrado."
     * ),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function cancelar(Pedido $pedido)
    {
        $currentStatus = $pedido->currentStatus ? $pedido->currentStatus->status : null;

        if (in_array($currentStatus, [PedidoStatusEnum::Concluido->value, PedidoStatusEnum::Cancelado->value])) {
            return response()->json(['message' => 'Pedido não pode ser cancelado neste status.'], 400);
        }

        PedidoStatus::create([
            'id_pedido' => $pedido->id_pedido,
            'status' => PedidoStatusEnum::Cancelado->value,
            'data_status' => now(),
        ]);

        $pedido->refresh();

        if ($pedido->id_empresa_desenvolvedora) {
            $desenvolvedor = Empresa::find($pedido->id_empresa_desenvolvedora);
            if ($desenvolvedor) { // Adicionado check para desenvolvedor, para evitar erro se não encontrar
                $pontosPerdidos = ConfiguracaoGamificacao::getValor('pontos_cancelamento_pedido', 10);
                $this->gamificationService->removePoints($desenvolvedor, $pontosPerdidos);
            }
        }

        return response()->json(['message' => 'Pedido cancelado com sucesso!', 'pedido' => $pedido]);
    }

    /**
     * @OA\Delete(
     * path="/api/pedidos/{id}",
     * operationId="deletePedido",
     * tags={"Pedidos"},
     * summary="Remove um pedido",
     * description="Deleta um pedido específico do armazenamento.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do pedido a ser deletado",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * format="int64"
     * )
     * ),
     * @OA\Response(
     * response=204,
     * description="Pedido deletado com sucesso (No Content)"
     * ),
     * @OA\Response(
     * response=404,
     * description="Pedido não encontrado"
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * ),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        return response()->json(['message' => 'Pedido deletado com sucesso!'], 204);
    }
}
