<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmpresaFactory extends Factory
{
    protected $model = Empresa::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->company,
            'cnpj' => $this->faker->unique()->numerify('##############'),
            'perfil' => 'empresa',
            'seguidores' => $this->faker->numberBetween(0, 1000),
            'email' => $this->faker->unique()->safeEmail,
            'senha' => 'password', // será automaticamente criptografada pelo setSenhaAttribute
            'telefone' => $this->faker->phoneNumber,
            'endereco' => $this->faker->address,
            'nivel' => $this->faker->numberBetween(1, 5),
            'pontos' => $this->faker->numberBetween(0, 500),
        ];
    }
}
