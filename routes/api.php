<?php

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductGalleryController;
use App\Http\Controllers\API\IncomingStockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('categories', [CategoryController::class, 'getAllCategories']);
Route::get('products', [ProductController::class, 'getAllProducts']);
Route::get('productByCategoryName', [ProductController::class, 'getProductByCategoryName']);

//ADMIN
Route::post('category', [CategoryController::class, 'createCategory']);
Route::delete('category/{id}', [CategoryController::class, 'deleteCategory']);
Route::put('category/{id}', [CategoryController::class, 'updateCategory']);

Route::post('product', [ProductController::class, 'createProduct']);
Route::get('retailedProduct', [ProductController::class, 'getRetailedProduct']);
Route::delete('product/{id}', [ProductController::class, 'deleteProduct']);
Route::put('product/{id}', [ProductController::class, 'updateProduct']);
Route::get('productInTransaction/{id}', [ProductController::class, 'checkProductInTransaction']);

Route::post('productGallery', [ProductGalleryController::class, 'createPhoto']);
Route::delete('productGallery/{id}', [ProductGalleryController::class, 'deletePhoto']);

Route::post('incomingStock', [IncomingStockController::class, 'createIncomingStock']);
Route::get('addedIncomingStock', [IncomingStockController::class, 'getAddedIncomingStock']);
Route::get('returnIncomingStock', [IncomingStockController::class, 'getReturnIncomingStock']);
Route::delete('incomingStock/{id}', [IncomingStockController::class, 'deleteIncomingStock']);
Route::put('incomingStock/{id}', [IncomingStockController::class, 'updateIncomingStock']);