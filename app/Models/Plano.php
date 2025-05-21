<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    protected $table = 'planos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nome_plano',
        'valor',
        'descricao',
        'data_criacao',
        'id_empresa',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
