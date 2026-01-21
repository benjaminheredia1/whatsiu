<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Middleware\AuthMiddleware;

Route::middleware(AuthMiddleware::class)->group(function () {
    Route::get('/conversations', [ChatController::class, 'index']);
    Route::get('/conversations/{session_id}', [ChatController::class, 'show']);
    Route::post('/conversations/{session_id}/send', [ChatController::class, 'send']);
    Route::put('/conversations/{session_id}/estado', [ChatController::class, 'updateEstado']);
    Route::post('/memory', [ChatController::class, 'storeMemory']);
});
