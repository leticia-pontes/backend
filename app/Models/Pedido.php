<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'Pedido';
    protected $primaryKey = 'id_pedido';
    public $timestamps = false;

    protected $fillable = [
        'data_pedido', 'id_empresa'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function status()
    {
        return $this->hasMany(PedidoStatus::class, 'id_pedido');
    }

    public function pagamento()
    {
        return $this->hasOne(Pagamento::class, 'id_pedido');
    }
}
