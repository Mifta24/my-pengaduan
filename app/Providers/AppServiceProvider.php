<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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

        $defaultIdentity = [
            'site_name' => 'MyPengaduan',
            'short_name' => 'MyPengaduan',
            'area_name' => 'Gang Annur 2 RT 05',
            'footer_text' => 'Dikelola oleh Admin RT 05 untuk warga Gang Annur 2',
            'contact_email' => 'admin.rt05@gangannur2.local',
            'contact_phone' => '-',
        ];

        try {
            $settings = $defaultIdentity;

            if (Schema::hasTable('app_settings')) {
                $dbSettings = Cache::remember('app_identity_settings', 600, function () {
                    return AppSetting::query()->pluck('value', 'key')->toArray();
                });

                $settings = array_merge($defaultIdentity, $dbSettings);
            }

            View::share('appIdentity', $settings);
        } catch (\Throwable $e) {
            View::share('appIdentity', $defaultIdentity);
        }
    }
}
