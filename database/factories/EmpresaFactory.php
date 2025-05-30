<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon; // Importe Carbon

class EmpresaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Empresa::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nome' => $this->faker->company,
            'cnpj' => $this->faker->unique()->numerify('##############'), // CNPJ com 14 dígitos
            'email' => $this->faker->unique()->safeEmail,
            'senha' => Hash::make('password'), // Senha padrão para testes
            'telefone' => $this->faker->phoneNumber,
            'endereco' => $this->faker->address,
            'data_cadastro' => Carbon::now(),
            'nivel' => $this->faker->numberBetween(1, 10),
            'pontos' => $this->faker->randomNumber(4),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Empresa $empresa) {
            // Ao criar uma empresa, cria também um Perfil associado
            $tipoPerfil = \App\Models\TipoPerfil::firstOrCreate(['nome_tipo' => 'Contratante']);

            \App\Models\Perfil::factory()->create([
                'id_empresa' => $empresa->id_empresa,
                'id_tipo_perfil' => $tipoPerfil->id_tipo_perfil,
            ]);
        });
    }
}
