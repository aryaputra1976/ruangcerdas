<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Admin\ReportController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::patch('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');

        Route::get('/payment-settings', [PaymentSettingController::class, 'edit'])->name('payment-settings.edit');
        Route::put('/payment-settings', [PaymentSettingController::class, 'update'])->name('payment-settings.update');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        Route::resource('/products', ProductController::class);
        Route::resource('/categories', CategoryController::class);
    });
