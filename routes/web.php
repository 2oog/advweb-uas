<?php

use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PosController::class, 'index']);
Route::get('/pos/history', [PosController::class, 'history']);
