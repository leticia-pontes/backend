<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracaoGamificacao extends Model
{
    protected $table = 'configuracoes_gamificacao';

    protected $fillable = ['chave', 'valor'];

    public static function getValor(string $chave, $default = 0)
    {
        return static::where('chave', $chave)->value('valor') ?? $default;
    }
}
