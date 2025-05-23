<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'data_pedido', 'id_empresa', 'descricao', 'valor', 'prazo', 'desenvolvedor_id',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function status()
    {
        return $this->hasMany(PedidoStatus::class, 'id_pedido');
    }

    public function statusAtual()
    {
        return $this->status()->orderBy('data_status', 'desc')->first();
    }

    public function pagamento()
    {
        return $this->hasOne(Pagamento::class, 'id_pedido');
    }
}
