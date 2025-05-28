<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tecnologia;

class TecnologiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que não haja duplicatas ao rodar o seeder múltiplas vezes
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'PHP']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Laravel']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'JavaScript']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'React']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Angular']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Vue.js']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Node.js']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Python']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Django']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Java']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Spring Boot']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'C#']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => '.NET']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Ruby on Rails']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Go']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Swift']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Kotlin']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Flutter']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'React Native']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'MySQL']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'PostgreSQL']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'MongoDB']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'AWS']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Azure']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Google Cloud Platform']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Docker']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Kubernetes']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'Git']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'GraphQL']);
        Tecnologia::firstOrCreate(['nome_tecnologia' => 'REST API']);
    }
}
