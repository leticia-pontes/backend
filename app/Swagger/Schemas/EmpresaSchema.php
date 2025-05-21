<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Empresa",
 *     type="object",
 *     title="Empresa",
 *     required={"id", "nome", "cnpj", "email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nome", type="string", example="Empresa Exemplo"),
 *     @OA\Property(property="cnpj", type="string", example="12.345.678/0001-99"),
 *     @OA\Property(property="perfil", type="string", example="Perfil X"),
 *     @OA\Property(property="seguidores", type="integer", example=100),
 *     @OA\Property(property="email", type="string", example="contato@empresa.com"),
 *     @OA\Property(property="telefone", type="string", example="(11) 99999-9999"),
 *     @OA\Property(property="endereco", type="string", example="Rua Exemplo, 123"),
 * )
 *
 * @OA\Schema(
 *     schema="EmpresaCreateRequest",
 *     type="object",
 *     required={"nome", "cnpj", "email", "senha"},
 *     @OA\Property(property="nome", type="string", example="Empresa Exemplo"),
 *     @OA\Property(property="cnpj", type="string", example="12.345.678/0001-99"),
 *     @OA\Property(property="perfil", type="string", example="Perfil X"),
 *     @OA\Property(property="seguidores", type="integer", example=100),
 *     @OA\Property(property="email", type="string", example="contato@empresa.com"),
 *     @OA\Property(property="senha", type="string", example="senha123"),
 *     @OA\Property(property="telefone", type="string", example="(11) 99999-9999"),
 *     @OA\Property(property="endereco", type="string", example="Rua Exemplo, 123"),
 * )
 *
 * @OA\Schema(
 *     schema="EmpresaUpdateRequest",
 *     type="object",
 *     @OA\Property(property="nome", type="string", example="Empresa Exemplo"),
 *     @OA\Property(property="cnpj", type="string", example="12.345.678/0001-99"),
 *     @OA\Property(property="perfil", type="string", example="Perfil X"),
 *     @OA\Property(property="seguidores", type="integer", example=100),
 *     @OA\Property(property="email", type="string", example="contato@empresa.com"),
 *     @OA\Property(property="senha", type="string", example="senha123"),
 *     @OA\Property(property="telefone", type="string", example="(11) 99999-9999"),
 *     @OA\Property(property="endereco", type="string", example="Rua Exemplo, 123"),
 * )
 */

class EmpresaSchema
{
    // Esta classe é usada apenas para armazenar as anotações Swagger
}
