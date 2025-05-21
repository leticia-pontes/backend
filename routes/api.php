<?php

use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\PlanoController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\PedidoStatusController;
use App\Http\Controllers\Api\PagamentoController;
use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\AvaliacaoController;

Route::apiResources([
    'empresa' => EmpresaController::class,
    // 'plano' => PlanoController::class,
    // 'perfil' => PerfilController::class,
    // 'pedido' => PedidoController::class,
    // 'status-pedido' => PedidoStatusController::class,
    // 'pagamento' => PagamentoController::class,
    // 'catalogo' => CatalogoController::class,
    // 'avaliacao' => AvaliacaoController::class,
]);
