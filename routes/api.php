<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Order as Order;
use App\Http\Controllers\Product as Product;
use App\Http\Controllers\Voucher as Voucher;
use App\Http\Controllers\OrderLine as OrderLine;
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
Route::prefix('orders')->group(function () {
    Route::get('', Order\GetOrdersController::class);
    Route::post('', Order\CreateOrderController::class);
    Route::patch('{order}', Order\UpdateOrderController::class);
    Route::delete('{order}', Order\DeleteOrderController::class);
    Route::get('{order}', Order\GetOrderController::class);

    Route::prefix('{order}/order-lines')->group(function () {
        Route::get('', OrderLine\GetOrderLinesByOrderController::class);
        Route::post('', OrderLine\CreateOrderLineController::class);
        Route::patch('{order_line}', OrderLine\UpdateOrderLineController::class);
        Route::delete('{order_line}', OrderLine\DeleteOrderLineController::class);
        Route::get('{order_line}', OrderLine\GetOrderLineController::class);
    });
});

Route::prefix('products')->group(function () {
    Route::get('', Product\GetProductsController::class);
    Route::post('', Product\CreateProductController::class);
    Route::patch('{product}', Product\UpdateProductController::class);
    Route::delete('{product}', Product\DeleteProductController::class);
    Route::get('{product}', Product\GetProductController::class);
});

Route::prefix('vouchers')->group(function () {
    Route::get('', Voucher\GetVouchersController::class);
    Route::post('', Voucher\CreateVoucherController::class);
    Route::patch('{voucher}', Voucher\UpdateVoucherController::class);
    Route::delete('{voucher}', Voucher\DeleteVoucherController::class);
    Route::get('{voucher}', Voucher\GetVoucherController::class);
});
