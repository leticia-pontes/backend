<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Catalogo extends Model
{
    use hasFactory;

    protected $table = 'catalogos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'arquivo',
        'nome_arquivo',
        'descricao',
        'data_criacao',
        'versao',
        'ativo',
        'id_empresa',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
