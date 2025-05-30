<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * @OA\Tag(
 * name="Avaliações",
 * description="Gerencia as avaliações feitas pelas empresas"
 * )
 */
class AvaliacaoController extends Controller
{
    /**
     * Construtor para aplicar middlewares.
     */
    public function __construct()
    {
        // Aplica o middleware 'auth:sanctum' SOMENTE para 'store', 'update' e 'destroy'
        // Deixa os métodos 'index' e 'show' públicos.
        $this->middleware('auth:sanctum')->except(['index', 'show']);

        // Aplica a política 'can:update,avaliacao' SOMENTE para 'update' e 'destroy'
        // Isso requer que você tenha uma Policy para a Avaliacao configurada.
        $this->middleware('can:update,avaliacao')->only(['update', 'destroy']);
    }

    /**
     * @OA\Get(
     * path="/api/avaliacoes",
     * tags={"Avaliações"},
     * summary="Listar todas as avaliações",
     * @OA\Response(
     * response=200,
     * description="Lista de avaliações",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Avaliacao"))
     * )
     * )
     */
    public function index()
    {
        return response()->json(Avaliacao::all());
    }

    /**
     * @OA\Get(
     * path="/api/avaliacoes/{id}",
     * tags={"Avaliações"},
     * summary="Exibir uma avaliação específica",
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", format="int64")),
     * @OA\Response(
     * response=200,
     * description="Avaliação encontrada",
     * @OA\JsonContent(ref="#/components/schemas/Avaliacao")
     * ),
     * @OA\Response(response=404, description="Avaliação não encontrada")
     * )
     */
    public function show($id)
    {
        $avaliacao = Avaliacao::find($id);

        if (!$avaliacao) {
            return response()->json(['message' => 'Avaliação não encontrada.'], 404);
        }

        return response()->json($avaliacao);
    }

    /**
     * @OA\Post(
     * path="/api/avaliacoes",
     * tags={"Avaliações"},
     * summary="Criar uma nova avaliação",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"nota", "comentario", "id_empresa"},
     * @OA\Property(property="nota", type="integer", example=5, description="Nota da avaliação (1-5)"),
     * @OA\Property(property="comentario", type="string", example="Empresa excelente, serviço de qualidade!"),
     * @OA\Property(property="id_empresa", type="integer", example=1, description="ID da empresa que está sendo avaliada")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Avaliação criada com sucesso",
     * @OA\JsonContent(ref="#/components/schemas/Avaliacao")
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * ),
     * @OA\Response(
     * response=422,
     * description="Erro de validação",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string"),
     * @OA\Property(property="errors", type="object")
     * )
     * )
     * )
     */
    public function store(Request $request)
    {
        // 1. Obter o ID da empresa avaliadora (autenticada)
        $empresaAvaliador = Auth::user(); // Pega o objeto Empresa autenticado
        $idEmpresaAvaliador = $empresaAvaliador->id_empresa;

        // 2. Validação dos dados de entrada
        $validatedData = $request->validate([
            'id_empresa_avaliado' => 'required|exists:empresas,id_empresa', // Verifica se a empresa avaliada existe
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
        ]);

        // 3. Verificação de auto-avaliação
        if ($idEmpresaAvaliador === $validatedData['id_empresa_avaliado']) {
            return response()->json(['message' => 'Você não pode avaliar a si mesmo.'], 400);
        }

        // 4. Verificação de avaliação duplicada (opcional, dependendo da sua regra de negócio)
        $exists = Avaliacao::where('id_empresa_avaliador', $idEmpresaAvaliador)
                           ->where('id_empresa_avaliado', $validatedData['id_empresa_avaliado'])
                           ->exists();

        if ($exists) {
            return response()->json(['message' => 'Você já avaliou esta empresa.'], 409); // 409 Conflict
        }

        // 5. Criar a avaliação
        $avaliacao = Avaliacao::create([
            'id_empresa_avaliador' => $idEmpresaAvaliador,
            'id_empresa_avaliado' => $validatedData['id_empresa_avaliado'],
            'nota' => $validatedData['nota'],
            'comentario' => $validatedData['comentario'],
            'data_avaliacao' => Carbon::now()->toDateString(), // Usa Carbon para garantir formato de data
        ]);

        return response()->json($avaliacao, 201); // Retorna a avaliação criada com status 201
    }

    /**
     * @OA\Put(
     * path="/api/avaliacoes/{id}",
     * tags={"Avaliações"},
     * summary="Atualizar uma avaliação",
     * security={{"sanctum": {}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", format="int64")),
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="nota", type="integer", example=4),
     * @OA\Property(property="comentario", type="string", example="Comentário atualizado para 'Serviço ótimo!'")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Avaliação atualizada",
     * @OA\JsonContent(ref="#/components/schemas/Avaliacao")
     * ),
     * @OA\Response(response=401, description="Não autenticado"),
     * @OA\Response(response=403, description="Não autorizado a atualizar esta avaliação"),
     * @OA\Response(response=404, description="Avaliação não encontrada"),
     * @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function update(Request $request, Avaliacao $avaliacao)
    {
        $validated = $request->validate([
            'nota' => 'sometimes|required|integer|min:1|max:5',
            'comentario' => 'sometimes|required|string|max:500',
        ]);

        $avaliacao->update($validated);

        return response()->json($avaliacao);
    }

    /**
     * @OA\Delete(
     * path="/api/avaliacoes/{id}",
     * tags={"Avaliações"},
     * summary="Remover uma avaliação",
     * security={{"sanctum": {}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", format="int64")),
     * @OA\Response(response=204, description="Avaliação removida com sucesso"),
     * @OA\Response(response=401, description="Não autenticado"),
     * @OA\Response(response=403, description="Não autorizado a remover esta avaliação"),
     * @OA\Response(response=404, description="Avaliação não encontrada")
     * )
     */
    public function destroy(Avaliacao $avaliacao)
    {
        $avaliacao->delete();

        return response()->noContent();
    }
}
