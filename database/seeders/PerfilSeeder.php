<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Perfil;
use App\Models\TipoPerfil;
use Faker\Factory as Faker;

class PerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        $empresas = Empresa::all(); // Pega todas as empresas existentes
        $tiposPerfil = TipoPerfil::all(); // Pega todos os tipos de perfil existentes

        if ($empresas->isEmpty()) {
            $this->command->info('Nenhuma empresa encontrada. Por favor, execute EmpresaSeeder primeiro.');
            return;
        }
        if ($tiposPerfil->isEmpty()) {
            $this->command->info('Nenhum tipo de perfil encontrado. Por favor, execute TipoPerfilSeeder primeiro.');
            return;
        }

        foreach ($empresas as $empresa) {
            // Verifica se a empresa já possui um perfil para evitar duplicatas (relacionamento 1:1)
            if (!Perfil::where('id_empresa', $empresa->id_empresa)->exists()) {
                // Escolhe um tipo de perfil aleatório
                $randomTipoPerfil = $tiposPerfil->random();

                Perfil::create([
                    'foto' => null, // Por enquanto, sem foto de perfil.
                    'biografia' => $faker->paragraph(3),
                    'redes_sociais' => json_encode([ // Exemplo de JSON para redes sociais
                        'linkedin' => 'https://linkedin.com/in/' . $faker->slug(),
                        'facebook' => 'https://facebook.com/' . $faker->slug(),
                    ]),
                    'seguidores_cache' => $faker->numberBetween(0, 1000), // Número aleatório de seguidores para demonstração
                    'id_empresa' => $empresa->id_empresa,
                    'id_tipo_perfil' => $randomTipoPerfil->id_tipo_perfil,
                ]);
            }
        }
    }
}
