<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'Perfil';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'foto', 'biografia', 'nicho_mercado', 'tecnologia', 'redes_sociais', 'id'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id');
    }
}
