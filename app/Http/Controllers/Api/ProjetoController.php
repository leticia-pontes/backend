<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Projeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProjetoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * @OA\Get(
     *     path="/api/projetos",
     *     tags={"Projetos"},
     *     summary="Listar todos os projetos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de projetos",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Projeto"))
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Projeto::with('empresa')->get());
    }

    /**
     * @OA\Get(
     *     path="/api/projetos/{id}",
     *     tags={"Projetos"},
     *     summary="Exibir um projeto específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Projeto encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Projeto")
     *     ),
     *     @OA\Response(response=404, description="Projeto não encontrado")
     * )
     */
    public function show(Projeto $projeto)
    {
        return response()->json($projeto->load('empresa'));
    }

    /**
     * @OA\Post(
     *     path="/api/projetos",
     *     tags={"Projetos"},
     *     summary="Criar um novo projeto",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nome_projeto", "descricao"},
     *                 @OA\Property(property="nome_projeto", type="string", description="Nome do projeto"),
     *                 @OA\Property(property="descricao", type="string", description="Descrição detalhada do projeto"),
     *                 @OA\Property(property="data_inicio", type="string", format="date", description="Data de início do projeto"),
     *                 @OA\Property(property="data_fim", type="string", format="date", description="Data de término do projeto"),
     *                 @OA\Property(property="status", type="string", description="Status do projeto"),
     *                 @OA\Property(property="url_projeto", type="string", format="url", description="URL do projeto"),
     *                 @OA\Property(
     *                     property="imagem_destaque",
     *                     type="string",
     *                     format="binary",
     *                     description="Imagem de destaque (opcional)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Projeto criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Projeto")
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_projeto' => 'required|string|max:255',
            'descricao' => 'required|string',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'status' => 'nullable|string|max:50',
            'url_projeto' => 'nullable|url',
            'imagem_destaque' => 'nullable|image|max:10240'
        ]);

        $idEmpresaAutenticada = Auth::check() ? Auth::user()->id_empresa : null;

        if (!$idEmpresaAutenticada) {
            return response()->json(['message' => 'Usuário não autenticado ou empresa não vinculada.'], 401);
        }

        $validated['id_empresa'] = $idEmpresaAutenticada;

        if ($request->hasFile('imagem_destaque')) {
            $validated['imagem_destaque_url'] = $request->file('imagem_destaque')->store('projetos/imagens');
        }

        $projeto = Projeto::create($validated);

        return response()->json($projeto, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/projetos/{id}",
     *     tags={"Projetos"},
     *     summary="Atualizar um projeto",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="nome_projeto", type="string"),
     *                 @OA\Property(property="descricao", type="string"),
     *                 @OA\Property(property="data_inicio", type="string", format="date"),
     *                 @OA\Property(property="data_fim", type="string", format="date"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="url_projeto", type="string"),
     *                 @OA\Property(property="imagem_destaque", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Projeto atualizado com sucesso", @OA\JsonContent(ref="#/components/schemas/Projeto")),
     *     @OA\Response(response=403, description="Não autorizado"),
     *     @OA\Response(response=404, description="Projeto não encontrado")
     * )
     */
    public function update(Request $request, Projeto $projeto)
    {
        if (Auth::check() && $projeto->id_empresa !== Auth::user()->id_empresa) {
            return response()->json(['message' => 'Você não tem permissão para atualizar este projeto.'], 403);
        }

        $validated = $request->validate([
            'nome_projeto' => 'sometimes|string|max:255',
            'descricao' => 'sometimes|string',
            'data_inicio' => 'sometimes|date',
            'data_fim' => 'sometimes|date',
            'status' => 'sometimes|string|max:50',
            'url_projeto' => 'sometimes|url',
            'imagem_destaque' => 'sometimes|image|max:10240',
        ]);

        if ($request->hasFile('imagem_destaque')) {
            if ($projeto->imagem_destaque_url) {
                Storage::delete($projeto->imagem_destaque_url);
            }
            $validated['imagem_destaque_url'] = $request->file('imagem_destaque')->store('projetos/imagens');
        }

        $projeto->update($validated);

        return response()->json($projeto);
    }

    /**
     * @OA\Delete(
     *     path="/api/projetos/{id}",
     *     tags={"Projetos"},
     *     summary="Deletar um projeto",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Projeto removido com sucesso"),
     *     @OA\Response(response=403, description="Não autorizado"),
     *     @OA\Response(response=404, description="Projeto não encontrado")
     * )
     */
    public function destroy(Projeto $projeto)
    {
        if (Auth::check() && $projeto->id_empresa !== Auth::user()->id_empresa) {
            return response()->json(['message' => 'Você não tem permissão para remover este projeto.'], 403);
        }

        if ($projeto->imagem_destaque_url) {
            Storage::delete($projeto->imagem_destaque_url);
        }

        $projeto->delete();

        return response()->noContent();
    }
}
