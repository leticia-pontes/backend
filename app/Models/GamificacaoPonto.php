<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamificacaoPonto extends Model
{
    protected $table = 'gamificacao_pontos';

    protected $fillable = [
        'id_empresa', 'tipo', 'pontos', 'nivel'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
