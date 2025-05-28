<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;

    protected $table = 'pagamentos';
    protected $primaryKey = 'id_pagamento';

    protected $fillable = [
        'id_empresa_pagadora',
        'valor',
        'data_pagamento',
        'metodo_pagamento',
        'status',
        'referencia_transacao',
        'id_pedido',
        'id_empresa_plano',
    ];

    protected $casts = [
        'data_pagamento' => 'datetime', // Converte para objeto Carbon
        'valor' => 'decimal:2',
    ];

    // Relacionamento com Empresa Pagadora
    public function pagadora()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa_pagadora', 'id_empresa');
    }

    // Relacionamento com Pedido (Opcional)
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    // Relacionamento com EmpresaPlano (Opcional)
    public function empresaPlano()
    {
        return $this->belongsTo(EmpresaPlano::class, 'id_empresa_plano', 'id_empresa_plano');
    }
}
