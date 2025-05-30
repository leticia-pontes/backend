<?php

// Falta seeders de perfil_nicho e perfil_tecnologia e os controllers

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AvaliacaoController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\GamificacaoController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\PedidoStatusController;
use App\Http\Controllers\Api\PagamentoController;
use App\Http\Controllers\Api\ProjetoController;
use Illuminate\Support\Facades\Route;

// ---
// Rotas que não exigem autenticação ou que já estão agrupadas por outras regras
// ---

Route::apiResources([
    'empresas' => EmpresaController::class,
    'perfis' => PerfilController::class,
]);

// Rotas de Avaliações (públicas)
Route::get('avaliacoes', [AvaliacaoController::class, 'index']);
Route::get('avaliacoes/{avaliacao}', [AvaliacaoController::class, 'show']);

// Rotas de Projetos (públicas)
Route::get('projetos', [ProjetoController::class, 'index']);
Route::get('projetos/{projeto}', [ProjetoController::class, 'show']);

// Rotas de Pagamentos (públicas - apenas index e show)
Route::get('pagamentos', [PagamentoController::class, 'index']);
Route::get('pagamentos/{pagamento}', [PagamentoController::class, 'show']);

// ---
// Rotas que exigem autenticação (Auth:Sanctum)
// ---
Route::middleware('auth:sanctum')->group(function () {
    // Rotas de Avaliações que exigem autenticação
    Route::post('avaliacoes', [AvaliacaoController::class, 'store']); // Criar nova avaliação
    Route::put('avaliacoes/{avaliacao}', [AvaliacaoController::class, 'update']); // Atualizar avaliação
    Route::delete('avaliacoes/{avaliacao}', [AvaliacaoController::class, 'destroy']); // Remover uma avalicação

    // Rotas de Projetos que exigem autenticação
    Route::post('projetos', [ProjetoController::class, 'store']); // Criar novo projeto
    Route::put('projetos/{projeto}', [ProjetoController::class, 'update']); // Atualizar projeto
    Route::delete('projetos/{projeto}', [ProjetoController::class, 'destroy']); // Remover projeto

    // Pagamentos: store, update, destroy (exigem autenticação)
    Route::post('pagamentos', [PagamentoController::class, 'store']);
    Route::put('pagamentos/{pagamento}', [PagamentoController::class, 'update']);
    Route::delete('pagamentos/{pagamento}', [PagamentoController::class, 'destroy']);

    // Outros recursos da API que exigem autenticação
    Route::apiResource('planos', PlanoController::class);
    Route::apiResource('pedidos', PedidoController::class);
    Route::apiResource('gamificacao', GamificacaoController::class);

    // Rotas de mudança de status de pedidos
    Route::prefix('pedidos')->controller(PedidoController::class)->group(function () {
        Route::patch('{pedido}/aceitar', 'aceitar');
        Route::patch('{pedido}/em-andamento', 'emAndamento');
        Route::patch('{pedido}/aguardar', 'aguardar');
        Route::patch('{pedido}/concluir', 'concluir');
        Route::patch('{pedido}/cancelar', 'cancelar');
    });

});


// Rotas de Autenticação (login, register)
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
