<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoStatus extends Model
{
    use HasFactory;

    protected $table = 'pedido_status'; // Nome da tabela no banco
    protected $primaryKey = 'id_pedido_status';
    public $timestamps = false; // Não usaremos created_at e updated_at, já temos data_status

    protected $fillable = [
        'id_pedido',
        'status',
        'observacao',
        'data_status',
    ];

    protected $casts = [
        'data_status' => 'datetime', // Converte para objeto Carbon
    ];

    // Relacionamento com Pedido (N:1) - Um status pertence a um pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }
}
