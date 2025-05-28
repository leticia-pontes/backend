<?php

namespace App\Enums;

enum PedidoStatusEnum: string
{
    case Aguardando = 'aguardando';
    case Aceito = 'aceito';
    case EmAndamento = 'em_andamento';
    case Concluido = 'concluido';
    case Cancelado = 'cancelado';
}
