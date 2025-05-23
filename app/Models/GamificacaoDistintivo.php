<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamificacaoDistintivo extends Model
{
    protected $table = 'gamificacao_distintivos';

    protected $fillable = [
        'nome', 'descricao', 'icone', 'requisito_pontos'
    ];

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_distintivos', 'id_distintivo', 'id_empresa')
                    ->withPivot('data_conquista')
                    ->withTimestamps();
    }
}
