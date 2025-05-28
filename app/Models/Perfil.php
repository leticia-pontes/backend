<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasFactory;

    // Define o nome da tabela no banco de dados
    protected $table = 'perfis';

    // Define a chave primária da tabela
    protected $primaryKey = 'id_perfil';

    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'foto',
        'biografia',
        'redes_sociais',
        'seguidores_cache',
        'id_empresa',
        'id_tipo_perfil',
    ];

    // Define os atributos que devem ser convertidos para tipos nativos do PHP
    protected $casts = [
        // 'foto' => 'string', // Se mudar para armazenar URL da imagem
    ];

    // Desabilita os timestamps padrão do Laravel (created_at, updated_at)
    public $timestamps = false;

    // Relacionamento com Empresa (1:1) - Um perfil pertence a uma empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    // Relacionamento com TipoPerfil (N:1) - Um perfil tem um tipo de perfil
    public function tipoPerfil()
    {
        return $this->belongsTo(TipoPerfil::class, 'id_tipo_perfil', 'id_tipo_perfil');
    }
}
