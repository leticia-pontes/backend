<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Empresa"))
     *     )
     * )
     */
    public function index()
    {
        $empresas = Empresa::all();
        return response()->json($empresas);
    }

    /**
     * @OA\Post(
     *     path="/api/empresas",
     *     summary="Cria uma nova empresa",
     *     tags={"Empresa"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"nome","cnpj","email","senha"},
     *             @OA\Property(property="nome", type="string", example="Empresa Exemplo"),
     *             @OA\Property(property="cnpj", type="string", example="12.345.678/0001-99"),
     *             @OA\Property(property="perfil", type="string", example="perfil_exemplo"),
     *             @OA\Property(property="seguidores", type="integer", example=100),
     *             @OA\Property(property="email", type="string", format="email", example="email@exemplo.com"),
     *             @OA\Property(property="senha", type="string", format="password", example="123456"),
     *             @OA\Property(property="telefone", type="string", example="(11) 99999-9999"),
     *             @OA\Property(property="endereco", type="string", example="Rua Exemplo, 123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empresa criada com sucesso"
     *     )
     * )
     */
    public function store(Request $request)
    {
        Log::info('Dados recebidos:', $request->all());

        $exists = \DB::table('empresas')->where('cnpj', $request->cnpj)->exists();

        Log::info('Existe empresa com esse CNPJ? ' . ($exists ? 'Sim' : 'Não'));

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|unique:empresas,cnpj',
            'perfil' => 'nullable|string',
            'seguidores' => 'nullable|integer',
            'email' => 'required|email|unique:empresas,email',
            'senha' => 'required|string|min:6',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
        ]);

        $empresa = Empresa::create($validatedData);

        return response()->json($empresa, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/empresas/{id}",
     *     summary="Mostra uma empresa pelo id",
     *     tags={"Empresa"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da empresa",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *     summary="Atualiza uma empresa",
     *     tags={"Empresa"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da empresa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EmpresaUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa atualizada",
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
            'perfil' => 'nullable|string',
            'seguidores' => 'nullable|integer',
            'email' => 'sometimes|required|email|unique:empresas,email,' . $id . ',id',
            'senha' => 'sometimes|required|string|min:6',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
        ]);

        if (isset($validatedData['senha'])) {
            $validatedData['senha'] = bcrypt($validatedData['senha']);
        }

        $empresa->update($validatedData);

        return response()->json($empresa);
    }

    /**
     * @OA\Delete(
     *     path="/api/empresas/{id}",
     *     summary="Deleta uma empresa",
     *     tags={"Empresa"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da empresa",
     *         required=true,
     *         @OA\Schema(type="integer")
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
