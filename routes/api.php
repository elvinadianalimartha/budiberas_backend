<?php

use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductGalleryController;
use App\Http\Controllers\API\IncomingStockController;
use App\Http\Controllers\API\OutStockController;
use App\Http\Controllers\API\ShiftStockController;
use App\Http\Controllers\API\UserController;
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

Route::middleware(['auth:sanctum'])->group(function(){
    //CUSTOMER
    Route::post('user/logout', [UserController::class, 'logout']);
    Route::get('user/fetchData', [UserController::class, 'fetchDataUser']);

    Route::get('carts', [CartController::class, 'getCart']);
    Route::post('cart', [CartController::class, 'addToCart']);
    Route::put('qtyCart/{id}', [CartController::class, 'updateQty']);
    Route::put('noteInCart/{id}', [CartController::class, 'updateOrderNotes']);
    Route::delete('cart/{id}', [CartController::class, 'deleteCart']);
    Route::put('cartIsSelected/{id}', [CartController::class, 'updateIsSelected']);
    Route::put('allCartIsSelected', [CartController::class, 'updateIsSelectedAllCart']);
});

Route::post('user/login', [UserController::class, 'login']);
Route::post('user/register', [UserController::class, 'register']);

Route::get('localRegencies', [UserController::class, 'getLocalRegencies']);
Route::get('localDistricts', [UserController::class, 'getLocalDistricts']);

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
Route::get('destinationProduct/{sourceProductId}', [ProductController::class, 'getDestinationProduct']);
Route::delete('product/{id}', [ProductController::class, 'deleteProduct']);
Route::put('product/{id}', [ProductController::class, 'updateProduct']);
Route::put('statusProduct/{id}', [ProductController::class, 'updateActivationProduct']);
Route::put('productPrice/{id}', [ProductController::class, 'updateProductPrice']);
Route::get('productInTransaction/{id}', [ProductController::class, 'checkProductInTransaction']);

Route::get('productGallery/{productId}', [ProductGalleryController::class, 'getPhoto']);
Route::post('productGallery/{productId}', [ProductGalleryController::class, 'addPhoto']);
Route::delete('productGallery/{id}', [ProductGalleryController::class, 'deletePhoto']);
Route::post('updatePhoto/{id}', [ProductGalleryController::class, 'updatePhoto']);

Route::post('incomingStock', [IncomingStockController::class, 'createIncomingStock']);
Route::get('incomingStock', [IncomingStockController::class, 'getIncomingStock']);
Route::delete('incomingStock/{id}', [IncomingStockController::class, 'deleteIncomingStock']);
Route::put('incomingStock/{id}', [IncomingStockController::class, 'updateIncomingStock']);

Route::post('outStock', [OutStockController::class, 'createOutStock']);
Route::get('outStock', [OutStockController::class, 'getOutStock']);
Route::delete('outStock/{id}', [OutStockController::class, 'deleteOutStock']);
Route::put('outStock/{id}', [OutStockController::class, 'updateOutStock']);
Route::get('maxOutQty/{id}', [OutStockController::class, 'countMaxOutQty']);

Route::get('shiftStock', [ShiftStockController::class, 'getShiftStock']);
Route::post('shiftingStock', [ShiftStockController::class, 'shiftingStock']);
Route::delete('cancelShiftStock/{id}', [ShiftStockController::class, 'cancelShiftStock']);