<?php

namespace TCEMT\KeyCloak\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
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
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('keycloak', \SocialiteProviders\Keycloak\Provider::class);
        });

        // Register Middleware
        $this->app['router']->middlewareGroup('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \TCEMT\KeyCloak\Http\Middleware\KeyCloakMiddleware::class,
        ]);

        // Register routes
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        // Register blade directives
        Blade::directive('keycloak_login_url', function ($expression) {
            list($label, $cssClasses) = explode(',',$expression);
            if($label) {
                $label = substr(trim($label), 1, -1);
                $label = str_replace('"', "'", $label);
            }
            if($cssClasses) {
                $cssClasses = substr(trim($cssClasses), 1, -1);
                $cssClasses = str_replace('"', "'", $cssClasses);
            }

            $link = env("KEYCLOAK_LOGIN_REDIRECT_URL", route('auth.redirect'));
            return "<a href=\"{$link}\" class=\"{$cssClasses}\">{$label}</a>";
        });

        // Register blade directives
        Blade::directive('keycloak_logout_url', function ($expression) {
            list($label, $cssClasses) = explode(',',$expression);
            if($label) {
                $label = substr(trim($label), 1, -1);
                $label = str_replace('"', "'", $label);
            }
            if($cssClasses) {
                $cssClasses = substr(trim($cssClasses), 1, -1);
                $cssClasses = str_replace('"', "'", $cssClasses);
            }

            $appUrl = env("KEYCLOAK_LOGOUT_URL", route('auth.logout'));
            $idTokenHint = session()->get('keycloak_auth_id_token');
            $link = \Laravel\Socialite\Facades\Socialite::driver('keycloak')->getLogoutUrl($appUrl, env('KEYCLOAK_CLIENT_ID'), $idTokenHint);
            return "<a href=\"{$link}\" class=\"{$cssClasses}\">{$label}</a>";
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('keycloak', function ($app) {
            return new \TCEMT\KeyCloak\Helpers\KeyCloak();
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
