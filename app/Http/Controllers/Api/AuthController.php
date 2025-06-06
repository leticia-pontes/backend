<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Perfil;
use App\Models\TipoPerfil;
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
     *     path="/api/auth/login",
     *     summary="Login de empresa",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","senha"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="senha", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login efetuado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="empresa", type="object"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'senha' => ['required'],
        ]);

        $empresa = Empresa::where('email', $credentials['email'])->first();

        if (!$empresa || !Hash::check($credentials['senha'], $empresa->senha)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }

        $token = $empresa->createToken('auth_token')->plainTextToken;

        // Carregar perfil e tipoPerfil
        $empresa->load('perfil.tipoPerfil');

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'empresa' => $empresa->makeHidden(['senha']),
            'perfil' => $empresa->perfil ? [
                'id_perfil' => $empresa->perfil->id_perfil,
                'id_tipo_perfil' => $empresa->perfil->id_tipo_perfil,
                'tipo_perfil_nome' => $empresa->perfil->tipoPerfil->nome_tipo,
                'foto' => $empresa->perfil->foto ?? null,
            ] : null,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Cadastro de empresa ou desenvolvedor",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome", "email", "senha", "senha_confirmation", "nome_tipo_perfil"},
     *             @OA\Property(property="nome", type="string", example="Empresa Exemplo"),
     *             @OA\Property(property="cnpj", type="string", example="43040983000100"),
     *             @OA\Property(property="email", type="string", format="email", example="empresa@exemplo.com"),
     *             @OA\Property(property="senha", type="string", format="password", example="senha123"),
     *             @OA\Property(property="senha_confirmation", type="string", format="password", example="senha123"),
     *             @OA\Property(property="nome_tipo_perfil", type="string", enum={"Contratante","Desenvolvedor"}, example="Contratante"),
     *             @OA\Property(property="telefone", type="string", nullable=true, example="11987654321"),
     *             @OA\Property(property="endereco", type="string", nullable=true, example="Rua Exemplo, 123"),
     *             @OA\Property(property="foto", type="string", nullable=true, example="http://example.com/foto.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cadastro realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="empresa", type="object"),
     *             @OA\Property(property="perfil", type="object"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome'                 => 'required|string|max:100',
            'cnpj'                 => 'required_if:nome_tipo_perfil,Contratante|string|max:14|unique:empresas,cnpj',
            'email'                => 'required|email|unique:empresas,email',
            'senha'                => 'required|string|min:6|confirmed',
            'nome_tipo_perfil'     => 'required|string|in:Contratante,Desenvolvedor',
            'telefone'             => 'nullable|string|max:15',
            'endereco'             => 'nullable|string|max:200',
            'foto'                 => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            // 1) Cria a Empresa (criptografa a senha)
            $empresa = Empresa::create([
                'nome'          => $request->nome,
                'cnpj'          => $request->cnpj,             // só vem se for “Contratante”
                'email'         => $request->email,
                'senha'         => Hash::make($request->senha),
                'telefone'      => $request->telefone,
                'endereco'      => $request->endereco,
                'data_cadastro' => Carbon::now(),
            ]);

            // 2) Busca o TipoPerfil baseado no nome enviado
            $tipoPerfil = TipoPerfil::where('nome_tipo', $request->nome_tipo_perfil)->first();
            if (!$tipoPerfil) {
                throw ValidationException::withMessages([
                    'nome_tipo_perfil' => ['Tipo de perfil inválido ou não encontrado.'],
                ]);
            }

            // 3) Cria o Perfil associado à empresa
            $perfil = Perfil::create([
                'id_empresa'      => $empresa->id_empresa,
                'id_tipo_perfil'  => $tipoPerfil->id_tipo_perfil,
                'foto'            => $request->foto,  // se existir
                // Se quiser já preencher biografia e redes, adicione aqui
            ]);

            // 4) Gera token de API (Sanctum) usando o model Empresa
            $token = $empresa->createToken('auth_token')->plainTextToken;

            // 5) Carrega relações para retornar no JSON
            $empresa->load('perfil.tipoPerfil');

            return response()->json([
                'message'      => 'Cadastro realizado com sucesso',
                'empresa'      => $empresa->makeHidden(['senha']), // oculta senha
                'perfil'       => $perfil->makeHidden([]),         // traz id_perfil, id_tipo_perfil, id_empresa
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ], 201);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout de empresa",
     *     tags={"Autenticação"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Dados da empresa autenticada",
     *     tags={"Autenticação"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados da empresa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id_empresa", type="integer"),
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="cnpj", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="telefone", type="string", nullable=true),
     *             @OA\Property(property="endereco", type="string", nullable=true),
     *             @OA\Property(property="data_cadastro", type="string", format="date-time"),
     *             @OA\Property(property="perfil", type="object",
     *                 @OA\Property(property="id_perfil", type="integer"),
     *                 @OA\Property(property="id_empresa", type="integer"),
     *                 @OA\Property(property="id_tipo_perfil", type="integer"),
     *                 @OA\Property(property="foto", type="string", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="tipo_perfil", type="object",
     *                     @OA\Property(property="id_tipo_perfil", type="integer"),
     *                     @OA\Property(property="nome_tipo", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado"
     *     )
     * )
     */
    public function me(Request $request)
    {
        $empresa = $request->user()->load('perfil.tipoPerfil');

        return response()->json($empresa->makeHidden(['senha']));
    }
}
