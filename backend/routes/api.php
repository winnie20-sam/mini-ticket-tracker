<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'tickets'], function ($router) {
    $router->get('stats', [TicketController::class, 'stats']);
    $router->get('/', [TicketController::class, 'index']);
    $router->post('/', [TicketController::class, 'store']);
    $router->patch('{id}', [TicketController::class, 'update']);
});
