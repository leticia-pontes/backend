<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\PedidoStatusEnum;

class PedidoStatus extends Model
{
    protected $casts = [
        'status' => PedidoStatusEnum::class,
    ];

    protected $table = 'pedido_status';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'status', 'data_status', 'id_pedido'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}
