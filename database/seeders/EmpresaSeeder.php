<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR'); // Usando Faker para dados em português do Brasil

        // Cria a empresa fixa se não existir ainda (evita duplicação)
        Empresa::firstOrCreate(
            ['email' => 'contato@solucaodigital.com.br'],
            [
                'nome' => 'Solução Digital Principal',
                'cnpj' => '00000000000000', // CNPJ de teste
                'senha' => Hash::make('password123'), // Hash da senha
                'telefone' => '(11)98765-4321',
                'endereco' => 'Rua Principal, 123 - Centro, São Paulo - SP',
                'data_cadastro' => now(), // Data de cadastro atual
            ]
        );

        // Gerar 10 empresas fictícias
        for ($i = 0; $i < 10; $i++) {
            Empresa::create([
                'nome' => $faker->company,
                'cnpj' => $faker->unique()->cnpj(false), // Gera CNPJ sem formatação
                'email' => $faker->unique()->safeEmail,
                'senha' => Hash::make('password'),
                'telefone' => $faker->phoneNumber,
                'endereco' => $faker->address,
                'data_cadastro' => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
}
