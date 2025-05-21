<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Empresa extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'Empresa';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nome', 'cnpj', 'perfil', 'seguidores', 'email', 'senha', 'telefone', 'endereco',
    ];

    protected $hidden = [
        'senha', // oculta a senha nas respostas JSON
    ];

    /**
     * Hash automático ao definir a senha
     */
    public function setSenhaAttribute($value)
    {
        $this->attributes['senha'] = bcrypt($value);
    }

    /**
     * Retorna a senha para o sistema de autenticação
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // === RELACIONAMENTOS ===

    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'id');
    }

    public function planos()
    {
        return $this->hasMany(Plano::class, 'id');
    }

    public function catalogos()
    {
        return $this->hasMany(Catalogo::class, 'id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id');
    }

    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class, 'id');
    }
}
