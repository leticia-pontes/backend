<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Empresa extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    // Define o nome da tabela no banco de dados
    protected $table = 'empresas';

    // Define a chave primária da tabela
    protected $primaryKey = 'id_empresa';

    // Indica que a chave primária não é um inteiro auto-incrementável (false se não for, mas aqui é)
    // public $incrementing = true; // Valor padrão, pode ser omitido

    // Define os campos que podem ser preenchidos em massa (mass assignable)
    protected $fillable = [
        'nome',
        'cnpj',
        'email',
        'senha',
        'telefone',
        'endereco',
        'data_cadastro',
        'nivel',
        'pontos',
    ];

    public function getAuthPassword()
    {
        return $this->senha;
    }

    // Define os campos que devem ser ocultados ao serializar o modelo para arrays/JSON (ex: senha)
    protected $hidden = [
        'senha',
    ];

    // Define os atributos que devem ser convertidos para tipos nativos do PHP
    protected $casts = [
        'data_cadastro' => 'datetime',
        'redes_sociais' => 'json',
    ];

    // Como já estamos usando 'data_cadastro' e não 'created_at'/'updated_at', desabilitamos os timestamps padrão do Laravel
    public $timestamps = false;

    public function tipoPerfil()
    {
        return $this->belongsTo(TipoPerfil::class, 'id_tipo_perfil', 'id_tipo_perfil');
    }

    // Relacionamento com Perfil (1:1)
    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'id_empresa', 'id_empresa');
    }

    // Relacionamento com Empresa_Nicho (M:N)
    public function nichos()
    {
        return $this->belongsToMany(Nicho::class, 'empresa_nicho', 'id_empresa', 'id_nicho');
    }

    // Relacionamento com Empresa_Tecnologia (M:N)
    public function tecnologias()
    {
        return $this->belongsToMany(Tecnologia::class, 'empresa_tecnologia', 'id_empresa', 'id_tecnologia');
    }

    // Relacionamento para saber quem a empresa segue (M:N através da tabela Seguidor)
    public function seguindo()
    {
        return $this->belongsToMany(Empresa::class, 'seguidor', 'id_empresa_seguidor', 'id_empresa_seguido');
    }

    // Relacionamento para saber quem segue a empresa (seus seguidores) (M:N através da tabela Seguidor)
    public function seguidores()
    {
        return $this->belongsToMany(Empresa::class, 'seguidor', 'id_empresa_seguido', 'id_empresa_seguidor');
    }

    // Relacionamento com Empresa_Plano (1:N)
    public function planosAssinados()
    {
        return $this->hasMany(EmpresaPlano::class, 'id_empresa', 'id_empresa');
    }

    // Relacionamento com Projeto (1:N)
    public function projetos()
    {
        return $this->hasMany(Projeto::class, 'id_empresa', 'id_empresa');
    }

    // Relacionamento com Pedido (Empresa como solicitante)
    public function pedidosSolicitados()
    {
        return $this->hasMany(Pedido::class, 'id_empresa_solicitante', 'id_empresa');
    }

    // Relacionamento com Pedido (Empresa como prestadora)
    public function pedidosPrestados()
    {
        return $this->hasMany(Pedido::class, 'id_empresa_prestadora', 'id_empresa');
    }

    // Relacionamento com Avaliacao (Empresa que avaliou)
    public function avaliacoesFeitas()
    {
        return $this->hasMany(Avaliacao::class, 'id_empresa_avaliador', 'id_empresa');
    }

    // Relacionamento com Avaliacao (Empresa que foi avaliada)
    public function avaliacoesRecebidas()
    {
        return $this->hasMany(Avaliacao::class, 'id_empresa_avaliada', 'id_empresa');
    }

    // Relacionamento com Gamificacao_Log (1:N)
    public function gamificacaoLogs()
    {
        return $this->hasMany(GamificacaoLog::class, 'id_empresa', 'id_empresa');
    }

    // Relacionamento com Empresa_Distintivo (M:N)
    public function distintivos()
    {
        return $this->belongsToMany(Distintivo::class, 'empresa_distintivo', 'empresa_id', 'distintivo_id')
                    ->withPivot('data_conquista')
                    ->withTimestamps();
    }
}
