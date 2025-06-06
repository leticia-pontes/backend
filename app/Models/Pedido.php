<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';
    protected $primaryKey = 'id_pedido';

    protected $fillable = [
        'id_empresa_contratante',
        'id_empresa_desenvolvedora',
        'titulo',
        'descricao',
        'valor_estimado',
        'data_prazo',
        'data_pedido',
    ];

    protected $casts = [
        'data_prazo' => 'date',
        'data_pedido' => 'date',
    ];

    // Relacionamento com a Empresa Contratante
    public function contratante()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa_contratante', 'id_empresa');
    }

    // Relacionamento com a Empresa Desenvolvedora (pode ser nula)
    public function desenvolvedora()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa_desenvolvedora', 'id_empresa');
    }

    // Relacionamento com PedidoStatus (1:N) - Um pedido pode ter vários status ao longo do tempo
    public function statusHistorico()
    {
        return $this->hasMany(PedidoStatus::class, 'id_pedido', 'id_pedido');
    }

    public function current_status()
    {
        return $this->hasOne(PedidoStatus::class, 'id_pedido', 'id_pedido')
                    ->latestOfMany('data_status');
    }
}
