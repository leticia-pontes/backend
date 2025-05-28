<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tecnologia extends Model
{
    use HasFactory;

    // Define o nome da tabela no banco de dados
    protected $table = 'tecnologias';

    // Define a chave primária da tabela
    protected $primaryKey = 'id_tecnologia';

    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome_tecnologia',
    ];

    // Desabilita os timestamps padrão do Laravel (created_at, updated_at)
    public $timestamps = false;

    // Relacionamento com Empresa (M:N) - Uma tecnologia pode ser usada por muitas empresas, e uma empresa pode usar muitas tecnologias
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_tecnologia', 'id_tecnologia', 'id_empresa');
    }
}
