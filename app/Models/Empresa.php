<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'Empresa';
    protected $primaryKey = 'id_empresa';
    public $timestamps = false;

    protected $fillable = [
        'nome', 'cnpj', 'perfil', 'seguidores', 'email', 'senha', 'telefone', 'endereco',
    ];

    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'id_empresa');
    }

    public function planos()
    {
        return $this->hasMany(Plano::class, 'id_empresa');
    }

    public function catalogos()
    {
        return $this->hasMany(Catalogo::class, 'id_empresa');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_empresa');
    }

    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class, 'id_empresa');
    }
}
