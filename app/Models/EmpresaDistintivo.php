<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmpresaDistintivo extends Pivot
{
    protected $table = 'empresa_distintivos';

    protected $fillable = [
        'id_empresa', 'id_distintivo', 'data_conquista'
    ];

    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function distintivo()
    {
        return $this->belongsTo(GamificacaoDistintivo::class, 'id_distintivo');
    }
}
