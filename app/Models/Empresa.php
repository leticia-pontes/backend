<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Empresa extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'empresas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nome', 'cnpj', 'perfil', 'seguidores', 'email', 'senha', 'telefone', 'endereco',
    ];

    protected $hidden = [
        'senha',
    ];

    public function setSenhaAttribute($value)
    {
        if (!empty($value) && !preg_match('/^\$2y\$/', $value)) {
            $value = bcrypt($value);
        }

        $this->attributes['senha'] = $value;
    }

    public function getAuthPassword()
    {
        return $this->senha;
    }

    // === RELACIONAMENTOS ===

    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'id_empresa', 'id');
    }

    public function planos()
    {
        return $this->hasMany(Plano::class, 'id_empresa', 'id');
    }

    public function catalogos()
    {
        return $this->hasMany(Catalogo::class, 'id_empresa', 'id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_empresa', 'id');
    }

    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class, 'id_empresa', 'id');
    }
}
