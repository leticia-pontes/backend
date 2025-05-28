<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="Projeto",
 * type="object",
 * title="Projeto",
 * description="Detalhes de um Projeto",
 * required={"id", "arquivo", "nome_arquivo", "descricao", "data_criacao", "id_empresa"},
 * @OA\Property(
 * property="id",
 * type="integer",
 * format="int64",
 * description="ID único do projeto",
 * readOnly=true,
 * example=1
 * ),
 * @OA\Property(
 * property="arquivo",
 * type="string",
 * description="Caminho ou URL do arquivo do projeto armazenado (Ex: storage/projetos/documento.pdf)",
 * example="storage/projetos/documento_projeto_final.pdf"
 * ),
 * @OA\Property(
 * property="nome_arquivo",
 * type="string",
 * description="Nome original do arquivo enviado",
 * example="Relatório Anual 2024.pdf"
 * ),
 * @OA\Property(
 * property="descricao",
 * type="string",
 * description="Descrição detalhada do projeto",
 * example="Este projeto abrange a otimização de processos internos e a expansão de mercado para o próximo ano."
 * ),
 * @OA\Property(
 * property="data_criacao",
 * type="string",
 * format="date",
 * description="Data de criação do projeto (formato ISO 8601: YYYY-MM-DD)",
 * example="2025-05-28"
 * ),
 * @OA\Property(
 * property="versao",
 * type="string",
 * description="Versão do projeto (opcional)",
 * example="1.0.1"
 * ),
 * @OA\Property(
 * property="ativo",
 * type="boolean",
 * description="Status de ativação do projeto (true se ativo, false se inativo)",
 * example=true
 * ),
 * @OA\Property(
 * property="id_empresa",
 * type="integer",
 * format="int64",
 * description="ID da empresa à qual o projeto pertence",
 * example=5
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
 */
class ProjetoSchema
{
    // Esta classe é usada apenas para armazenar a anotação Swagger para o modelo principal
}
