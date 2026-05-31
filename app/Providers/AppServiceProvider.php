<?php

namespace App\Providers;

use App\Support\AdminNotificationSummary;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer([
            'components.admin.sidebar',
            'components.admin.header',
        ], function ($view) {
            if (!request()->routeIs('admin.*')) {
                return;
            }

            $view->with('adminNotificationSummary', AdminNotificationSummary::make());
        });
    }
}
