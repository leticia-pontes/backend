<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
{
    use HasFactory;

    protected $table = 'projetos';
    protected $primaryKey = 'id_projeto';

    protected $fillable = [
        'nome_projeto',
        'descricao',
        'data_inicio',
        'data_fim',
        'status',
        'url_projeto',
        'imagem_destaque_url',
        'id_empresa',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    // Esta tabela usará os timestamps padrão do Laravel (created_at, updated_at)
    // public $timestamps = true; // Este é o padrão, pode ser omitido

    // Relacionamento com Empresa (N:1) - Um projeto pertence a uma empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    // Uso futuro, NÃO APAGAR
    // public function tecnologias() { return $this->belongsToMany(Tecnologia::class, 'projeto_tecnologia', 'id_projeto', 'id_tecnologia'); }
    // public function cliente() { return $this->belongsTo(Empresa::class, 'id_cliente', 'id_empresa'); }
}
