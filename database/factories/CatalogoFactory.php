<?php

namespace Database\Factories;

use App\Models\Catalogo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CatalogoFactory extends Factory
{
    protected $model = Catalogo::class;

    public function definition(): array
    {
        return [
            'arquivo' => $this->faker->filePath(),
            'nome_arquivo' => $this->faker->word() . ".pdf",
            'descricao' => $this->faker->sentence(),
            'data_criacao' => $this->faker->dateTime(),
            'versao' => $this->faker->randomFloat(1, 1.0, 10.0),
            'ativo' => $this->faker->boolean(),
            'id_empresa' => \App\Models\Empresa::factory(),
        ];
    }
}
