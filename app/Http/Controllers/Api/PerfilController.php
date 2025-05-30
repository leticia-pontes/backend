<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perfil;
use App\Models\Nicho;
use App\Models\Tecnologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class PerfilController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/perfis",
     * summary="Lista todos os perfis",
     * tags={"Perfil"},
     * @OA\Response(
     * response=200,
     * description="Lista de Perfis",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Perfil"))
     * )
     * )
     */
    public function index()
    {
        // Carrega os relacionamentos nichos e tecnologias para que sejam incluídos na resposta
        $perfis = Perfil::with(['nichos', 'tecnologias', 'empresa', 'tipoPerfil'])->get();
        return response()->json($perfis);
    }

    /**
     * @OA\Post(
     * path="/api/perfis",
     * summary="Cria um novo perfil",
     * tags={"Perfil"},
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"id_empresa"},
     * @OA\Property(property="foto", type="string", format="binary", description="Arquivo de imagem para a foto do perfil."),
     * @OA\Property(property="biografia", type="string", description="Biografia do perfil."),
     * @OA\Property(property="id_empempresa", type="integer", description="ID da empresa associada ao perfil."),
     * @OA\Property(property="id_tipo_perfil", type="integer", nullable=true, description="ID do tipo de perfil."),
     * @OA\Property(property="redes_sociais", type="string", format="json", description="Array JSON de URLs de redes sociais."),
     * @OA\Property(property="nichos", type="array", @OA\Items(type="integer"), description="Array de IDs de nichos para associar ao perfil."),
     * @OA\Property(property="tecnologias", type="array", @OA\Items(type="integer"), description="Array de IDs de tecnologias para associar ao perfil.")
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Perfil criado com sucesso",
     * @OA\JsonContent(ref="#/components/schemas/Perfil")
     * ),
     * @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'foto' => 'nullable|file|image|max:2048', // Max 2MB
            'biografia' => 'nullable|string',
            'redes_sociais' => 'nullable|json', // Espera uma string JSON
            'id_empresa' => 'required|integer|exists:empresas,id_empresa', // Ajuste para id_empresa
            'id_tipo_perfil' => 'nullable|integer|exists:tipo_perfis,id_tipo_perfil', // Ajuste para id_tipo_perfil
            'nichos' => 'nullable|array',
            'nichos.*' => 'integer|exists:nichos,id_nicho', // Valida cada ID no array de nichos
            'tecnologias' => 'nullable|array',
            'tecnologias.*' => 'integer|exists:tecnologias,id_tecnologia', // Valida cada ID no array de tecnologias
        ]);

        // Trata o upload da foto, se existir
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('perfis', 'public');
            $validatedData['foto'] = $path;
        } else {
            // Remove 'foto' do validatedData se não houver arquivo e não for para ser atualizado
            unset($validatedData['foto']);
        }

        // Remove nichos e tecnologias dos dados principais antes de criar o perfil,
        // pois eles serão anexados separadamente.
        $nichos = $validatedData['nichos'] ?? [];
        $tecnologias = $validatedData['tecnologias'] ?? [];
        unset($validatedData['nichos'], $validatedData['tecnologias']);


        // Cria o perfil
        $perfil = Perfil::create($validatedData);

        // Anexa os nichos e tecnologias ao perfil
        // O `sync` é útil para adicionar/remover associações, garantindo que apenas os IDs fornecidos permaneçam.
        // Para apenas adicionar e não remover os existentes, usar `attach`.
        $perfil->nichos()->sync($nichos);
        $perfil->tecnologias()->sync($tecnologias);

        // Carrega os relacionamentos para a resposta, se necessário
        $perfil->load(['nichos', 'tecnologias', 'empresa', 'tipoPerfil']);

        return response()->json($perfil, 201);
    }

    /**
     * @OA\Get(
     * path="/api/perfis/{id}",
     * summary="Exibe um perfil específico",
     * tags={"Perfil"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer", description="ID do perfil")
     * ),
     * @OA\Response(
     * response=200,
     * description="Dados do perfil",
     * @OA\JsonContent(ref="#/components/schemas/Perfil")
     * ),
     * @OA\Response(response=404, description="Perfil não encontrado")
     * )
     */
    public function show(string $id)
    {
        // Carrega os relacionamentos ao buscar um perfil específico
        $perfil = Perfil::with(['nichos', 'tecnologias', 'empresa', 'tipoPerfil'])->findOrFail($id);
        return response()->json($perfil);
    }

    /**
     * @OA\Put(
     * path="/api/perfis/{id}",
     * summary="Atualiza um perfil existente",
     * tags={"Perfil"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do perfil",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="foto", type="string", format="binary", nullable=true, description="Novo arquivo de imagem para a foto do perfil."),
     * @OA\Property(property="biografia", type="string", nullable=true, description="Nova biografia do perfil."),
     * @OA\Property(property="id_empresa", type="integer", nullable=true, description="Novo ID da empresa associada ao perfil."),
     * @OA\Property(property="id_tipo_perfil", type="integer", nullable=true, description="Novo ID do tipo de perfil."),
     * @OA\Property(property="redes_sociais", type="string", format="json", nullable=true, description="Novo array JSON de URLs de redes sociais."),
     * @OA\Property(property="nichos", type="array", @OA\Items(type="integer"), nullable=true, description="Array de IDs de nichos para associar/sincronizar ao perfil."),
     * @OA\Property(property="tecnologias", type="array", @OA\Items(type="integer"), nullable=true, description="Array de IDs de tecnologias para associar/sincronizar ao perfil.")
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Perfil atualizado com sucesso",
     * @OA\JsonContent(ref="#/components/schemas/Perfil")
     * ),
     * @OA\Response(response=404, description="Perfil não encontrado"),
     * @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function update(Request $request, string $id)
    {
        $perfil = Perfil::findOrFail($id);

        $validatedData = $request->validate([
            'foto' => 'nullable|file|image|max:2048',
            'biografia' => 'nullable|string',
            'redes_sociais' => 'nullable|json',
            'id_empresa' => 'sometimes|integer|exists:empresas,id_empresa',
            'id_tipo_perfil' => 'nullable|integer|exists:tipo_perfis,id_tipo_perfil',
            'nichos' => 'nullable|array',
            'nichos.*' => 'integer|exists:nichos,id_nicho',
            'tecnologias' => 'nullable|array',
            'tecnologias.*' => 'integer|exists:tecnologias,id_tecnologia',
        ]);

        if ($request->hasFile('foto')) {
            if ($perfil->foto && \Storage::disk('public')->exists($perfil->foto)) {
                \Storage::disk('public')->delete($perfil->foto);
            }
            $path = $request->file('foto')->store('perfis', 'public');
            $validatedData['foto'] = $path;
        }

        // Remove nichos e tecnologias dos dados principais antes de atualizar o perfil,
        // pois eles serão sincronizados separadamente.
        $nichos = $validatedData['nichos'] ?? null; // null para saber se foi enviado
        $tecnologias = $validatedData['tecnologias'] ?? null;
        unset($validatedData['nichos'], $validatedData['tecnologias']);


        $updated = $perfil->update($validatedData);

        if (!$updated) {
            return response()->json(['message' => 'Falha ao atualizar o perfil'], 500);
        }

        // Sincroniza os nichos e tecnologias APENAS SE ELES FOREM ENVIADOS na requisição
        // Isso permite atualizar outros campos sem ter que enviar todos os nichos/tecnologias toda vez.
        if (!is_null($nichos)) {
            $perfil->nichos()->sync($nichos);
        }
        if (!is_null($tecnologias)) {
            $perfil->tecnologias()->sync($tecnologias);
        }

        // Recarrega o perfil com os novos relacionamentos para a resposta
        $perfil->load(['nichos', 'tecnologias', 'empresa', 'tipoPerfil']);

        return response()->json($perfil);
    }

    /**
     * @OA\Delete(
     * path="/api/perfis/{id}",
     * summary="Remove um perfil",
     * tags={"Perfil"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="Perfil removido com sucesso"
     * ),
     * @OA\Response(response=404, description="Perfil não encontrado")
     * )
     */
    public function destroy(string $id)
    {
        $perfil = Perfil::findOrFail($id);
        $perfil->delete();

        return response()->json(null, 204);
    }
}
