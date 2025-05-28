<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Projeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * @OA\Tag(
 * name="Projetos",
 * description="Gerencia os projetos das empresas"
 * )
 */
class ProjetoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * @OA\Get(
     * path="/api/projetos",
     * tags={"Projetos"},
     * summary="Listar todos os projetos",
     * @OA\Response(
     * response=200,
     * description="Lista de projetos",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Projeto")
     * )
     * )
     * )
     */
    public function index()
    {
        return response()->json(Projeto::all());
    }

    /**
     * @OA\Get(
     * path="/api/projetos/{id}",
     * tags={"Projetos"},
     * summary="Exibir um projeto específico",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer", format="int64", description="ID do Projeto")
     * ),
     * @OA\Response(
     * response=200,
     * description="Projeto encontrado",
     * @OA\JsonContent(ref="#/components/schemas/Projeto")
     * ),
     * @OA\Response(response=404, description="Projeto não encontrado")
     * )
     */
    public function show(Projeto $projeto)
    {
        return response()->json($projeto);
    }

    /**
     * @OA\Post(
     * path="/api/projetos",
     * tags={"Projetos"},
     * summary="Criar um novo projeto",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * description="Dados para criar um novo projeto, incluindo o arquivo binário",
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"arquivo", "nome_arquivo", "descricao", "id_empresa"},
     * @OA\Property(
     * property="arquivo",
     * type="string",
     * format="binary",
     * description="O arquivo binário do projeto (ex: PDF, imagem). Use form-data."
     * ),
     * @OA\Property(property="nome_arquivo", type="string", description="Nome original do arquivo enviado"),
     * @OA\Property(property="descricao", type="string", description="Descrição detalhada do projeto"),
     * @OA\Property(property="versao", type="string", description="Versão do projeto (opcional)"),
     * @OA\Property(property="ativo", type="boolean", description="Status de ativação do projeto (opcional, padrão true)"),
     * @OA\Property(property="id_empresa", type="integer", description="ID da empresa à qual o projeto pertence")
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Projeto criado com sucesso",
     * @OA\JsonContent(ref="#/components/schemas/Projeto")
     * ),
     * @OA\Response(response=401, description="Não autenticado"),
     * @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'arquivo' => 'required|file|max:10240',
            'nome_arquivo' => 'required|string|max:255',
            'descricao' => 'required|string|max:1000',
            'versao' => 'nullable|string|max:20',
            'ativo' => 'boolean',
            'id_empresa' => 'required|integer|exists:empresas,id_empresa',
        ]);

        $idEmpresaAutenticada = Auth::check() ? Auth::user()->id_empresa : null;

        if ($idEmpresaAutenticada !== null && $validated['id_empresa'] !== $idEmpresaAutenticada) {
            return response()->json(['message' => 'Você não tem permissão para criar um projeto para esta empresa.'], 403);
        }

        $caminhoArquivo = null;
        if ($request->hasFile('arquivo')) {
            $caminhoArquivo = $request->file('arquivo')->store('projetos');
        }

        $projeto = Projeto::create([
            'arquivo' => $caminhoArquivo,
            'nome_arquivo' => $validated['nome_arquivo'],
            'descricao' => $validated['descricao'],
            'data_criacao' => Carbon::now()->toDateString(),
            'versao' => $validated['versao'] ?? '1.0',
            'ativo' => $validated['ativo'] ?? true,
            'id_empresa' => $validated['id_empresa'],
        ]);

        return response()->json($projeto, 201);
    }

    /**
     * @OA\Put(
     * path="/api/projetos/{id}",
     * tags={"Projetos"},
     * summary="Atualizar um projeto existente",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer", format="int64", description="ID do Projeto")
     * ),
     * @OA\RequestBody(
     * description="Dados para atualizar um projeto (todos os campos são opcionais)",
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(
     * property="arquivo",
     * type="string",
     * format="binary",
     * description="Um novo arquivo binário do projeto (opcional, para substituição)."
     * ),
     * @OA\Property(property="nome_arquivo", type="string", description="Nome atualizado do arquivo"),
     * @OA\Property(property="descricao", type="string", description="Descrição atualizada do projeto."),
     * @OA\Property(property="versao", type="string", description="Nova versão do projeto"),
     * @OA\Property(property="ativo", type="boolean", description="Novo status de ativação")
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Projeto atualizado",
     * @OA\JsonContent(ref="#/components/schemas/Projeto")
     * ),
     * @OA\Response(response=401, description="Não autenticado"),
     * @OA\Response(response=403, description="Não autorizado a atualizar este projeto"),
     * @OA\Response(response=404, description="Projeto não encontrado"),
     * @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function update(Request $request, Projeto $projeto)
    {
        if (Auth::check() && $projeto->id_empresa !== Auth::user()->id_empresa) {
            return response()->json(['message' => 'Você não tem permissão para atualizar este projeto.'], 403);
        }

        $validated = $request->validate([
            'arquivo' => 'sometimes|file|max:10240',
            'nome_arquivo' => 'sometimes|string|max:255',
            'descricao' => 'sometimes|string|max:1000',
            'versao' => 'sometimes|nullable|string|max:20',
            'ativo' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('arquivo')) {
            if ($projeto->arquivo) {
                Storage::delete($projeto->arquivo);
            }
            $validated['arquivo'] = $request->file('arquivo')->store('projetos');
        }

        $projeto->update($validated);

        return response()->json($projeto);
    }

    /**
     * @OA\Delete(
     * path="/api/projetos/{id}",
     * tags={"Projetos"},
     * summary="Remover um projeto",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer", format="int64", description="ID do Projeto")
     * ),
     * @OA\Response(response=204, description="Projeto removido com sucesso"),
     * @OA\Response(response=401, description="Não autenticado"),
     * @OA\Response(response=403, description="Não autorizado a remover este projeto"),
     * @OA\Response(response=404, description="Projeto não encontrado")
     * )
     */
    public function destroy(Projeto $projeto)
    {
        if (Auth::check() && $projeto->id_empresa !== Auth::user()->id_empresa) {
            return response()->json(['message' => 'Você não tem permissão para remover este projeto.'], 403);
        }

        if ($projeto->arquivo) {
            Storage::delete($projeto->arquivo);
        }

        $projeto->delete();
        return response()->noContent();
    }
}
