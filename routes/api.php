<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AvaliacaoController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\GamificacaoController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\PlanoController;
use App\Http\Controllers\Api\PedidoStatusController;
use App\Http\Controllers\Api\PagamentoController;
// use App\Http\Controllers\Api\PlanoController;
use App\Http\Controllers\Api\NichoController;
use App\Http\Controllers\Api\TecnologiaController;
use Illuminate\Support\Facades\Route;

// Rotas públicas de perfis (somente leitura)
Route::get('perfis', [PerfilController::class, 'index']);
Route::get('perfis/{id}', [PerfilController::class, 'show']);

// Rotas públicas de avaliações (somente leitura)
Route::get('avaliacoes', [AvaliacaoController::class, 'index']);
Route::get('avaliacoes/{id}', [AvaliacaoController::class, 'show']);

// Rotas públicas de empresas (leitura e criação)
Route::get('empresas', [EmpresaController::class, 'index']);
Route::post('empresas', [EmpresaController::class, 'store']);
Route::get('empresas/{empresa}', [EmpresaController::class, 'show']);

Route::get('pedidos-recentes', [PedidoController::class, 'recentesConcluidos']);

Route::apiResource('nichos', NichoController::class);
Route::apiResource('tecnologias', TecnologiaController::class);

// Rotas de autenticação pública
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});

// Rotas protegidas por autenticação (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação: perfil do usuário logado e logout
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Rotas protegidas de empresas
    Route::put('empresas/{empresa}', [EmpresaController::class, 'update']);
    Route::delete('empresas/{empresa}', [EmpresaController::class, 'destroy']);

    // Rotas protegidas de perfis
    Route::post('perfis', [PerfilController::class, 'store']);
    Route::put('perfis/{id}', [PerfilController::class, 'update']);
    Route::delete('perfis/{id}', [PerfilController::class, 'destroy']);

    // Rotas protegidas de avaliações
    Route::post('avaliacoes', [AvaliacaoController::class, 'store']);
    Route::put('avaliacoes/{avaliacao}', [AvaliacaoController::class, 'update']);
    Route::delete('avaliacoes/{avaliacao}', [AvaliacaoController::class, 'destroy']);

    // Rotas protegidas de pagamentos
    Route::get('pagamentos', [PagamentoController::class, 'index']);
    Route::post('pagamentos', [PagamentoController::class, 'store']);
    Route::get('pagamentos/{pagamento}', [PagamentoController::class, 'show']);
    Route::put('pagamentos/{pagamento}', [PagamentoController::class, 'update']);
    Route::delete('pagamentos/{pagamento}', [PagamentoController::class, 'destroy']);

    // Planos
    Route::apiResource('planos', PlanoController::class);

    // Pedidos
    Route::get('/pedidos/pendentes', [PedidoController::class, 'pedidosPendentes']);
    Route::apiResource('pedidos', PedidoController::class);

    // Mudança de status dos pedidos
    Route::prefix('pedidos')->controller(PedidoController::class)->group(function () {
        Route::patch('{pedido}/aceitar', 'aceitar');
        Route::patch('{pedido}/em-andamento', 'emAndamento');
        Route::patch('{pedido}/aguardar', 'aguardar');
        Route::patch('{pedido}/concluir', 'concluir');
        Route::patch('{pedido}/cancelar', 'cancelar');
    });

    // Gamificação
    Route::prefix('gamificacao')->controller(GamificacaoController::class)->group(function () {
        Route::get('status', 'status');
        Route::get('ranking', 'ranking');
        Route::get('distintivos', 'distintivos');
        Route::get('{id}', 'show');
    });
});
