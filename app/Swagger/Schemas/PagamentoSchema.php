<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="Pagamento",
 * type="object",
 * title="Pagamento",
 * description="Detalhes de um pagamento",
 * required={"id", "valor", "data_pagamento", "metodo_pagamento", "status", "id_pedido"},
 * @OA\Property(
 * property="id",
 * type="integer",
 * format="int64",
 * description="ID único do pagamento",
 * readOnly=true,
 * example=1
 * ),
 * @OA\Property(
 * property="valor",
 * type="number",
 * format="float",
 * description="Valor total do pagamento",
 * example=100.50
 * ),
 * @OA\Property(
 * property="data_pagamento",
 * type="string",
 * format="date",
 * description="Data em que o pagamento foi realizado",
 * example="2025-05-23"
 * ),
 * @OA\Property(
 * property="metodo_pagamento",
 * type="string",
 * description="Método de pagamento (ex: cartão, boleto, pix)",
 * example="cartao de credito"
 * ),
 * @OA\Property(
 * property="status",
 * type="string",
 * description="Status atual do pagamento (ex: pago, pendente, cancelado)",
 * example="pago"
 * ),
 * @OA\Property(
 * property="id_pedido",
 * type="integer",
 * format="int64",
 * description="ID do pedido ao qual este pagamento está associado",
 * example=1
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
 * schema="PagamentoCreateRequest",
 * type="object",
 * title="PagamentoCreateRequest",
 * description="Dados necessários para criar um novo pagamento",
 * required={"valor", "data_pagamento", "metodo_pagamento", "status", "id_pedido"},
 * @OA\Property(property="valor", type="number", format="float", example=250.00),
 * @OA\Property(property="data_pagamento", type="string", format="date", example="2025-05-24"),
 * @OA\Property(property="metodo_pagamento", type="string", example="pix"),
 * @OA\Property(property="status", type="string", example="pendente"),
 * @OA\Property(property="id_pedido", type="integer", example=2)
 * )
 *
 * @OA\Schema(
 * schema="PagamentoUpdateRequest",
 * type="object",
 * title="PagamentoUpdateRequest",
 * description="Dados opcionais para atualizar um pagamento existente",
 * @OA\Property(property="valor", type="number", format="float", example=120.75),
 * @OA\Property(property="data_pagamento", type="string", format="date", example="2025-05-25"),
 * @OA\Property(property="metodo_pagamento", type="string", example="boleto"),
 * @OA\Property(property="status", type="string", example="cancelado"),
 * @OA\Property(property="id_pedido", type="integer", example=1)
 * )
 */
class PagamentoSchema
{
    // Esta classe é usada apenas para armazenar as anotações Swagger
}
