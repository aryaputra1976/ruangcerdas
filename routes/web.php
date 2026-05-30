<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/public.php';
require __DIR__.'/admin.php';
require __DIR__.'/auth.php';