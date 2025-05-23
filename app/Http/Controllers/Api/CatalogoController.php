<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Catálogos",
 *     description="Gerencia o catálogo das empresas"
 * )
 */
class CatalogoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/catalogos",
     *     tags={"Catálogos"},
     *     summary="Listar todos os catálogos",
     *     @OA\Response(response=200, description="Lista de catálogos")
     * )
     */
    public function index()
    {
        return Catalogo::all();
    }

    /**
     * @OA\Get(
     *     path="/api/catalogos/{id}",
     *     tags={"Catálogos"},
     *     summary="Exibir um catálogo específico",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Catálogo encontrado"),
     *     @OA\Response(response=404, description="Catálogo não encontrado")
     * )
     */
    public function show($id)
    {
        return Catalogo::findOrFail($id);
    }

    /**
     * @OA\Post(
     *     path="/api/catalogos",
     *     tags={"Catálogos"},
     *     summary="Criar um novo catálogo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"arquivo", "nome_arquivo", "descricao", "data_criacao", "versao", "ativo", "id_empresa"},
     *             @OA\Property(property="arquivo", type="string", format="binary"),
     *             @OA\Property(property="nome_arquivo", type="string", example="documento.pdf"),
     *             @OA\Property(property="descricao", type="string", example="Apresentação institucional"),
     *             @OA\Property(property="data_criacao", type="string", format="date", example="2025-05-20"),
     *             @OA\Property(property="versao", type="string", example="v1.0"),
     *             @OA\Property(property="ativo", type="boolean", example=true),
     *             @OA\Property(property="id_empresa", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Catálogo criado")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'arquivo' => 'required',
            'nome_arquivo' => 'required|string|max:100',
            'descricao' => 'required|string',
            'data_criacao' => 'required|date',
            'versao' => 'required|string|max:20',
            'ativo' => 'required|boolean',
            'id_empresa' => 'required|exists:empresas,id',
        ]);

        return Catalogo::create($validated);
    }

    /**
     * @OA\Put(
     *     path="/api/catalogos/{id}",
     *     tags={"Catálogos"},
     *     summary="Atualizar um catálogo",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nome_arquivo", type="string"),
     *             @OA\Property(property="descricao", type="string"),
     *             @OA\Property(property="data_criacao", type="string", format="date"),
     *             @OA\Property(property="versao", type="string"),
     *             @OA\Property(property="ativo", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Catálogo atualizado")
     * )
     */
    public function update(Request $request, $id)
    {
        $catalogo = Catalogo::findOrFail($id);
        $catalogo->update($request->only([
            'nome_arquivo', 'descricao', 'data_criacao', 'versao', 'ativo'
        ]));
        return $catalogo;
    }

    /**
     * @OA\Delete(
     *     path="/api/catalogos/{id}",
     *     tags={"Catálogos"},
     *     summary="Remover um catálogo",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Catálogo removido")
     * )
     */
    public function destroy($id)
    {
        $catalogo = Catalogo::findOrFail($id);
        $catalogo->delete();
        return response()->noContent();
    }
}
