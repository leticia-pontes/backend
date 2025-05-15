<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    protected $table = 'Avaliacao';
    protected $primaryKey = 'id_avaliacao';
    public $timestamps = false;

    protected $fillable = [
        'nota', 'comentario', 'data_avaliacao', 'id_empresa'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
