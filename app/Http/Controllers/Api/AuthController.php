<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
     *             @OA\Property(property="token", type="string")
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

        if (! $empresa || ! Hash::check($credentials['senha'], $empresa->senha)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }

        $token = $empresa->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'empresa' => $empresa,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Cadastro de empresa",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome", "email", "senha", "senha_confirmation", "perfil"},
     *             @OA\Property(property="nome", type="string", example="Empresa Exemplo"),
     *             @OA\Property(property="cnpj", type="string", example="43040983000100"),
     *             @OA\Property(property="email", type="string", format="email", example="empresa@exemplo.com"),
     *             @OA\Property(property="senha", type="string", format="password", example="senha123"),
     *             @OA\Property(property="senha_confirmation", type="string", format="password", example="senha123"),
     *             @OA\Property(property="perfil", type="string", enum={"empresa_contratante", "desenvolvedor"}, example="empresa_contratante")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cadastro realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cadastro realizado com sucesso"),
     *             @OA\Property(property="empresa", type="object"),
     *             @OA\Property(property="access_token", type="string", example="1|xpto123..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
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
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|unique:empresas,cnpj',
            'email' => 'required|email|unique:empresas,email',
            'senha' => 'required|string|min:6|confirmed',
            'perfil' => 'required|in:empresa_contratante,desenvolvedor',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'seguidores' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $empresa = Empresa::create([
            'nome' => $request->nome,
            'cnpj' => $request->cnpj,
            'email' => $request->email,
            'senha' => $request->senha, // será criptografada automaticamente pelo mutator
            'perfil' => $request->perfil,
            'telefone' => $request->telefone,
            'endereco' => $request->endereco,
            'seguidores' => $request->seguidores ?? 0,
        ]);

        $token = $empresa->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Cadastro realizado com sucesso',
            'empresa' => $empresa,
            'token' => $token,
        ], 201);
    }

    /**
     * Logout - Revoga o token atual
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    /**
     * Retorna os dados da empresa autenticada
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
