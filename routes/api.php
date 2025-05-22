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
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'empresas' => EmpresaController::class,
    'perfis' => PerfilController::class,
    // 'plano' => PlanoController::class,
    // 'catalogo' => CatalogoController::class,
    // 'pedido' => PedidoController::class,
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
