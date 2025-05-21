<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    protected $table = 'pagamentos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'valor', 'data_pagamento', 'metodo_pagamento', 'status', 'id_pedido'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}
