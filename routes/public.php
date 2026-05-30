<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProductController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Public\OrderController;
use App\Http\Controllers\Public\DownloadController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/checkout/{product:slug}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{product:slug}', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/order/{invoice}/thank-you', [OrderController::class, 'thankYou'])->name('orders.thank-you');
Route::get('/order/{invoice}/upload-payment', [OrderController::class, 'paymentForm'])->name('orders.payment.form');
Route::post('/order/{invoice}/upload-payment', [OrderController::class, 'uploadPayment'])->name('orders.payment.upload');

Route::get('/order/{invoice}/download/{token}', [DownloadController::class, 'download'])->name('orders.download');
