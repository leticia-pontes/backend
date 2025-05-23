<?php

use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\PlanoController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\PedidoStatusController;
use App\Http\Controllers\Api\PagamentoController;
use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\AvaliacaoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GamificacaoController;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'empresas' => EmpresaController::class,
    'perfis' => PerfilController::class,
    // 'catalogo' => CatalogoController::class,
    // 'status-pedido' => PedidoStatusController::class,
    // 'pagamento' => PagamentoController::class,
    // 'avaliacao' => AvaliacaoController::class,
]);

// Rotas autenticadas dentro do prefixo auth
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->prefix('pedidos')->group(function () {
    Route::post('/', [PedidoController::class, 'store']);
    Route::get('/', [PedidoController::class, 'index']);
    Route::get('/{id}', [PedidoController::class, 'show']);
    Route::put('/{id}', [PedidoController::class, 'update']);
    Route::post('/{id}/cancelar', [PedidoController::class, 'cancelar']);
    Route::post('/{id}/concluir', [PedidoController::class, 'concluir']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/gamificacao/status', [GamificacaoController::class, 'status']);
    Route::get('/gamificacao/ranking', [GamificacaoController::class, 'ranking']);
    Route::get('/gamificacao/distintivos', [GamificacaoController::class, 'distintivos']);
});
