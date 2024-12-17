<?php

namespace TCEMT\KeyCloak\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Gate;

class KeyCloakMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $permissoes = $request->session()->get('permissoes');
        if(!config('services.keycloak.enabled')) {
            Gate::before(function () {
                return true;
            });
        } else {
            if($permissoes) {
                foreach ($permissoes as $permissao) {
                    Gate::define($permissao, function ($user) {
                        return true;
                    });
                }
            }
        }
        return $next($request);
    }
}
