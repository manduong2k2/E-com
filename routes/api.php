<?php

use App\Http\Controllers\brandController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\productController;
use App\Http\Controllers\userController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/products',[productController::class,'index']);

Route::get('/products/{id}',[productController::class,'show']);

Route::post('/product/create',[productController::class,'create']);

Route::post('/brand/create',[brandController::class,'create']);

Route::post('/category/create',[categoryController::class,'create']);

Route::post('/signup',[userController::class,'signup']);

Route::post('/login',[userController::class,'login'] );

