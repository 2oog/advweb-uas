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
Route::apiResource('orders', OrderController::class);
Route::post('/orders/{id}/print', [PrinterController::class, 'print']);
