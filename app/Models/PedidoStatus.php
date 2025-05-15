<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoStatus extends Model
{
    protected $table = 'Pedido_Status';
    protected $primaryKey = 'id_pedido_status';
    public $timestamps = false;

    protected $fillable = [
        'status', 'data_status', 'id_pedido'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}
