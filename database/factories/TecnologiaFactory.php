<?php

namespace Database\Factories;

use App\Models\Tecnologia;
use Illuminate\Database\Eloquent\Factories\Factory;

class TecnologiaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tecnologia::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nome_tecnologia' => $this->faker->unique()->word,
        ];
    }
}
