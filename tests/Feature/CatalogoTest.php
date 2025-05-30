<?php

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CatalogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_catalogos()
    {
        $empresa = Empresa::factory()->create();

        Catalogo::factory()->count(3)->create([
            'id_empresa' => $empresa->id,
        ]);

        $response = $this->getJson('/api/catalogos');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }
}
