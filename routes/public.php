<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProductController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Public\OrderController;
use App\Http\Controllers\Public\DownloadController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\Public\RobotsController;
use App\Http\Controllers\Public\OrderTrackingController;
use App\Http\Controllers\Public\FaqController;
use App\Http\Controllers\Public\LegalPageController;
use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\LeadMagnetController;
use App\Http\Controllers\Public\TryoutController;
use App\Http\Controllers\Public\TryoutSessionController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/checkout/{product:slug}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{product:slug}', [CheckoutController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('checkout.store');

Route::get('/order/{invoice}/thank-you', [OrderController::class, 'thankYou'])->name('orders.thank-you');
Route::get('/order/{invoice}/upload-payment', [OrderController::class, 'paymentForm'])->name('orders.payment.form');
Route::post('/order/{invoice}/upload-payment', [OrderController::class, 'uploadPayment'])
    ->middleware('throttle:10,1')
    ->name('orders.payment.upload');

Route::get('/order/{invoice}/download/{token}', [DownloadController::class, 'download'])->name('orders.download');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('public.sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('public.robots');
Route::get('/faq', [FaqController::class, 'index'])->name('public.faq');
Route::get('/terms', [LegalPageController::class, 'terms'])->name('public.terms');
Route::get('/privacy', [LegalPageController::class, 'privacy'])->name('public.privacy');
Route::get('/order-tracking', [OrderTrackingController::class, 'index'])->name('public.order-tracking.index');
Route::post('/order-tracking', [OrderTrackingController::class, 'show'])
    ->middleware('throttle:15,1')
    ->name('public.order-tracking.show');
Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/gratis', [LeadMagnetController::class, 'index'])->name('lead-magnets.index');
Route::get('/gratis/{leadMagnet:slug}', [LeadMagnetController::class, 'show'])->name('lead-magnets.show');
Route::post('/gratis/{leadMagnet:slug}/download', [LeadMagnetController::class, 'download'])
    ->middleware('throttle:20,1')
    ->name('lead-magnets.download');
Route::get('/tryout-cpns', [TryoutController::class, 'index'])->name('public.tryouts.index');
Route::get('/tryout-cpns/{tryoutPackage:slug}/beli', [TryoutController::class, 'buy'])->name('public.tryouts.buy');
Route::get('/tryout-cpns/{tryoutPackage:slug}/mulai', [TryoutController::class, 'start'])->name('public.tryouts.start');
Route::post('/tryout-cpns/{tryoutPackage:slug}/mulai', [TryoutSessionController::class, 'begin'])
    ->middleware('throttle:20,1')
    ->name('public.tryouts.begin');
Route::get('/tryout-session/{tryoutSession}/ujian', [TryoutSessionController::class, 'exam'])->name('public.tryout-sessions.exam');
Route::post('/tryout-session/{tryoutSession}/jawaban', [TryoutSessionController::class, 'save'])
    ->middleware('throttle:60,1')
    ->name('public.tryout-sessions.save');
Route::post('/tryout-session/{tryoutSession}/submit', [TryoutSessionController::class, 'submit'])
    ->middleware('throttle:30,1')
    ->name('public.tryout-sessions.submit');
Route::get('/tryout-cpns/riwayat', [TryoutSessionController::class, 'history'])->name('public.tryout-sessions.history');
Route::get('/tryout-session/{tryoutSession}/hasil', [TryoutSessionController::class, 'result'])->name('public.tryout-sessions.result');
Route::get('/tryout-session/{tryoutSession}/pembahasan', [TryoutSessionController::class, 'review'])->name('public.tryout-sessions.review');
