<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Para manipular datas

class SeguidorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = Empresa::all();

        if ($empresas->isEmpty()) {
            $this->command->info('Nenhuma empresa encontrada. Execute EmpresaSeeder primeiro.');
            return;
        }

        foreach ($empresas as $empresaSeguidora) {
            // Garante que a empresa não siga a si mesma
            $outrasEmpresas = $empresas->where('id_empresa', '!=', $empresaSeguidora->id_empresa);

            if ($outrasEmpresas->isEmpty()) {
                continue; // Nenhuma outra empresa para seguir
            }

            // Seleciona um número aleatório de empresas para seguir (entre 0 e 5)
            $empresasASeguir = $outrasEmpresas->random(rand(0, min(5, $outrasEmpresas->count())));

            foreach ($empresasASeguir as $empresaSeguida) {
                // Anexa o relacionamento, verificando se já não existe
                $empresaSeguidora->seguindo()->syncWithoutDetaching([
                    $empresaSeguida->id_empresa => ['data_seguida' => Carbon::now()->subDays(rand(1, 365))]
                ]);
            }
        }

        foreach ($empresas as $empresa) {
            $contagemSeguidores = $empresa->seguidores()->count();
            if ($empresa->perfil) { // Verifica se a empresa tem um perfil associado
                $empresa->perfil->update(['seguidores_cache' => $contagemSeguidores]);
            }
        }
    }
}
