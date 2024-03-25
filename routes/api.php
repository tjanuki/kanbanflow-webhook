<?php

use App\Http\Controllers\KanbanFlowWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/kanbanflow-webhook', [KanbanFlowWebhookController::class, 'handleWebhook'])
//    ->middleware('auth:sanctum');
;
