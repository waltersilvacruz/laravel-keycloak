<?php

namespace TCEMT\KeyCloak\Providers;

use Illuminate\Support\ServiceProvider;

class KeyCloakServiceProvider extends ServiceProvider {

    public function boot()
    {
        $keycloak = __DIR__ . '/../Config/keycloak.php';

        // Add publishable configuration.
        $this->publishes([
            $keycloak => config_path('keycloak.php'),
        ], 'keycloak');

        // Register Middleware
        $this->app['router']->middlewareGroup('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \TCEMT\KeyCloak\Http\Middleware\KeyCloakMiddleware::class,
        ]);
    }
}
