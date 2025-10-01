<?php

namespace TCEMT\KeyCloak\Http\Controllers;

use App\Databases\Models\Usuario;
use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class KeyCloakController extends Controller {

    /**
     * @return RedirectResponse
     */
    public function redirect(Request $request): RedirectResponse
    {
        return Socialite::driver('keycloak')->redirect();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function callback(Request $request): RedirectResponse
    {
        $userData = Socialite::driver('keycloak')->user();
        $token = $userData->token;
        $idToken = $userData->accessTokenResponseBody['id_token'];
        $refreshToken = $userData->refreshToken;
        $jwtTokenFile = config('services.keycloak.key_file');

        if(!file_exists(storage_path($jwtTokenFile))) {
            throw new Exception('Arquivo JWT Token não encontrado!');
        }

        $usuario = Usuario::query()->where('logon', strtoupper($userData->nickname))->first();
        if (!$usuario) {
            $validator = Validator::make([], []);
            $validator->errors()->add('login', 'Usuário não localizado na base de dados local.');
            return redirect(route('login'))->withErrors($validator)->withInput();
        }

        $publicKey = file_get_contents(storage_path($jwtTokenFile));
        
        $loadCredentials = config('services.keycloak.load_credentials');
        if($loadCredentials) {
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
            $clientId = config('services.keycloak.client_id');
            $permissoes = $decoded->resource_access?->$clientId ?? null;
            session()->put('permissoes', $permissoes->roles ?? []);
        }

        session()->put('keycloak_auth_token', $token);
        session()->put('keycloak_auth_id_token', $idToken);
        session()->put('keycloak_auth_refresh_token', $refreshToken);

        // autenticação do usuário no sistema
        Auth::login($usuario);
        return response()->redirectToRoute('dashboard.index');
    }

    /**
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();
        Session::flush();
        return redirect(route('login'));
    }
}
