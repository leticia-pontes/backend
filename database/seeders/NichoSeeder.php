<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nicho;

class NichoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nichos para empresas CONTRATANTES
        Nicho::firstOrCreate(['nome_nicho' => 'Desenvolvimento de Software']);
        Nicho::firstOrCreate(['nome_nicho' => 'Desenvolvimento Web']);
        Nicho::firstOrCreate(['nome_nicho' => 'Desenvolvimento Mobile']);
        Nicho::firstOrCreate(['nome_nicho' => 'Inteligência Artificial']);
        Nicho::firstOrCreate(['nome_nicho' => 'Machine Learning']);
        Nicho::firstOrCreate(['nome_nicho' => 'Ciência de Dados']);
        Nicho::firstOrCreate(['nome_nicho' => 'Infraestrutura de TI']);
        Nicho::firstOrCreate(['nome_nicho' => 'Segurança da Informação']);
        Nicho::firstOrCreate(['nome_nicho' => 'Cloud Computing']);
        Nicho::firstOrCreate(['nome_nicho' => 'Consultoria de TI']);
        Nicho::firstOrCreate(['nome_nicho' => 'Suporte Técnico']);
        Nicho::firstOrCreate(['nome_nicho' => 'Design de UX/UI']);
        Nicho::firstOrCreate(['nome_nicho' => 'Marketing Digital']);
        Nicho::firstOrCreate(['nome_nicho' => 'E-commerce']);

        // Nichos para empresas DESENVOLVEDORAS
        Nicho::firstOrCreate(['nome_nicho' => 'Desenvolvimento de Jogos']);
        Nicho::firstOrCreate(['nome_nicho' => 'Realidade Virtual']);
        Nicho::firstOrCreate(['nome_nicho' => 'Realidade Aumentada']);
        Nicho::firstOrCreate(['nome_nicho' => 'Internet das Coisas (IoT)']);
        Nicho::firstOrCreate(['nome_nicho' => 'Blockchain']);
        Nicho::firstOrCreate(['nome_nicho' => 'Serviços de API']);
        Nicho::firstOrCreate(['nome_nicho' => 'Testes de Software']);
        Nicho::firstOrCreate(['nome_nicho' => 'Manutenção de Software']);
        Nicho::firstOrCreate(['nome_nicho' => 'Outsourcing de Desenvolvimento']);
        Nicho::firstOrCreate(['nome_nicho' => 'Startups']);
        Nicho::firstOrCreate(['nome_nicho' => 'Desenvolvimento Full-Stack']);
        Nicho::firstOrCreate(['nome_nicho' => 'Desenvolvimento Back-End']);
        Nicho::firstOrCreate(['nome_nicho' => 'Desenvolvimento Front-End']);
        Nicho::firstOrCreate(['nome_nicho' => 'Desenvolvimento de Aplicativos']);
    }
}
