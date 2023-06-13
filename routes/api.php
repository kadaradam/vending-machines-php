<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register/seller', 'App\\Http\\Controllers\\Api\\AuthController@registerSeller');
Route::post('register/buyer', 'App\\Http\\Controllers\\Api\\AuthController@registerBuyer');
Route::post('login', 'App\\Http\\Controllers\\Api\\AuthController@login');

Route::group(['namespace' => 'App\Http\Controllers\Api', 'middleware' => 'auth:sanctum'], function () {
    Route::apiResource('user', UserController::class);
    Route::apiResource('products/seller', SellerProductController::class)->middleware('role:seller');
    Route::apiResource('products/buyer', BuyerProductController::class)->middleware('role:buyer');
});
