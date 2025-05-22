<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
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
