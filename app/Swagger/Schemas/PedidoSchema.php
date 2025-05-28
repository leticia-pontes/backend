<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Pedido",
 *     type="object",
 *     title="Pedido",
 *     required={"id", "data_pedido", "descricao", "valor", "prazo", "id_empresa"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="data_pedido", type="string", format="date", example="2025-05-22"),
 *     @OA\Property(property="descricao", type="string", example="Desenvolvimento de site"),
 *     @OA\Property(property="valor", type="number", format="float", example=1500.00),
 *     @OA\Property(property="prazo", type="string", format="date", example="2025-06-22"),
 *     @OA\Property(property="id_empresa", type="integer", example=1),
 *     @OA\Property(property="desenvolvedor_id", type="integer", nullable=true, example=2),
 *     @OA\Property(
 *         property="status",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"status", "data_status"},
 *             @OA\Property(property="status", type="string", example="aguardando"),
 *             @OA\Property(property="data_status", type="string", format="date-time", example="2025-05-22T14:55:00Z")
 *         )
 *     ),
 *     @OA\Property(property="pagamento", type="object", nullable=true,
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="id_pedido", type="integer", example=1),
 *         @OA\Property(property="status", type="string", example="pago"),
 *         @OA\Property(property="valor_pago", type="number", format="float", example=1500.00),
 *         @OA\Property(property="data_pagamento", type="string", format="date-time", example="2025-05-23T10:00:00Z")
 *     )
 * )
 */

class PedidoSchema
{

}
