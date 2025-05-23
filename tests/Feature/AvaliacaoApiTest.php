<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Avaliacao;
use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AvaliacaoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_avaliacoes()
    {
        $empresa = Empresa::factory()->create();

        Avaliacao::factory()->count(3)->create([
            'id_empresa' => $empresa->id,
        ]);

        $response = $this->getJson('/api/avaliacoes');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_create_avaliacao()
    {
        $empresa = Empresa::factory()->create();

        $data = [
            'nota' => 4,
            'comentario' => 'Teste de avaliação',
            'data_avaliacao' => '2025-05-23',
            'id_empresa' => $empresa->id,
        ];

        $response = $this->postJson('/api/avaliacoes', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['comentario' => 'Teste de avaliação']);

        $this->assertDatabaseHas('avaliacoes', $data);
    }
}
