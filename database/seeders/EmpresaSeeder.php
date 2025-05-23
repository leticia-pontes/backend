<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        Empresa::create([
            'nome' => 'Empresa Exemplo',
            'cnpj' => '12.345.678/0001-99',
            'perfil' => 'desenvolvedor',
            'seguidores' => 120,
            'email' => 'empresa@example.com',
            'senha' => 'senha123', // será bcrypt() automaticamente
            'telefone' => '11999999999',
            'endereco' => 'Rua das Flores, 123',
            'nivel' => 1,
            'pontos' => 200,
        ]);
    }
}
