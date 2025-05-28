<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPerfil extends Model
{
    use HasFactory;

    // Define o nome da tabela no banco de dados
    protected $table = 'tipo_perfis';

    // Define a chave primária da tabela
    protected $primaryKey = 'id_tipo_perfil';

    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome_tipo',
    ];

    // Desabilita os timestamps padrão do Laravel (created_at, updated_at)
    public $timestamps = false;

    // Relacionamento com Perfil (1:N) - Um tipo de perfil pode ter muitos perfis de empresas
    public function perfis()
    {
        return $this->hasMany(Perfil::class, 'id_tipo_perfil', 'id_tipo_perfil');
    }
}
