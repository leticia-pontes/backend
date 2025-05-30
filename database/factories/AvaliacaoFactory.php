<?php

namespace Database\Factories;

use App\Models\Avaliacao;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AvaliacaoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Avaliacao::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // id_empresa_avaliadora deve ser uma ID de uma Empresa existente
            // Usamos Empresa::factory() para criar uma empresa se uma não for explicitamente fornecida
            'id_empresa_avaliadora' => Empresa::factory(),

            // id_empresa_avaliada deve ser uma ID de uma Empresa existente
            // E é importante que não seja a mesma empresa que o avaliador
            'id_empresa_avaliada' => Empresa::factory(),

            'nota' => $this->faker->numberBetween(1, 5), // Nota entre 1 e 5
            'comentario' => $this->faker->paragraph,
            'data_avaliacao' => Carbon::now()->subDays($this->faker->numberBetween(1, 365)), // Data aleatória no último ano
        ];
    }

    /**
     * Configura a factory para garantir que avaliador e avaliado sejam diferentes.
     * Use essa trait/método ao usar esta factory em testes.
     * Ex: Avaliacao::factory()->forUniqueCompanies()->create();
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forUniqueCompanies()
    {
        return $this->state(function (array $attributes) {
            $avaliador = Empresa::factory()->create();
            $avaliado = Empresa::factory()->create();

            // Garante que o avaliador e o avaliado são diferentes
            while ($avaliador->id_empresa === $avaliado->id_empresa) {
                $avaliado = Empresa::factory()->create();
            }

            return [
                'id_empresa_avaliadora' => $avaliador->id_empresa,
                'id_empresa_avaliada' => $avaliado->id_empresa,
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
        return $this->afterMaking(function (Avaliacao $avaliacao) {
            // Verifica se as IDs de avaliador e avaliado são as mesmas.
            // Se sim, cria um novo avaliado para garantir que sejam diferentes.
            // Isso é um fallback se for usado sem 'forUniqueCompanies'.
            if (isset($avaliacao->id_empresa_avaliadora) && isset($avaliacao->id_empresa_avaliada) &&
                $avaliacao->id_empresa_avaliadora === $avaliacao->id_empresa_avaliada) {
                $avaliacao->id_empresa_avaliada = Empresa::factory()->create()->id_empresa;
            }
        });
    }
}
