<?php

namespace App\Enums;

enum PedidoStatusEnum: string
{
    case Pendente = 'Pendente';
    case Aceito = 'Aceito';
    case EmAndamento = 'Em Andamento';
    case Aguardando = 'Aguardando';
    case Concluido = 'Concluido';
    case Cancelado = 'Cancelado';
}
