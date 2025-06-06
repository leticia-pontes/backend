<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Nicho",
 *     type="object",
 *     title="Nicho",
 *     description="Modelo de Nicho",
 *     required={"id_nicho", "nome_nicho"},
 *     @OA\Property(
 *         property="id_nicho",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="nome_nicho",
 *         type="string",
 *         example="Tecnologia"
 *     )
 * )
 */

class NichoSchema
{
    // Esta classe serve apenas como um marcador para a documentação OpenAPI.
    // Não contém lógica de aplicação, apenas define o esquema do modelo Nicho.
}
