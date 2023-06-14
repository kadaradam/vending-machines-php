<?php

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
    Route::apiResource('user', UserController::class)->except(['index', 'create', 'store', 'show', 'edit']);
    Route::get('user/me', 'App\\Http\\Controllers\\Api\\UserController@me');
    Route::apiResource('products/seller', SellerProductController::class)->middleware('role:seller');
    Route::apiResource("products/buyer", BuyerProductController::class)
        ->except(["create", "store", "edit", "update", "destroy"])
        ->middleware("role:buyer");
});
