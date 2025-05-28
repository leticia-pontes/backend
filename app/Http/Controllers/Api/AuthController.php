<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Perfil;
use App\Models\TipoPerfil;
use App\Models\Nicho;
use App\Models\Tecnologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="Login de empresa",
     * tags={"Autenticação"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","senha"},
     * @OA\Property(property="email", type="string", format="email"),
     * @OA\Property(property="senha", type="string", format="password")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login efetuado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="empresa", type="object"),
     * @OA\Property(property="token", type="string")
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Credenciais inválidas"
     * )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'senha' => ['required'],
        ]);

        $empresa = Empresa::where('email', $credentials['email'])->first();

        if (! $empresa || ! Hash::check($credentials['senha'], $empresa->senha)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }

        $token = $empresa->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'empresa' => $empresa->makeHidden(['senha']),
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Cadastro de empresa",
     * tags={"Autenticação"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"nome", "email", "senha", "senha_confirmation", "nome_tipo_perfil"},
     * @OA\Property(property="nome", type="string", example="Empresa Exemplo"),
     * @OA\Property(property="cnpj", type="string", example="43040983000100"),
     * @OA\Property(property="email", type="string", format="email", example="empresa@exemplo.com"),
     * @OA\Property(property="senha", type="string", format="password", example="senha123"),
     * @OA\Property(property="senha_confirmation", type="string", format="password", example="senha123"),
     * @OA\Property(property="nome_tipo_perfil", type="string", enum={"Contratante", "Desenvolvedor"}, example="Contratante"),
     * @OA\Property(property="telefone", type="string", nullable=true, example="11987654321"),
     * @OA\Property(property="endereco", type="string", nullable=true, example="Rua Exemplo, 123"),
     * @OA\Property(property="foto", type="string", nullable=true, example="http://example.com/foto.jpg"),
     * @OA\Property(property="biografia", type="string", nullable=true, example="Uma empresa inovadora..."),
     * @OA\Property(property="nicho_mercado_nome", type="string", nullable=true, example="Tecnologia"),
     * @OA\Property(property="tecnologia_nome", type="string", nullable=true, example="Laravel"),
     * @OA\Property(property="redes_sociais", type="string", nullable=true, description="String JSON contendo links de redes sociais, ex: {'instagram':'@exemplo', 'linkedin':'/empresa'}")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Cadastro realizado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string"),
     * @OA\Property(property="empresa", type="object"),
     * @OA\Property(property="access_token", type="string"),
     * @OA\Property(property="token_type", type="string")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Erro de validação",
     * @OA\JsonContent(
     * @OA\Property(property="errors", type="object")
     * )
     * )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:14|unique:empresas,cnpj',
            'email' => 'required|email|unique:empresas,email',
            'senha' => 'required|string|min:6|confirmed',
            'nome_tipo_perfil' => 'required|string|in:Contratante,Desenvolvedor',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'foto' => 'nullable|string',
            'biografia' => 'nullable|string',
            'nicho_mercado_nome' => 'nullable|string',
            'tecnologia_nome' => 'nullable|string',
            'redes_sociais' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            $empresa = Empresa::create([
                'nome' => $request->nome,
                'cnpj' => $request->cnpj,
                'email' => $request->email,
                'senha' => $request->senha,
                'telefone' => $request->telefone,
                'endereco' => $request->endereco,
                'data_cadastro' => Carbon::now(),
                'seguidores' => 0,
            ]);

            $tipoPerfil = TipoPerfil::where('nome_tipo', $request->nome_tipo_perfil)->first();

            if (!$tipoPerfil) {
                throw ValidationException::withMessages([
                    'nome_tipo_perfil' => ['Tipo de perfil inválido ou não encontrado.'],
                ]);
            }

            $perfil = Perfil::create([
                'id_empresa' => $empresa->id_empresa,
                'id_tipo_perfil' => $tipoPerfil->id_tipo_perfil,
                'foto' => $request->foto,
                'biografia' => $request->biografia,
                'redes_sociais' => $request->redes_sociais,
            ]);

            if ($request->nicho_mercado_nome) {
                $nicho = Nicho::firstOrCreate(['nome_nicho' => $request->nicho_mercado_nome]);
                $empresa->nichos()->attach($nicho->id_nicho);
            }
            if ($request->tecnologia_nome) {
                $tecnologia = Tecnologia::firstOrCreate(['nome_tecnologia' => $request->tecnologia_nome]);
                $empresa->tecnologias()->attach($tecnologia->id_tecnologia);
            }

            $token = $empresa->createToken('auth_token')->plainTextToken;

            $empresa->load('perfil.tipoPerfil', 'nichos', 'tecnologias');

            return response()->json([
                'message' => 'Cadastro realizado com sucesso',
                'empresa' => $empresa->makeHidden(['senha']),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        });
    }

    /**
     * @OA\Post(
     * path="/api/auth/logout",
     * summary="Logout de empresa",
     * tags={"Autenticação"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Logout realizado com sucesso"
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    /**
     * @OA\Get(
     * path="/api/auth/me",
     * summary="Dados da empresa autenticada",
     * tags={"Autenticação"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Dados da empresa",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="id_empresa", type="integer"),
     * @OA\Property(property="nome", type="string"),
     * @OA\Property(property="cnpj", type="string"),
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="senha", type="string", description="Senha oculta"),
     * @OA\Property(property="telefone", type="string", nullable=true),
     * @OA\Property(property="endereco", type="string", nullable=true),
     * @OA\Property(property="data_cadastro", type="string", format="date-time"),
     * @OA\Property(property="perfil", type="object",
     * @OA\Property(property="id_perfil", type="integer"),
     * @OA\Property(property="id_empresa", type="integer"),
     * @OA\Property(property="id_tipo_perfil", type="integer"),
     * @OA\Property(property="foto", type="string", nullable=true),
     * @OA\Property(property="biografia", type="string", nullable=true),
     * @OA\Property(property="redes_sociais", type="string", nullable=true),
     * @OA\Property(property="created_at", type="string", format="date-time"),
     * @OA\Property(property="updated_at", type="string", format="date-time"),
     * @OA\Property(property="tipo_perfil", type="object",
     * @OA\Property(property="id_tipo_perfil", type="integer"),
     * @OA\Property(property="nome_tipo", type="string")
     * )
     * ),
     * @OA\Property(property="nichos", type="array", @OA\Items(type="object")),
     * @OA\Property(property="tecnologias", type="array", @OA\Items(type="object")),
     * @OA\Property(property="seguidores", type="array", @OA\Items(type="object", description="Empresas que esta empresa segue")),
     * @OA\Property(property="seguindo", type="array", @OA\Items(type="object", description="Empresas que seguem esta empresa"))
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autenticado"
     * )
     * )
     */
    public function me(Request $request)
    {
        $empresa = $request->user()->load('perfil.tipoPerfil', 'nichos', 'tecnologias', 'seguidores', 'seguindo');

        return response()->json($empresa->makeHidden(['senha']));
    }
}
