<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaPlano extends Model
{
    use HasFactory;

    protected $table = 'empresa_planos';
    protected $primaryKey = 'id_empresa_plano';
    public $timestamps = false; // Desabilita created_at e updated_at

    protected $fillable = [
        'id_empresa',
        'id_plano',
        'data_inicio',
        'data_fim',
        'ativo',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ativo' => 'boolean',
    ];

    // Relacionamento com Empresa (N:1) - Uma assinatura pertence a uma empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    // Relacionamento com Plano (N:1) - Uma assinatura refere-se a um plano
    public function plano()
    {
        return $this->belongsTo(Plano::class, 'id_plano', 'id_plano');
    }
}
