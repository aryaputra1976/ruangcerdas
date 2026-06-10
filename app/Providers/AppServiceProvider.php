<?php

namespace App\Providers;

use App\Support\AdminNotificationSummary;
use Illuminate\Pagination\Paginator;
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
        Paginator::defaultView('vendor.pagination.ruangcerdas');
        Paginator::defaultSimpleView('vendor.pagination.ruangcerdas-simple');

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
