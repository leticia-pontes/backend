<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'avaliacoes';
    protected $primaryKey = 'id_avaliacao';
    public $timestamps = false;

    protected $fillable = [
        'nota',
        'comentario',
        'data_avaliacao',
        'id_empresa_avaliador',
        'id_empresa_avaliado',
    ];

    protected $casts = [
        'data_avaliacao' => 'datetime',
        'nota' => 'integer', // ou 'decimal:1' se mudar o tipo no DB
    ];

    // Relacionamento com a Empresa Avaliadora
    public function avaliador()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa_avaliador', 'id_empresa');
    }

    // Relacionamento com a Empresa Avaliada
    public function avaliado()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa_avaliado', 'id_empresa');
    }
}
