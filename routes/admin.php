<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProductAnalyticsController;
use App\Http\Controllers\Admin\LandingSettingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ProductFaqController;
use App\Http\Controllers\Admin\ProductPreviewImageController;
use App\Http\Controllers\Admin\CustomerContactController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\LeadMagnetController;
use App\Http\Controllers\Admin\LeadSubscriberController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin', 'security.headers'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}/payment-proof', [OrderController::class, 'paymentProof'])->name('orders.payment-proof');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::patch('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/{order}/resend-download-link', [OrderController::class, 'resendDownloadLink'])
            ->name('orders.resend-download-link');
        Route::post('/orders/{order}/notes', [OrderController::class, 'storeNote'])->name('orders.notes.store');
        Route::patch('/orders/{order}/notes/{note}', [OrderController::class, 'updateNote'])->name('orders.notes.update');
        Route::delete('/orders/{order}/notes/{note}', [OrderController::class, 'destroyNote'])->name('orders.notes.destroy');

        Route::get('/payment-settings', [PaymentSettingController::class, 'edit'])->name('payment-settings.edit');
        Route::put('/payment-settings', [PaymentSettingController::class, 'update'])->name('payment-settings.update');
        Route::get('/landing-settings', [LandingSettingController::class, 'edit'])->name('landing-settings.edit');
        Route::put('/landing-settings', [LandingSettingController::class, 'update'])->name('landing-settings.update');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
        Route::get('/analytics/products', [ProductAnalyticsController::class, 'index'])->name('analytics.products.index');
        Route::get('/analytics/products/export', [ProductAnalyticsController::class, 'exportCsv'])->name('analytics.products.export');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/customers', [CustomerContactController::class, 'index'])->name('customers.index');
        Route::resource('/testimonials', TestimonialController::class)->except(['show']);
        Route::resource('/coupons', CouponController::class)->except(['show']);
        Route::resource('/users', UserController::class)->except(['show']);
        Route::resource('/articles', ArticleController::class)->except(['show']);
        Route::resource('/lead-magnets', LeadMagnetController::class)->except(['show']);
        Route::get('/lead-subscribers', [LeadSubscriberController::class, 'index'])->name('lead-subscribers.index');

        Route::resource('/products', ProductController::class);
        Route::get('/products/{product}/preview', [ProductController::class, 'preview'])
            ->name('products.preview');
        Route::get('/products/{product}/faqs', [ProductFaqController::class, 'index'])->name('products.faqs.index');
        Route::post('/products/{product}/faqs', [ProductFaqController::class, 'store'])->name('products.faqs.store');
        Route::patch('/products/{product}/faqs/{faq}', [ProductFaqController::class, 'update'])->name('products.faqs.update');
        Route::delete('/products/{product}/faqs/{faq}', [ProductFaqController::class, 'destroy'])->name('products.faqs.destroy');
        Route::get('/products/{product}/preview-images', [ProductPreviewImageController::class, 'index'])->name('products.preview-images.index');
        Route::post('/products/{product}/preview-images', [ProductPreviewImageController::class, 'store'])->name('products.preview-images.store');
        Route::delete('/products/{product}/preview-images/{previewImage}', [ProductPreviewImageController::class, 'destroy'])->name('products.preview-images.destroy');
        Route::get('/products/{product}/file/download', [ProductController::class, 'downloadFile'])
            ->name('products.file.download');
        Route::delete('/products/{product}/file', [ProductController::class, 'destroyFile'])
            ->name('products.file.destroy');
        Route::resource('/categories', CategoryController::class);
    });
