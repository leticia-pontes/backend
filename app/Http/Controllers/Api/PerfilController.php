<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/perfis",
     *     summary="Lista todos os perfis",
     *     tags={"Perfis"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Perfis",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Perfil"))
     *     )
     * )
     */
    public function index()
    {
        $perfis = Perfil::with(['nichos', 'tecnologias', 'empresa', 'tipoPerfil'])->get();
        return response()->json($perfis);
    }

    /**
     * @OA\Post(
     *     path="/api/perfis",
     *     summary="Cria ou atualiza perfil (todos os campos são opcionais)",
     *     tags={"Perfis"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="biografia",
     *                     type="string",
     *                     description="Texto livre para a biografia",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="id_tipo_perfil",
     *                     type="integer",
     *                     description="ID do tipo de perfil (opcional)",
     *                     nullable=true,
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="redes_sociais",
     *                     type="array",
     *                     description="Lista de URLs ou nomes de usuário de redes sociais",
     *                     nullable=true,
     *                     @OA\Items(type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="nichos",
     *                     type="array",
     *                     description="Array de IDs de nichos selecionados",
     *                     nullable=true,
     *                     @OA\Items(type="integer")
     *                 ),
     *                 @OA\Property(
     *                     property="tecnologias",
     *                     type="array",
     *                     description="Array de IDs de tecnologias (opcional)",
     *                     nullable=true,
     *                     @OA\Items(type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Perfil criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Perfil")
     *     ),
     *     @OA\Response(response=200, description="Perfil atualizado com sucesso", @OA\JsonContent(ref="#/components/schemas/Perfil")),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(Request $request)
    {
        // 1) Validação: todos os campos são nullable (opcionais)
        $validatedData = $request->validate([
            'biografia'        => 'nullable|string',
            'redes_sociais'    => 'nullable|array',
            'redes_sociais.*'  => 'string',
            'id_tipo_perfil'   => 'nullable|integer|exists:tipo_perfis,id_tipo_perfil',
            'nichos'           => 'nullable|array',
            'nichos.*'         => 'integer|exists:nichos,id_nicho',
            'tecnologias'      => 'nullable|array',
            'tecnologias.*'    => 'integer|exists:tecnologias,id_tecnologia',
        ]);

        $usuario = auth()->user();
        if (!$usuario || !$usuario->id_empresa) {
            return response()->json(['message' => 'Usuário não autenticado ou sem empresa associada'], 401);
        }

        // 2) Checa se já existe perfil para esta empresa
        $perfilExistente = Perfil::where('id_empresa', $usuario->id_empresa)->first();

        if (!isset($validatedData['id_tipo_perfil'])) {
            $validatedData['id_tipo_perfil'] = 1;
        }

        // Cria ou atualiza
        if ($perfilExistente) {
            // Atualiza as colunas que vieram em $validatedData
            $perfilExistente->fill([
                'biografia'      => $validatedData['biografia'] ?? $perfilExistente->biografia,
                'id_tipo_perfil' => $validatedData['id_tipo_perfil'],
            ]);
            $perfilExistente->save();

            // Sincroniza nichos e tecnologias, se vierem
            if (isset($validatedData['nichos'])) {
                $perfilExistente->nichos()->sync($validatedData['nichos']);
            }
            if (isset($validatedData['tecnologias'])) {
                $perfilExistente->tecnologias()->sync($validatedData['tecnologias']);
            }

            if (array_key_exists('redes_sociais', $validatedData)) {
                $perfilExistente->redes_sociais = $validatedData['redes_sociais'];
                $perfilExistente->save();
            }

            $perfilExistente->load(['nichos', 'tecnologias', 'empresa', 'tipoPerfil']);
            return response()->json($perfilExistente, 200);
        }

        // 3) Se não existe, cria novo
        $validatedData['id_empresa'] = $usuario->id_empresa;

        // Se não veio `nichos` ou `tecnologias`, garanta chave vazia para sincronizar
        $nichos      = $validatedData['nichos']     ?? [];
        $tecnologias = $validatedData['tecnologias'] ?? [];

        unset($validatedData['nichos'], $validatedData['tecnologias']);

        $perfil = Perfil::create($validatedData);
        $perfil->nichos()->sync($nichos);
        $perfil->tecnologias()->sync($tecnologias);

        $perfil->load(['nichos', 'tecnologias', 'empresa', 'tipoPerfil']);
        return response()->json($perfil, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/perfis/{id}",
     *     summary="Exibe um perfil específico",
     *     tags={"Perfis"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Dados do perfil", @OA\JsonContent(ref="#/components/schemas/Perfil")),
     *     @OA\Response(response=404, description="Perfil não encontrado")
     * )
     */
    public function show(string $id)
    {
        $perfil = Perfil::with(['nichos', 'tecnologias', 'empresa', 'tipoPerfil'])->findOrFail($id);
        return response()->json($perfil);
    }

    /**
     * @OA\Put(
     *     path="/api/perfis/{id}",
     *     summary="Atualiza um perfil existente",
     *     tags={"Perfis"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="foto", type="string", description="URL ou base64 da imagem (opcional)"),
     *                 @OA\Property(property="biografia", type="string"),
     *                 @OA\Property(property="id_empresa", type="integer"),
     *                 @OA\Property(property="id_tipo_perfil", type="integer"),
     *                 @OA\Property(property="redes_sociais", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="nichos", type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="tecnologias", type="array", @OA\Items(type="integer"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Perfil atualizado", @OA\JsonContent(ref="#/components/schemas/Perfil")),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Perfil não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function update(Request $request, string $id)
    {
        $perfil = Perfil::findOrFail($id);

        $validatedData = $request->validate([
            'foto' => 'nullable|string',
            'biografia' => 'nullable|string',
            'redes_sociais' => 'nullable|array',
            'redes_sociais.*' => 'string',
            'id_empresa' => 'sometimes|integer|exists:empresas,id_empresa',
            'id_tipo_perfil' => 'nullable|integer|exists:tipo_perfis,id_tipo_perfil',
            'nichos' => 'nullable|array',
            'nichos.*' => 'integer|exists:nichos,id_nicho',
            'tecnologias' => 'nullable|array',
            'tecnologias.*' => 'integer|exists:tecnologias,id_tecnologia',
        ]);

        $nichos = $validatedData['nichos'] ?? null;
        $tecnologias = $validatedData['tecnologias'] ?? null;
        unset($validatedData['nichos'], $validatedData['tecnologias']);

        $updated = $perfil->update($validatedData);

        if (!$updated) {
            return response()->json(['message' => 'Falha ao atualizar o perfil'], 500);
        }

        if (!is_null($nichos)) {
            $perfil->nichos()->sync($nichos);
        }
        if (!is_null($tecnologias)) {
            $perfil->tecnologias()->sync($tecnologias);
        }

        $perfil->load(['nichos', 'tecnologias', 'empresa', 'tipoPerfil']);

        return response()->json($perfil);
    }

    /**
     * @OA\Delete(
     *     path="/api/perfis/{id}",
     *     summary="Remove um perfil",
     *     tags={"Perfis"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Perfil removido com sucesso"),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Perfil não encontrado")
     * )
     */
    public function destroy(string $id)
    {
        $perfil = Perfil::findOrFail($id);
        $perfil->delete();

        return response()->json(null, 204);
    }
}
