<?php

use App\Http\Controllers\brandController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\productController;
use App\Http\Controllers\userController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'hello world';
});

