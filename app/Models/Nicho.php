<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nicho extends Model
{
    use HasFactory;

    // Define o nome da tabela no banco de dados
    protected $table = 'nichos';

    // Define a chave primária da tabela
    protected $primaryKey = 'id_nicho';

    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome_nicho',
    ];

    // Desabilita os timestamps padrão do Laravel (created_at, updated_at)
    public $timestamps = false;

    // Relacionamento com Perfil (M:N) - Um nicho pode ter muitos perfis, e um perfil pode ter muitos nichos
    public function perfis()
    {
        return $this->belongsToMany(Empresa::class, 'perfil_nicho', 'id_nicho', 'id_perfil');
    }
}
