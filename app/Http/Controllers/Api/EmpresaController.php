<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *     name="Empresa",
 *     description="Operações relacionadas a empresas"
 * )
 */
class EmpresaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/empresas",
     *     summary="Lista todas as empresas",
     *     tags={"Empresa"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Empresas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Empresa")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Empresa::all());
    }

    /**
     * @OA\Post(
     *     path="/api/empresas",
     *     summary="Cria uma nova empresa",
     *     tags={"Empresa"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EmpresaCreateRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empresa criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Empresa")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function store(Request $request)
    {
        Log::info('Dados recebidos para criação de empresa:', $request->all());

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|unique:empresas,cnpj',
            'perfil' => 'nullable|string|max:255',
            'seguidores' => 'nullable|integer',
            'email' => 'required|email|unique:empresas,email',
            'senha' => 'required|string|min:6',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
        ]);

        $validatedData['senha'] = Hash::make($validatedData['senha']);

        $empresa = Empresa::create($validatedData);

        return response()->json($empresa, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/empresas/{id}",
     *     summary="Mostra uma empresa pelo ID",
     *     tags={"Empresa"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da empresa",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da empresa",
     *         @OA\JsonContent(ref="#/components/schemas/Empresa")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa não encontrada"
     *     )
     * )
     */
    public function show(string $id)
    {
        $empresa = Empresa::findOrFail($id);
        return response()->json($empresa);
    }

    /**
     * @OA\Put(
     *     path="/api/empresas/{id}",
     *     summary="Atualiza uma empresa existente",
     *     tags={"Empresa"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da empresa",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EmpresaUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Empresa")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa não encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $empresa = Empresa::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'cnpj' => 'sometimes|required|string|max:20|unique:empresas,cnpj,' . $id . ',id',
            'perfil' => 'nullable|string|max:255',
            'seguidores' => 'nullable|integer',
            'email' => 'sometimes|required|email|unique:empresas,email,' . $id . ',id',
            'senha' => 'sometimes|required|string|min:6',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
        ]);

        if (isset($validatedData['senha'])) {
            $validatedData['senha'] = Hash::make($validatedData['senha']);
        }

        $empresa->update($validatedData);

        return response()->json($empresa);
    }

    /**
     * @OA\Delete(
     *     path="/api/empresas/{id}",
     *     summary="Deleta uma empresa existente",
     *     tags={"Empresa"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da empresa",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Empresa deletada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa não encontrada"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();

        return response()->json(null, 204);
    }
}
