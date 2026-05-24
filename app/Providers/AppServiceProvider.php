<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        \URL::forceRootUrl('https://' . $_SERVER['HTTP_X_FORWARDED_HOST']);
        \URL::forceScheme('https');
}
}
}