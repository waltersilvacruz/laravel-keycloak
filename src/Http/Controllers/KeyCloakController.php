<?php

namespace TCEMT\KeyCloak\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class KeycloakController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function autentica(Request $request)
    {
        $username = $request->input('login');
        $password = $request->input('senha');

        // Validação dos campos
        $validator = Validator::make(
            ['login' => $username, 'senha' => $password],
            ['login' => 'required', 'senha' => 'required|min:3'],
            ['required' => ':attribute é obrigatório', 'min' => ':attribute deve ter ao menos :min caracteres']
        );

        if ($validator->fails()) {
            return redirect(route(config('keycloak_auth_login_route')))
                ->withErrors($validator)
                ->withInput();
        }

        try {

            // Fazer a requisição ao Keycloak
            $response = Http::asForm()->post(config('keycloak_api_base_url'), [
                'client_id' => config('keycloak_client_id'),
                'client_secret' => config('keycloak_client_secret'),
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password,
                'scope' => 'openid',
            ]);

            $jwtTokenFile = config('keycloak.key_file');
            if(!file_exists(storage_path($jwtTokenFile))) {
                throw new Exception('Arquivo JWT Token não encontrado!');
            }

            $publicKey = readfile(storage_path($jwtTokenFile));

            if ($response->successful()) {
                $tokenData = $response->json();
                // Suponha que $tokenData['access_token'] contém o JWT
                $token = $tokenData['access_token'];

                $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
                $permissoes = $decoded->resource_access->webadmin_client;

                Gate::define('has-permission', function ($user, $permissoes) {
                    $permissions = session('user_permissions', []);
                    return in_array($permissoes, $permissions);
                });

                $model = config('keycloak_auth_model');
                $loginField = config('keycloak_auth_login_field');
                $usuario = $model::where($loginField, strtoupper($username))->first();

                if (!$usuario) {
                    $validator->errors()->add('senha', 'Usuário não localizado na base de dados local.');
                    return redirect(route(config('keycloak_auth_login_route')))->withErrors($validator)->withInput();
                }

                // Login do usuário no Laravel
                Auth::login($usuario);

                // Redireciona para o local definido
                return redirect(route(config('keycloak_auth_login_success_route')));
            } else {
                $errorMessage = $response->json('error_description', 'Login ou senha inválidos');
                $validator->errors()->add('senha', $errorMessage);
                return redirect(route(config('keycloak_auth_login_route')))->withErrors($validator)->withInput();
            }
        } catch (\Exception $e) {
            $validator->errors()->add('senha', 'Erro durante a autenticação: ' . $e->getMessage());
            return redirect(route(config('keycloak_auth_login_route')))->withErrors($validator)->withInput();
        }
    }

    /**
     * Logout do usuário.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect(route(config('keycloak_auth_login_route')));
    }
}

