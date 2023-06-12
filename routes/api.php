<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SellerProductController;
use App\Http\Controllers\Api\BuyerProductController;

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
Route::post('register', 'App\\Http\\Controllers\\Api\\AuthController@register');
Route::post('login', 'App\\Http\\Controllers\\Api\\AuthController@login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('products/seller', SellerProductController::class);
    Route::apiResource('products/buyer', BuyerProductController::class);
});
