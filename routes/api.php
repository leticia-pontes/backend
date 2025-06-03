<?php

// Falta seeders de perfil_nicho e perfil_tecnologia e os controllers

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AvaliacaoController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\GamificacaoController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\PlanoController;
use App\Http\Controllers\Api\PedidoStatusController;
use App\Http\Controllers\Api\PagamentoController;
use App\Http\Controllers\Api\ProjetoController;
use Illuminate\Support\Facades\Route;

// Rotas de perfis (públicas apenas leitura)
Route::get('perfis', [PerfilController::class, 'index']);
Route::get('perfis/{id}', [PerfilController::class, 'show']);

// Rotas de empresas
Route::apiResource('empresas', EmpresaController::class);

// ---
// Rotas que exigem autenticação (auth:sanctum)
// ---
Route::middleware('auth:sanctum')->group(function () {
    // Rotas protegidas de perfis
    Route::post('perfis', [PerfilController::class, 'store']);
    Route::put('perfis/{id}', [PerfilController::class, 'update']);
    Route::delete('perfis/{id}', [PerfilController::class, 'destroy']);

    // Avaliações (criação, edição, remoção)
    Route::post('avaliacoes', [AvaliacaoController::class, 'store']);
    Route::put('avaliacoes/{avaliacao}', [AvaliacaoController::class, 'update']);
    Route::delete('avaliacoes/{avaliacao}', [AvaliacaoController::class, 'destroy']);

    // Projetos (criação, edição, remoção)
    Route::post('projetos', [ProjetoController::class, 'store']);
    Route::put('projetos/{projeto}', [ProjetoController::class, 'update']);
    Route::delete('projetos/{projeto}', [ProjetoController::class, 'destroy']);

    // Pagamentos protegidos
    Route::post('pagamentos', [PagamentoController::class, 'store']);
    Route::put('pagamentos/{pagamento}', [PagamentoController::class, 'update']);
    Route::delete('pagamentos/{pagamento}', [PagamentoController::class, 'destroy']);

    // Outros recursos protegidos
    Route::apiResource('planos', PlanoController::class);
    Route::apiResource('pedidos', PedidoController::class);

    Route::get('gamificacao/status', [GamificacaoController::class, 'status']);
    Route::get('gamificacao/ranking', [GamificacaoController::class, 'ranking']);
    Route::get('gamificacao/distintivos', [GamificacaoController::class, 'distintivos']);

    // Rotas de mudança de status de pedidos
    Route::prefix('pedidos')->controller(PedidoController::class)->group(function () {
        Route::patch('{pedido}/aceitar', 'aceitar');
        Route::patch('{pedido}/em-andamento', 'emAndamento');
        Route::patch('{pedido}/aguardar', 'aguardar');
        Route::patch('{pedido}/concluir', 'concluir');
        Route::patch('{pedido}/cancelar', 'cancelar');
    });

    // Autenticação: usuário logado
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
});

// Rotas públicas de autenticação
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});
