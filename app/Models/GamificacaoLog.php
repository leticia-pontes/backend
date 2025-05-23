<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamificacaoLog extends Model
{
    protected $table = 'gamificacao_log';

    protected $fillable = [
        'id_empresa', 'tipo', 'evento', 'pontos'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
