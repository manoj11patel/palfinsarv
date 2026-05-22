<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        // Compatibility for older MySQL/MariaDB index limits.
        Schema::defaultStringLength(191);

        // Use Bootstrap 5 pagination view
        Paginator::useBootstrapFive();
    }
}
