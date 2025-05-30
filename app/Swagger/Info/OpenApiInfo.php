<?php

namespace App\Swagger\Info;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="API (backend)",
 * description="API para gerenciamento de pedidos, incluindo funcionalidades de gamificação."
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT"
 * )
 */
class OpenApiInfo
{
}

// @OA\Server(
// url=L5_SWAGGER_CONST_HOST,
// description="Servidor da API"
// )
