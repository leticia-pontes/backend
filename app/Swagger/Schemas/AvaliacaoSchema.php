<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Avaliacao",
 *     title="Avaliacao",
 *     description="Modelo de Avaliação",
 *     @OA\Property(
 *         property="id_avaliacao",
 *         type="integer",
 *         format="int64",
 *         description="ID da avaliação"
 *     ),
 *     @OA\Property(
 *         property="nota",
 *         type="integer",
 *         format="int32",
 *         description="Nota da avaliação (1-5)"
 *     ),
 *     @OA\Property(
 *         property="comentario",
 *         type="string",
 *         description="Comentário da avaliação"
 *     ),
 *     @OA\Property(
 *         property="data_avaliacao",
 *         type="string",
 *         format="date",
 *         description="Data em que a avaliação foi feita"
 *     ),
 *     @OA\Property(
 *         property="id_empresa",
 *         type="integer",
 *         format="int64",
 *         description="ID da empresa que está sendo avaliada"
 *     ),
 *     @OA\Property(
 *         property="id_empresa_avaliadora",
 *         type="integer",
 *         format="int64",
 *         description="ID da empresa que fez a avaliação"
 *     )
 * )
 */
class AvaliacaoSchema
{

}
