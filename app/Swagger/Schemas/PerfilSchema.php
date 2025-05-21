<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Perfil",
 *     title="Perfil",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="foto", type="string", example="base64stringouurl.jpg"),
 *     @OA\Property(property="biografia", type="string", example="Desenvolvedor de software apaixonado."),
 *     @OA\Property(property="nicho_mercado", type="string", example="Educação"),
 *     @OA\Property(property="tecnologia", type="string", example="Laravel"),
 *     @OA\Property(property="redes_sociais", type="string", example="{'linkedin': '/meuLinkedin'}"),
 *     @OA\Property(property="id_empresa", type="integer", example=1),
 * )
 *
 * @OA\Schema(
 *     schema="PerfilCreate",
 *     required={"id_empresa"},
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Perfil")
 *     }
 * )
 */
class PerfilSchema {}
