<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Empresa; // Certifique-se de importar o model Empresa
use App\Models\Avaliacao; // Certifique-se de importar o model Avaliacao
use Illuminate\Testing\Fluent\AssertableJson; // Para assertJson
use Carbon\Carbon; // Para auxiliar com datas nos dados de teste

class AvaliacaoTest extends TestCase
{
    use RefreshDatabase; // Garante um banco de dados limpo para cada teste

    /** @test */
    public function test_list_avaliacoes()
    {
        // Cria algumas empresas e avaliações para popular o banco
        $empresa1 = Empresa::factory()->create();
        $empresa2 = Empresa::factory()->create();
        $empresa3 = Empresa::factory()->create();

        Avaliacao::factory()->count(3)->create([
            'id_empresa_avaliadora' => $empresa1->id_empresa,
            'id_empresa_avaliada' => $empresa2->id_empresa,
        ]);
        Avaliacao::factory()->count(2)->create([
            'id_empresa_avaliadora' => $empresa3->id_empresa,
            'id_empresa_avaliada' => $empresa1->id_empresa,
        ]);

        // Autentica uma empresa para acessar a rota (se necessário)
        $this->actingAs($empresa1, 'sanctum');

        $response = $this->getJson('/api/avaliacoes');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data') // Verifica se 5 avaliações foram retornadas na chave 'data'
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [ // '*' para cada item no array
                             'id_avaliacao',
                             'nota',
                             'comentario',
                             'data_avaliacao',
                             'id_empresa_avaliadora',
                             'id_empresa_avaliada',
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function test_show_avaliacao()
    {
        // Cria uma avaliação específica
        $empresaAvaliador = Empresa::factory()->create();
        $empresaAvaliada = Empresa::factory()->create();
        $avaliacao = Avaliacao::factory()->create([
            'id_empresa_avaliadora' => $empresaAvaliador->id_empresa,
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
            'comentario' => 'Teste de exibição de avaliação',
        ]);

        // Autentica uma empresa para acessar a rota
        $this->actingAs($empresaAvaliador, 'sanctum');

        $response = $this->getJson("/api/avaliacoes/{$avaliacao->id_avaliacao}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id_avaliacao' => $avaliacao->id_avaliacao,
                     'nota' => $avaliacao->nota,
                     'comentario' => 'Teste de exibição de avaliação',
                     'id_empresa_avaliadora' => $empresaAvaliador->id_empresa,
                     'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
                 ]);
    }

    /** @test */
    public function test_show_avaliacao_not_found()
    {
        // Autentica uma empresa para simular a requisição
        $empresa = Empresa::factory()->create();
        $this->actingAs($empresa, 'sanctum');

        // Tenta buscar uma avaliação que não existe
        $response = $this->getJson('/api/avaliacoes/999'); // ID que certamente não existe

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Avaliação não encontrada.']);
    }

    /** @test */
    public function test_create_avaliacao_authenticated()
    {
        // 1. Crie uma empresa avaliadora (que estará autenticada)
        $empresaAvaliador = Empresa::factory()->create();

        // 2. Crie uma segunda empresa para ser a empresa avaliada
        $empresaAvaliada = Empresa::factory()->create();

        // 3. Autentique a empresa avaliadora
        $this->actingAs($empresaAvaliador, 'sanctum');

        // 4. Dados para a nova avaliação
        $data = [
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
            'nota' => 4,
            'comentario' => 'Ótima parceria, recomendo!',
        ];

        // 5. Envie a requisição
        $response = $this->postJson('/api/avaliacoes', $data);

        // 6. Afirme a resposta
        $response->assertStatus(201)
                 ->assertJsonFragment(['comentario' => 'Ótima parceria, recomendo!'])
                 ->assertJson(fn (AssertableJson $json) =>
                     $json->has('id_avaliacao') // Verifica se o ID foi retornado
                          ->where('id_empresa_avaliadora', $empresaAvaliador->id_empresa)
                          ->where('id_empresa_avaliada', $empresaAvaliada->id_empresa)
                          ->where('nota', 4)
                          ->etc() // Permite outros campos no JSON de resposta
                 );

        // 7. Verifique se a avaliação foi realmente criada no banco de dados
        $this->assertDatabaseHas('avaliacoes', [
            'id_empresa_avaliadora' => $empresaAvaliador->id_empresa,
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
            'nota' => 4,
            'comentario' => 'Ótima parceria, recomendo!',
        ]);
    }

    /** @test */
    public function test_create_avaliacao_unauthenticated()
    {
        // Tenta criar uma avaliação sem autenticação
        $empresaAvaliada = Empresa::factory()->create();
        $data = [
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
            'nota' => 5,
            'comentario' => 'Teste não autenticado',
        ];

        $response = $this->postJson('/api/avaliacoes', $data);

        $response->assertStatus(401) // 401 Unauthorized
                 ->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function test_create_avaliacao_invalid_data()
    {
        $empresaAvaliador = Empresa::factory()->create();
        $this->actingAs($empresaAvaliador, 'sanctum');

        // Dados inválidos (nota fora do range, id_empresa_avaliada inexistente)
        $data = [
            'id_empresa_avaliada' => 99999, // ID de empresa que não existe
            'nota' => 0, // Nota inválida
            'comentario' => str_repeat('a', 600), // Comentário muito longo
        ];

        $response = $this->postJson('/api/avaliacoes', $data);

        $response->assertStatus(422) // 422 Unprocessable Entity (erro de validação)
                 ->assertJsonValidationErrors(['id_empresa_avaliada', 'nota', 'comentario']);
    }

    /** @test */
    public function test_create_avaliacao_same_avaliador_and_avaliado()
    {
        $empresa = Empresa::factory()->create();
        $this->actingAs($empresa, 'sanctum');

        // Tenta avaliar a si mesma
        $data = [
            'id_empresa_avaliada' => $empresa->id_empresa, // A empresa avalia a si mesma
            'nota' => 3,
            'comentario' => 'Tentativa de auto-avaliação',
        ];

        $response = $this->postJson('/api/avaliacoes', $data);

        $response->assertStatus(400) // 400 Bad Request
                 ->assertJson(['message' => 'Você não pode avaliar a si mesmo.']);
    }

    /** @test */
    public function test_create_avaliacao_duplicate()
    {
        $empresaAvaliador = Empresa::factory()->create();
        $empresaAvaliada = Empresa::factory()->create();
        $this->actingAs($empresaAvaliador, 'sanctum');

        // Crie a primeira avaliação
        Avaliacao::create([
            'id_empresa_avaliadora' => $empresaAvaliador->id_empresa,
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
            'nota' => 5,
            'comentario' => 'Primeira avaliação',
            'data_avaliacao' => Carbon::now()->toDateString(),
        ]);

        // Tente criar uma avaliação duplicada
        $data = [
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
            'nota' => 3,
            'comentario' => 'Segunda avaliação',
        ];

        $response = $this->postJson('/api/avaliacoes', $data);

        $response->assertStatus(409) // 409 Conflict para duplicado
                 ->assertJson(['message' => 'Você já avaliou esta empresa.']);
    }

    /** @test */
    public function test_update_avaliacao_authenticated_as_owner()
    {
        $empresaAvaliador = Empresa::factory()->create();
        $empresaAvaliada = Empresa::factory()->create();
        $this->actingAs($empresaAvaliador, 'sanctum');

        $avaliacao = Avaliacao::create([
            'id_empresa_avaliadora' => $empresaAvaliador->id_empresa,
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
            'nota' => 3,
            'comentario' => 'Comentário inicial',
            'data_avaliacao' => Carbon::now()->toDateString(),
        ]);

        $updatedData = [
            'nota' => 5,
            'comentario' => 'Comentário atualizado!',
        ];

        $response = $this->putJson("/api/avaliacoes/{$avaliacao->id_avaliacao}", $updatedData);

        $response->assertStatus(200) // 200 OK para atualização bem-sucedida
                 ->assertJsonFragment(['comentario' => 'Comentário atualizado!']);

        // Verifique se o banco de dados foi realmente atualizado
        $this->assertDatabaseHas('avaliacoes', [
            'id_avaliacao' => $avaliacao->id_avaliacao,
            'nota' => 5,
            'comentario' => 'Comentário atualizado!',
        ]);
    }

    /** @test */
    public function test_update_avaliacao_unauthenticated()
    {
        $empresaAvaliador = Empresa::factory()->create();
        $empresaAvaliada = Empresa::factory()->create();
        $avaliacao = Avaliacao::factory()->create([
            'id_empresa_avaliadora' => $empresaAvaliador->id_empresa,
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
        ]);

        $updatedData = [
            'nota' => 1,
        ];

        $response = $this->putJson("/api/avaliacoes/{$avaliacao->id_avaliacao}", $updatedData);

        $response->assertStatus(401) // 401 Unauthorized
                 ->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function test_update_avaliacao_unauthorized_different_company()
    {
        // Empresa que criou a avaliação
        $empresaOriginalAvaliador = Empresa::factory()->create();
        $empresaAvaliada = Empresa::factory()->create();
        $avaliacao = Avaliacao::factory()->create([
            'id_empresa_avaliadora' => $empresaOriginalAvaliador->id_empresa,
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
        ]);

        // Outra empresa tenta atualizar (não autorizada)
        $outraEmpresa = Empresa::factory()->create();
        $this->actingAs($outraEmpresa, 'sanctum');

        $updatedData = [
            'nota' => 2,
        ];

        $response = $this->putJson("/api/avaliacoes/{$avaliacao->id_avaliacao}", $updatedData);

        $response->assertStatus(403) // 403 Forbidden (autorização falhou)
                 ->assertJson(['message' => 'Você não tem permissão para atualizar esta avaliação.']);
    }

    /** @test */
    public function test_update_avaliacao_not_found()
    {
        $empresaAvaliador = Empresa::factory()->create();
        $this->actingAs($empresaAvaliador, 'sanctum');

        $updatedData = [
            'nota' => 5,
            'comentario' => 'Tentativa de atualização',
        ];

        $response = $this->putJson('/api/avaliacoes/999', $updatedData); // ID que não existe

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Avaliação não encontrada.']);
    }

    /** @test */
    public function test_destroy_avaliacao_authenticated_as_owner()
    {
        $empresaAvaliador = Empresa::factory()->create();
        $empresaAvaliada = Empresa::factory()->create();
        $this->actingAs($empresaAvaliador, 'sanctum');

        $avaliacao = Avaliacao::create([
            'id_empresa_avaliadora' => $empresaAvaliador->id_empresa,
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
            'nota' => 3,
            'comentario' => 'Comentário para deletar',
            'data_avaliacao' => Carbon::now()->toDateString(),
        ]);

        $response = $this->deleteJson("/api/avaliacoes/{$avaliacao->id_avaliacao}");

        $response->assertStatus(204); // 204 No Content para deleção bem-sucedida

        // Verifique se a avaliação foi removida do banco de dados
        $this->assertDatabaseMissing('avaliacoes', [
            'id_avaliacao' => $avaliacao->id_avaliacao,
        ]);
    }

    /** @test */
    public function test_destroy_avaliacao_unauthenticated()
    {
        $empresaAvaliador = Empresa::factory()->create();
        $empresaAvaliada = Empresa::factory()->create();
        $avaliacao = Avaliacao::factory()->create([
            'id_empresa_avaliadora' => $empresaAvaliador->id_empresa,
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
        ]);

        $response = $this->deleteJson("/api/avaliacoes/{$avaliacao->id_avaliacao}");

        $response->assertStatus(401) // 401 Unauthorized
                 ->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function test_destroy_avaliacao_unauthorized_different_company()
    {
        $empresaOriginalAvaliador = Empresa::factory()->create();
        $empresaAvaliada = Empresa::factory()->create();
        $avaliacao = Avaliacao::factory()->create([
            'id_empresa_avaliadora' => $empresaOriginalAvaliador->id_empresa,
            'id_empresa_avaliada' => $empresaAvaliada->id_empresa,
        ]);

        $outraEmpresa = Empresa::factory()->create();
        $this->actingAs($outraEmpresa, 'sanctum'); // Outra empresa tenta deletar

        $response = $this->deleteJson("/api/avaliacoes/{$avaliacao->id_avaliacao}");

        $response->assertStatus(403) // 403 Forbidden
                 ->assertJson(['message' => 'Você não tem permissão para excluir esta avaliação.']);
    }

    /** @test */
    public function test_destroy_avaliacao_not_found()
    {
        $empresaAvaliador = Empresa::factory()->create();
        $this->actingAs($empresaAvaliador, 'sanctum');

        $response = $this->deleteJson('/api/avaliacoes/999'); // ID que não existe

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Avaliação não encontrada.']);
    }
}
