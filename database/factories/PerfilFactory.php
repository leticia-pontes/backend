<?php

namespace Database\Factories;

use App\Models\Perfil;
use App\Models\Empresa;
use App\Models\TipoPerfil;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerfilFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Perfil::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Garante que TipoPerfil 'Contratante' exista antes de criar um perfil
        $tipoPerfil = TipoPerfil::firstOrCreate(['nome_tipo' => 'Contratante']);

        return [
            'id_empresa' => Empresa::factory(), // Cria uma Empresa se não for passada
            'id_tipo_perfil' => $tipoPerfil->id_tipo_perfil,
            'foto' => $this->faker->imageUrl(),
            'biografia' => $this->faker->paragraph,
            'redes_sociais' => json_encode([
                'instagram' => '@' . $this->faker->userName,
                'linkedin' => 'https://linkedin.com/in/' . $this->faker->slug(),
            ]),
        ];
    }
}
