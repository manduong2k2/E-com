<?php

use App\Http\Controllers\brandController;
use App\Http\Controllers\cartController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\productController;
use App\Http\Controllers\userController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/protected', function () {
    return 'Authenticated success';
})->middleware(Authorize::class);

//Product

Route::get('/products',[productController::class,'index']);

Route::get('/products/personal',[productController::class,'personal'])->middleware(Authenticate::class);

Route::get('/products/{id}',[productController::class,'show']);

Route::post('/product/create',[productController::class,'create'])->middleware(Authenticate::class);

Route::post('/product/edit/{id}',[productController::class,'edit']);

Route::delete('/product/{id}',[productController::class,'destroy'])->middleware(Authorize::class);

//Brand

Route::get('/brands',[brandController::class,'index']);

Route::get('/brand/{id}',[brandController::class,'show']);

Route::post('/brand/create',[brandController::class,'create']);

Route::delete('/brand/delete/{id}',[brandController::class,'destroy']);

Route::post('/brand/edit/{id}',[brandController::class,'edit']);

//Category

Route::get('/categories',[categoryController::class,'index']);

Route::get('/category/{id}',[categoryController::class,'show']);

Route::post('/category/create',[categoryController::class,'create']);

Route::post('/category/edit/{id}',[categoryController::class,'edit']);

Route::delete('/category/delete/{id}',[categoryController::class,'destroy']);

//User

Route::get('/users',[userController::class,'index'])->middleware(Authorize::class);

Route::get('/user',[userController::class,'show']);

Route::get('/user/products',[userController::class,'products']);

Route::delete('/user/product/{id}',[userController::class,'destroy']);

Route::post('/user/forgot/{email}',[userController::class,'sendEmail']);

Route::post('/user/edit',[userController::class,'edit'])->middleware(Authenticate::class);

Route::post('/signup',[userController::class,'signup']);

Route::post('/user/changePassword',[userController::class,'changePassword'])->middleware(Authenticate::class);

Route::post('/login',[userController::class,'login'] );

Route::post('/grant/{id}',[userController::class,'grant'])->middleware(Authorize::class);

Route::post('/revoke/{id}',[userController::class,'revoke'])->middleware(Authorize::class);

//Cart

Route::get('/carts',[cartController::class,'index'])->middleware(Authenticate::class);

Route::post('/cart/add/{product_id}',[cartController::class,'create'])->middleware(Authenticate::class);

Route::post('/cart/increase/{product_id}',[cartController::class,'increase'])->middleware(Authenticate::class);

Route::post('/cart/decrease/{product_id}',[cartController::class,'decrease'])->middleware(Authenticate::class);

Route::delete('/cart/delete/{product_id}',[cartController::class,'destroy'])->middleware(Authenticate::class);

Route::delete('/cart/deleteAll',[cartController::class,'destroyAll'])->middleware(Authenticate::class);

//Order

Route::get('/orders',[orderController::class,'index'])->middleware(Authenticate::class);

Route::post('/order',[orderController::class,'create'])->middleware(Authenticate::class);


