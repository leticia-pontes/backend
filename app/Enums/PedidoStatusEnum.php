<?php

namespace App\Enums;

enum PedidoStatusEnum: string
{
    case Pendente = 'pendente';
    case Aceito = 'aceito';
    case EmAndamento = 'em_andamento';
    case Aguardando = 'aguardando';
    case Concluido = 'concluido';
    case Cancelado = 'cancelado';
}
