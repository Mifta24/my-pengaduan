<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Disable Scribe config in production if class doesn't exist (dev dependency)
        if (!class_exists(\Knuckles\Scribe\ScribeServiceProvider::class)) {
            $this->app->make('config')->set('scribe', []);
        }
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
