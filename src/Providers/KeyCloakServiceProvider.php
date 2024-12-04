<?php

namespace TCEMT\KeyCloak\Providers;

use Illuminate\Support\ServiceProvider;

class KeyCloakServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Run service provider boot operations.
     *
     * @return void
     */
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

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('keycloak', function ($app) {
            
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return ['keycloak'];
    }
}
