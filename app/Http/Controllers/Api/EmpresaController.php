<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Perfil;
use App\Models\TipoPerfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

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
     *     tags={"Empresas"},
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
     *     summary="Cria uma nova empresa (etapa 1) e retorna ID para personalização de perfil",
     *     tags={"Empresas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"nome", "cnpj", "email", "senha"},
     *             @OA\Property(property="nome", type="string", example="Empresa XYZ"),
     *             @OA\Property(property="cnpj", type="string", example="12.345.678/0001-90"),
     *             @OA\Property(property="email", type="string", format="email", example="empresa@exemplo.com"),
     *             @OA\Property(property="senha", type="string", format="password", example="segura123"),
     *             @OA\Property(property="telefone", type="string", example="(11) 99999-8888"),
     *             @OA\Property(property="endereco", type="string", example="Rua Exemplo, 123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empresa criada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id_empresa", type="integer", example=123),
     *             @OA\Property(property="redirect_to", type="string", example="/perfil/personalizar/123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="O campo nome é obrigatório."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Remover senha antes de logar
        $dataParaLog = Arr::except($request->all(), ['senha']);
        Log::info('Tentando criar empresa (sem senha):', $dataParaLog);

        $validated = $request->validate([
            'nome'     => 'required|string|max:255',
            'cnpj'     => 'required|string|max:20|unique:empresas,cnpj',
            'email'    => 'required|email|unique:empresas,email',
            'senha'    => 'required|string|min:6',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
        ]);

        // Criptografa a senha
        $validated['senha'] = Hash::make($validated['senha']);

        // Normaliza CNPJ (apenas dígitos)
        $cnpjLimpo = preg_replace('/\D/', '', $validated['cnpj']);

        // Normaliza telefone (apenas dígitos), se informado
        $telefoneLimpo = isset($validated['telefone'])
            ? preg_replace('/\D/', '', $validated['telefone'])
            : null;

        // Cria a empresa
        $empresa = Empresa::create([
            'nome'         => $validated['nome'],
            'cnpj'         => $cnpjLimpo,
            'email'        => $validated['email'],
            'senha'        => $validated['senha'],
            'telefone'     => $telefoneLimpo,
            'endereco'     => $validated['endereco'] ?? null,
            'data_cadastro'=> now(),
            'nivel'        => 1,
            'pontos'       => 0,
        ]);

        Log::info("Empresa ID {$empresa->id_empresa} criada com sucesso.");

        return response()->json([
            'id_empresa' => $empresa->id_empresa
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/empresas/{id}",
     *     summary="Mostra uma empresa pelo ID",
     *     tags={"Empresas"},
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
     *     tags={"Empresas"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da empresa",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nome", type="string", example="Empresa Atualizada"),
     *             @OA\Property(property="cnpj", type="string", example="12.345.678/0001-91"),
     *             @OA\Property(property="email", type="string", format="email", example="novoemail@empresa.com"),
     *             @OA\Property(property="senha", type="string", format="password", example="novasenha123"),
     *             @OA\Property(property="telefone", type="string", example="(11) 98888-7777"),
     *             @OA\Property(property="endereco", type="string", example="Rua Nova, 456")
     *         )
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
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="O campo email é obrigatório."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $empresa = Empresa::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'cnpj' => 'sometimes|required|string|max:20|unique:empresas,cnpj,' . $id . ',id_empresa',
            'email' => 'sometimes|required|email|unique:empresas,email,' . $id . ',id_empresa',
            'senha' => 'sometimes|required|string|min:6',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
        ]);

        if (!empty($validatedData['senha'])) {
            $validatedData['senha'] = Hash::make($validatedData['senha']);
        } else {
            unset($validatedData['senha']);
        }

        $empresa->update($validatedData);

        return response()->json($empresa);
    }

    /**
     * @OA\Delete(
     *     path="/api/empresas/{id}",
     *     summary="Deleta uma empresa existente",
     *     tags={"Empresas"},
     *     security={{"sanctum": {}}},
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
