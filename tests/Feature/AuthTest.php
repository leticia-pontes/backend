<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Empresa;
use App\Models\TipoPerfil;
use App\Models\Nicho;
use App\Models\Tecnologia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Garante que os tipos de perfil existam antes de qualquer teste
        TipoPerfil::firstOrCreate(['nome_tipo' => 'Contratante']);
        TipoPerfil::firstOrCreate(['nome_tipo' => 'Desenvolvedor']);
    }

    /**
     * Testa o login de uma empresa com credenciais válidas.
     *
     * @return void
     */
    public function test_login_empresa_com_credenciais_validas()
    {
        $password = 'senha123';
        $empresa = Empresa::factory()->create([
            'email' => 'teste@empresa.com',
            'senha' => Hash::make($password),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'teste@empresa.com',
            'senha' => $password,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type', 'empresa'])
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('empresa', fn (AssertableJson $json) =>
                        $json->where('id_empresa', $empresa->id_empresa)
                             ->where('email', 'teste@empresa.com')
                             ->missing('senha') // Garante que a senha não é retornada
                             ->etc()
                    )
                    ->etc()
                 );
    }

    /**
     * Testa o login de uma empresa com email inválido.
     *
     * @return void
     */
    public function test_login_empresa_com_email_invalido()
    {
        $password = 'senha123';
        Empresa::factory()->create([
            'email' => 'teste@empresa.com',
            'senha' => Hash::make($password),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'email_inexistente@empresa.com',
            'senha' => $password,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Testa o login de uma empresa com senha inválida.
     *
     * @return void
     */
    public function test_login_empresa_com_senha_invalida()
    {
        $password = 'senha123';
        Empresa::factory()->create([
            'email' => 'teste@empresa.com',
            'senha' => Hash::make($password),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'teste@empresa.com',
            'senha' => 'senha_incorreta',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Testa o registro de uma nova empresa com dados válidos.
     *
     * @return void
     */
    public function test_registro_empresa_com_dados_validos()
    {
        $data = [
            'nome' => $this->faker->company,
            'cnpj' => $this->faker->unique()->numerify('##############'),
            'email' => $this->faker->unique()->safeEmail,
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
            'nome_tipo_perfil' => 'Contratante',
            'telefone' => $this->faker->numerify('###########'),
            'endereco' => $this->faker->address,
            'foto' => $this->faker->imageUrl(),
            'biografia' => $this->faker->sentence,
            'nicho_mercado_nome' => 'Tecnologia',
            'tecnologia_nome' => 'Laravel',
            'redes_sociais' => json_encode(['instagram' => '@empresa', 'linkedin' => '/empresa']),
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'empresa', 'access_token', 'token_type'])
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->where('message', 'Cadastro realizado com sucesso')
                         ->has('empresa', fn (AssertableJson $json) =>
                            $json->where('email', $data['email'])
                                 ->missing('senha')
                                 ->has('perfil.tipo_perfil', fn (AssertableJson $json) =>
                                     $json->where('nome_tipo', $data['nome_tipo_perfil'])
                                          ->etc()
                                 )
                                 ->has('nichos', 1)
                                 ->has('tecnologias', 1)
                                 ->etc()
                         )
                         ->etc()
                 );

        $this->assertDatabaseHas('empresas', [
            'email' => $data['email'],
            'cnpj' => $data['cnpj'],
            'nome' => $data['nome'],
        ]);

        $empresa = Empresa::where('email', $data['email'])->first();
        $this->assertDatabaseHas('perfis', [
            'id_empresa' => $empresa->id_empresa,
            'foto' => $data['foto'],
        ]);
        $this->assertDatabaseHas('nichos', [
            'nome_nicho' => 'Tecnologia',
        ]);
        $this->assertDatabaseHas('tecnologias', [
            'nome_tecnologia' => 'Laravel',
        ]);
    }

    /**
     * Testa o registro de uma empresa com CNPJ duplicado.
     *
     * @return void
     */
    public function test_registro_empresa_com_cnpj_duplicado()
    {
        Empresa::factory()->create(['cnpj' => '12345678901234']);

        $data = [
            'nome' => $this->faker->company,
            'cnpj' => '12345678901234', // CNPJ duplicado
            'email' => $this->faker->unique()->safeEmail,
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
            'nome_tipo_perfil' => 'Contratante',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['cnpj']);
    }

    /**
     * Testa o registro de uma empresa com email duplicado.
     *
     * @return void
     */
    public function test_registro_empresa_com_email_duplicado()
    {
        Empresa::factory()->create(['email' => 'existente@empresa.com']);

        $data = [
            'nome' => $this->faker->company,
            'cnpj' => $this->faker->unique()->numerify('##############'),
            'email' => 'existente@empresa.com', // Email duplicado
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
            'nome_tipo_perfil' => 'Contratante',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Testa o registro de uma empresa com senhas que não coincidem.
     *
     * @return void
     */
    public function test_registro_empresa_com_senhas_nao_coincidentes()
    {
        $data = [
            'nome' => $this->faker->company,
            'cnpj' => $this->faker->unique()->numerify('##############'),
            'email' => $this->faker->unique()->safeEmail,
            'senha' => 'senha123',
            'senha_confirmation' => 'senhas_diferentes',
            'nome_tipo_perfil' => 'Contratante',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['senha']);
    }

    /**
     * Testa o logout de uma empresa autenticada.
     *
     * @return void
     */
    public function test_logout_empresa_autenticada()
    {
        $empresa = Empresa::factory()->create();
        $token = $empresa->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logout realizado com sucesso.']);

        // Tenta usar o token novamente para verificar se foi revogado
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/me'); // Ou qualquer outra rota protegida

        $response->assertStatus(401);
    }

    /**
     * Testa o logout de uma empresa não autenticada.
     *
     * @return void
     */
    public function test_logout_empresa_nao_autenticada()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /**
     * Testa a recuperação dos dados da empresa autenticada.
     *
     * @return void
     */
    public function test_me_empresa_autenticada()
    {
        $empresa = Empresa::factory()->hasPerfil()->create();
        $token = $empresa->createToken('auth_token')->plainTextToken;

        // Anexa nichos e tecnologias para testar o carregamento dos relacionamentos
        $nicho = Nicho::firstOrCreate(['nome_nicho' => 'Marketing']);
        $tecnologia = Tecnologia::firstOrCreate(['nome_tecnologia' => 'Vue.js']);
        $empresa->nichos()->attach($nicho->id_nicho);
        $empresa->tecnologias()->attach($tecnologia->id_tecnologia);

        // Cria uma empresa que segue a empresa autenticada
        $seguidor = Empresa::factory()->create();
        $seguidor->seguindo()->attach($empresa->id_empresa);

        // Cria uma empresa que a empresa autenticada segue
        $seguida = Empresa::factory()->create();
        $empresa->seguindo()->attach($seguida->id_empresa);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/me');

        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->where('id_empresa', $empresa->id_empresa)
                         ->where('email', $empresa->email)
                         ->missing('senha')
                         ->has('perfil', fn (AssertableJson $json) =>
                             $json->where('id_empresa', $empresa->id_empresa)
                                  ->has('tipo_perfil')
                                  ->etc()
                         )
                         ->has('nichos', 1)
                         ->has('tecnologias', 1)
                         ->has('seguidores', 1) // Verifica se os seguidores estão sendo carregados
                         ->has('seguindo', 1) // Verifica se as empresas que ela segue estão sendo carregadas
                         ->etc()
                 );
    }

    /**
     * Testa a recuperação dos dados da empresa não autenticada.
     *
     * @return void
     */
    public function test_me_empresa_nao_autenticada()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
}
