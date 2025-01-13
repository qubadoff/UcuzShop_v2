<?php

use App\Http\Controllers\api\Auth\AuthController;
use App\Http\Controllers\api\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::prefix('panel')->middleware('auth:sanctum')->group(function () {
    Route::get('/productCategory', [ProductController::class, 'productCategory']);
    Route::get('/products', [ProductController::class, 'products']);
});
