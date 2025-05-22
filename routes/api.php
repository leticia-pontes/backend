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

// Rota de login sem prefixo (opcional)
Route::post('login', [AuthController::class, 'login']);

// Rotas autenticadas dentro do prefixo auth
Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});
