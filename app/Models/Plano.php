<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    protected $table = 'Plano';
    protected $primaryKey = 'id_plano';
    public $timestamps = false;

    protected $fillable = [
        'nome_plano', 'valor', 'descricao', 'data_criacao', 'id'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id');
    }
}
