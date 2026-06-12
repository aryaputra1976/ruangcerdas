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
        if (View::exists('vendor.pagination.ruangcerdas')) {
            Paginator::defaultView('vendor.pagination.ruangcerdas');
        } else {
            Paginator::defaultView('pagination.ruangcerdas-fallback');
        }

        if (View::exists('vendor.pagination.ruangcerdas-simple')) {
            Paginator::defaultSimpleView('vendor.pagination.ruangcerdas-simple');
        } else {
            Paginator::defaultSimpleView('pagination.ruangcerdas-simple-fallback');
        }

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
