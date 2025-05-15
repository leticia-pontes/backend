<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    protected $table = 'Catalogo';
    protected $primaryKey = 'id_catalogo';
    public $timestamps = false;

    protected $fillable = [
        'arquivo', 'nome_arquivo', 'descricao', 'data_criacao', 'versao', 'ativo', 'id_empresa'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
