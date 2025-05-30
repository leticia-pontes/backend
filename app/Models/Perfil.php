<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Perfil extends Model
{
    use HasFactory;

    protected $table = 'perfis';
    protected $primaryKey = 'id_perfil';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'foto',
        'biografia',
        'redes_sociais',
        'seguidores_cache',
        'id_empresa',
        'id_tipo_perfil',
    ];

    protected $casts = [
        'redes_sociais' => 'array',
    ];

    public $timestamps = false;

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function tipoPerfil(): BelongsTo
    {
        return $this->belongsTo(TipoPerfil::class, 'id_tipo_perfil', 'id_tipo_perfil');
    }

    // Relacionamento Many-to-Many com Nichos
    public function nichos(): BelongsToMany
    {
        return $this->belongsToMany(Nicho::class, 'perfil_nicho', 'perfil_id', 'nicho_id');
    }

    // Relacionamento Many-to-Many com Tecnologias
    public function tecnologias(): BelongsToMany
    {
        return $this->belongsToMany(Tecnologia::class, 'perfil_tecnologia', 'perfil_id', 'tecnologia_id');
    }
}
