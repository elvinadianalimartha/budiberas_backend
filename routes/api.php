<?php

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductGalleryController;
use App\Http\Controllers\API\IncomingStockController;
use App\Http\Controllers\API\OutStockController;
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
Route::get('productByName', [ProductController::class, 'searchProductByName']);

//ADMIN
Route::post('category', [CategoryController::class, 'createCategory']);
Route::delete('category/{id}', [CategoryController::class, 'deleteCategory']);
Route::put('category/{id}', [CategoryController::class, 'updateCategory']);

Route::post('product', [ProductController::class, 'createProduct']);
Route::get('availableStockProduct', [ProductController::class, 'getAvailableStockProduct']);
Route::get('retailedProduct', [ProductController::class, 'getRetailedProduct']);
Route::delete('product/{id}', [ProductController::class, 'deleteProduct']);
Route::put('product/{id}', [ProductController::class, 'updateProduct']);
Route::put('statusProduct/{id}', [ProductController::class, 'updateActivationProduct']);
Route::put('productPrice/{id}', [ProductController::class, 'updateProductPrice']);
Route::get('productInTransaction/{id}', [ProductController::class, 'checkProductInTransaction']);

Route::post('productGallery/{id}', [ProductGalleryController::class, 'addPhoto']);
Route::delete('productGallery/{id}', [ProductGalleryController::class, 'deletePhoto']);
Route::post('updateCoverPhoto/{id}', [ProductGalleryController::class, 'updateCoverPhoto']);

Route::post('incomingStock', [IncomingStockController::class, 'createIncomingStock']);
Route::get('incomingStock', [IncomingStockController::class, 'getIncomingStock']);
Route::delete('incomingStock/{id}', [IncomingStockController::class, 'deleteIncomingStock']);
Route::put('incomingStock/{id}', [IncomingStockController::class, 'updateIncomingStock']);

Route::post('outStock', [OutStockController::class, 'createOutStock']);
Route::get('outStock', [OutStockController::class, 'getOutStock']);
Route::delete('outStock/{id}', [OutStockController::class, 'deleteOutStock']);
Route::put('outStock/{id}', [OutStockController::class, 'updateOutStock']);
Route::get('maxOutQty/{id}', [OutStockController::class, 'countMaxOutQty']);