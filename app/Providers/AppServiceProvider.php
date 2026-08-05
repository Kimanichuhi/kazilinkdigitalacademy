<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Password::defaults(function () {
            $rule = Password::min(12)->mixedCase()->numbers()->symbols();

            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });

        // Shared hosting (e.g. HostPinnacle/cPanel) commonly sits behind a
        // proxy/CDN that terminates TLS, so the app server sees plain HTTP
        // even though the visitor is on HTTPS. Force https:// in generated
        // URLs/asset links whenever the app isn't running locally.
        if (! $this->app->isLocal()) {
            URL::forceScheme('https');
        }
    }
}
