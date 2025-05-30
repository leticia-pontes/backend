<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoGamificacao extends Model
{
    use HasFactory;

    protected $table = 'configuracao_gamificacao';
    protected $primaryKey = 'id_configuracao_gamificacao';

    protected $fillable = [
        'chave',
        'descricao',
        'valor_tipo',
        'valor',
    ];

    /**
     * Acessor para retornar o valor no tipo correto.
     *
     */
    public function getValorConvertidoAttribute()
    {
        switch ($this->valor_tipo) {
            case 'int':
                return (int) $this->valor;
            case 'float':
                return (float) $this->valor;
            case 'boolean':
                return (bool) $this->valor;
            case 'string':
            default:
                return $this->valor;
        }
    }

    /**
     * Método estático para buscar uma configuração pelo nome da chave.
     * Retorna o valor já convertido.
     *
     * @param string $chave
     * @param mixed $default
     * @return mixed
     */
    public static function getValor(string $chave, $default = null)
    {
        $config = self::where('chave', $chave)->first();
        return $config ? $config->valor_convertido : $default;
    }
}
