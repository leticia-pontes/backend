<?php

namespace Database\Factories;

use App\Models\Avaliacao;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvaliacaoFactory extends Factory
{
    protected $model = Avaliacao::class;

    public function definition(): array
    {
        return [
            'nota' => $this->faker->numberBetween(1, 5),
            'comentario' => $this->faker->sentence(),
            'data_avaliacao' => $this->faker->date(),
            'id_empresa' => Empresa::factory(),
        ];
    }
}
