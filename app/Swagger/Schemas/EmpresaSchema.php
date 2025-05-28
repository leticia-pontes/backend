<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="Empresa",
 * type="object",
 * title="Empresa",
 * description="Detalhes de uma empresa",
 * required={"id", "nome", "cnpj", "email"},
 * @OA\Property(
 * property="id",
 * type="integer",
 * format="int64",
 * description="ID único da empresa",
 * readOnly=true,
 * example=1
 * ),
 * @OA\Property(
 * property="nome",
 * type="string",
 * description="Nome da empresa",
 * example="Empresa de Soluções Ltda."
 * ),
 * @OA\Property(
 * property="cnpj",
 * type="string",
 * description="CNPJ da empresa",
 * example="12.345.678/0001-99"
 * ),
 * @OA\Property(
 * property="perfil",
 * type="string",
 * nullable=true,
 * description="Perfil da empresa (opcional)",
 * example="Tecnologia e Inovação"
 * ),
 * @OA\Property(
 * property="seguidores",
 * type="integer",
 * nullable=true,
 * description="Número de seguidores da empresa (opcional)",
 * example=1500
 * ),
 * @OA\Property(
 * property="email",
 * type="string",
 * format="email",
 * description="Endereço de e-mail da empresa",
 * example="contato@empresasolucao.com"
 * ),
 * @OA\Property(
 * property="telefone",
 * type="string",
 * nullable=true,
 * description="Número de telefone da empresa (opcional)",
 * example="(11) 98765-4321"
 * ),
 * @OA\Property(
 * property="endereco",
 * type="string",
 * nullable=true,
 * description="Endereço completo da empresa (opcional)",
 * example="Av. Principal, 1000 - Centro"
 * ),
 * @OA\Property(
 * property="created_at",
 * type="string",
 * format="date-time",
 * description="Timestamp de criação do registro",
 * readOnly=true
 * ),
 * @OA\Property(
 * property="updated_at",
 * type="string",
 * format="date-time",
 * description="Timestamp da última atualização do registro",
 * readOnly=true
 * )
 * )
 *
 * @OA\Schema(
 * schema="EmpresaCreateRequest",
 * type="object",
 * title="EmpresaCreateRequest",
 * description="Dados necessários para criar uma nova empresa",
 * required={"nome", "cnpj", "email", "senha"},
 * @OA\Property(property="nome", type="string", example="Nova Empresa Ltda."),
 * @OA\Property(property="cnpj", type="string", example="98.765.432/0001-10"),
 * @OA\Property(property="perfil", type="string", nullable=true, example="Consultoria"),
 * @OA\Property(property="seguidores", type="integer", nullable=true, example=50),
 * @OA\Property(property="email", type="string", format="email", example="registro@novaempresa.com"),
 * @OA\Property(property="senha", type="string", format="password", example="senha_segura123"),
 * @OA\Property(property="telefone", type="string", nullable=true, example="(21) 91234-5678"),
 * @OA\Property(property="endereco", type="string", nullable=true, example="Rua da Inovação, 50 - Bairro Novo")
 * )
 *
 * @OA\Schema(
 * schema="EmpresaUpdateRequest",
 * type="object",
 * title="EmpresaUpdateRequest",
 * description="Dados opcionais para atualizar uma empresa existente",
 * @OA\Property(property="nome", type="string", example="Empresa Atualizada S.A."),
 * @OA\Property(property="cnpj", type="string", example="12.345.678/0001-99"),
 * @OA\Property(property="perfil", type="string", nullable=true, example="Software House"),
 * @OA\Property(property="seguidores", type="integer", nullable=true, example=2000),
 * @OA\Property(property="email", type="string", format="email", example="novo.email@empresa.com"),
 * @OA\Property(property="senha", type="string", format="password", description="Deixe vazio para não alterar", example="nova_senha_456"),
 * @OA\Property(property="telefone", type="string", nullable=true, example="(11) 99887-7665"),
 * @OA\Property(property="endereco", type="string", nullable=true, example="Av. Central, 500 - Distrito Industrial")
 * )
 */
class EmpresaSchema
{
    // Esta classe é usada apenas para armazenar as anotações Swagger
}
