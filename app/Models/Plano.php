<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    use HasFactory;

    protected $table = 'planos';
    protected $primaryKey = 'id_plano';
    protected $fillable = [
        'nome_plano',
        'valor',
        'descricao',
        'data_criacao',
    ];
    public $timestamps = false;

    // Relacionamento com EmpresaPlano (1:N) - Um plano pode ser assinado por muitas empresas
    public function empresasAssinantes()
    {
        return $this->hasMany(EmpresaPlano::class, 'id_plano', 'id_plano');
    }
}
