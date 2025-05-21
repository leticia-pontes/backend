<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerfilController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/perfis",
     *     summary="Lista todos os perfis",
     *     tags={"Perfil"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Perfis",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Perfil"))
     *     )
     * )
     */
    public function index()
    {
        $perfis = Perfil::all();
        return response()->json($perfis);
    }

    /**
     * @OA\Post(
     *     path="/api/perfis",
     *     summary="Cria um novo perfil",
     *     tags={"Perfil"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id_empresa"},
     *                 @OA\Property(property="foto", type="string", format="binary"),
     *                 @OA\Property(property="biografia", type="string"),
     *                 @OA\Property(property="nicho_mercado", type="string"),
     *                 @OA\Property(property="tecnologia", type="string"),
     *                 @OA\Property(property="redes_sociais", type="string"),
     *                 @OA\Property(property="id_empresa", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Perfil criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Perfil")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'foto' => 'nullable|file|image',
            'biografia' => 'nullable|string',
            'nicho_mercado' => 'nullable|string|max:50',
            'tecnologia' => 'nullable|string|max:50',
            'redes_sociais' => 'nullable',
            'id_empresa' => 'required|integer|exists:empresas,id',
        ]);

        if (is_array($validatedData['redes_sociais'] ?? null)) {
            $validatedData['redes_sociais'] = json_encode($validatedData['redes_sociais']);
        }

        if (is_string($validatedData['redes_sociais'] ?? null)) {
            json_decode($validatedData['redes_sociais']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'Campo redes_sociais deve ser uma string JSON válida.'], 422);
            }
        }

        $perfil = Perfil::create($validatedData);

        return response()->json($perfil, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/perfis/{id}",
     *     summary="Exibe um perfil específico",
     *     tags={"Perfil"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do perfil",
     *         @OA\JsonContent(ref="#/components/schemas/Perfil")
     *     )
     * )
     */
    public function show(string $id)
    {
        $perfil = Perfil::findOrFail($id);
        return response()->json($perfil);
    }

    /**
     * @OA\Put(
     *     path="/api/perfis/{id}",
     *     summary="Atualiza um perfil existente",
     *     tags={"Perfil"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do perfil",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="biografia", type="string"),
     *                 @OA\Property(property="nicho_mercado", type="string"),
     *                 @OA\Property(property="tecnologia", type="string"),
     *                 @OA\Property(property="redes_sociais", type="string"),
     *                 @OA\Property(property="id_empresa", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Perfil atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Perfil")
     *     ),
     *     @OA\Response(response=404, description="Perfil não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function update(Request $request, string $id)
    {
        $perfil = Perfil::findOrFail($id);

        $validatedData = $request->validate([
            'foto' => 'nullable|file|image',
            'biografia' => 'nullable|string',
            'nicho_mercado' => 'nullable|string|max:50',
            'tecnologia' => 'nullable|string|max:50',
            'redes_sociais' => 'nullable',
            'id_empresa' => 'sometimes|integer|exists:empresas,id',
        ]);

        \Log::info('Validated data:', $validatedData);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('perfis', 'public');
            $validatedData['foto'] = $path;
        }

        if (is_array($validatedData['redes_sociais'] ?? null)) {
            $validatedData['redes_sociais'] = json_encode($validatedData['redes_sociais']);
        }

        if (is_string($validatedData['redes_sociais'] ?? null)) {
            json_decode($validatedData['redes_sociais']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'Campo redes_sociais deve ser uma string JSON válida.'], 422);
            }
        }

        $updated = $perfil->update($validatedData);

        if (!$updated) {
            return response()->json(['message' => 'Falha ao atualizar o perfil'], 500);
        }

        return response()->json($perfil);
    }

    /**
     * @OA\Delete(
     *     path="/api/perfis/{id}",
     *     summary="Remove um perfil",
     *     tags={"Perfil"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Perfil removido com sucesso"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $perfil = Perfil::findOrFail($id);
        $perfil->delete();

        return response()->json(null, 204);
    }
}
