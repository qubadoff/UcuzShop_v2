<?php

use App\Http\Controllers\api\Auth\AuthController;
use App\Http\Controllers\api\Cart\CartController;
use App\Http\Controllers\api\Order\OrderController;
use App\Http\Controllers\api\Product\ProductController;
use App\Http\Controllers\api\v1\Partner\PartnerController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::prefix('panel')->middleware('auth:sanctum')->group(function () {
    Route::get('/productCategory', [ProductController::class, 'productCategory']);
    Route::get('/products', [ProductController::class, 'products']);
    Route::get('/cartList', [CartController::class, 'cartList']);
    Route::post('/addCart', [CartController::class, 'addCart']);
    Route::post('/deleteCart', [CartController::class, 'deleteCart']);
    Route::post('/updateCart', [CartController::class, 'updateCart']);

    Route::post('/sendOrder', [OrderController::class, 'sendOrder']);
    Route::get('/orderDetails', [OrderController::class, 'orderDetails']);
});


Route::prefix('partner')->group(function () {
    Route::post('/login', [PartnerController::class, 'login']);
    Route::get('/orders', [PartnerController::class, 'orders'])->middleware('auth:sanctum');
    Route::post('/orderStatus', [PartnerController::class, 'orderStatus'])->middleware('auth:sanctum');
});
