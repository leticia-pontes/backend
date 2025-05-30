<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Distintivo extends Model
{
    use HasFactory;

    protected $table = 'distintivos';

    protected $primaryKey = 'id_distintivo';

    protected $fillable = [
        'titulo',
        'descricao',
        'icone',
        'pontos_necessarios',
        'condicao_especifica',
    ];

    /**
     * The empresas that have unlocked this Distintivo.
     */
    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_distintivo', 'distintivo_id', 'empresa_id')
                    ->withPivot('data_conquista')
                    ->withTimestamps();
    }
}
