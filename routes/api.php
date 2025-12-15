<?php

use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PrinterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('menu-items', MenuController::class);
// equivalent to
// Route::get('menu-items', [MenuController::class, 'index']);
// Route::post('menu-items', [MenuController::class, 'store']);
// Route::get('menu-items/{id}', [MenuController::class, 'show']);
// Route::put('menu-items/{id}', [MenuController::class, 'update']);
// Route::delete('menu-items/{id}', [MenuController::class, 'destroy']);

Route::apiResource('orders', OrderController::class);
// equivalent to
// Route::get('orders', [OrderController::class, 'index']);
// Route::post('orders', [OrderController::class, 'store']);
// Route::get('orders/{id}', [OrderController::class, 'show']);
// Route::put('orders/{id}', [OrderController::class, 'update']);
// Route::delete('orders/{id}', [OrderController::class, 'destroy']);

Route::post('/orders/{id}/print', [PrinterController::class, 'print']);
