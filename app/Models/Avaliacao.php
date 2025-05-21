<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    protected $table = 'avaliacoes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nota', 'comentario', 'data_avaliacao', 'id_empresa'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
