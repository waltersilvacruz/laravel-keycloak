TCEMT KEYCLOAK
==============

# Instalação

### Requerimentos
- PHP 8.0+
- Laravel 9.0+


Instale o componente via comando do composer:
```
composer require equipe-web/laravel-keycloak
```

# Configuração

Abra o arquivo `bootstrap/providers.php` e adicione na lista de providers:
```
TCEMT\KeyCloak\Providers\KeyCloakServiceProvider::class
```

limpe o cache de configurações
```
php artisan config:cache
```


Edite o arquivo .env e adicione a configuração básicas para o componente de segurança:
```
KEYCLOAK_KEY_FILE="app/credentials/keycloak-jwt-public-key.txt" # JWT Public Key do KeyCloak 
KEYCLOAK_CLIENT_ID=client_id                                    # chave pública da aplicação dentro do REALM
KEYCLOAK_CLIENT_SECRET=secret                                   # chave secreta do aplicação dentro do REALM
KEYCLOAK_REDIRECT_URI="${APP_URL}/auth/callback"                # callback da autenticação oauth
KEYCLOAK_BASE_URL=https://d-iam.tce.mt.gov.br                   # url base do servidor KeyCloak
KEYCLOAK_REALM=master                                           # realm da aplicação
KEYCLOAK_ENABLED=true                                           # se FALSE ativa o bypass nas autorizações
```
Adicione as configurações do KeyCloak no arquivo `config/services.php`:
```
...
    'keycloak' => [
        'enabled' => env('KEYCLOAK_ENABLED', false),
        'key_file' => env('KEYCLOAK_KEY_FILE'),
        'client_id' => env('KEYCLOAK_CLIENT_ID'),
        'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
        'redirect' => env('KEYCLOAK_REDIRECT_URI'),
        'base_url' => env('KEYCLOAK_BASE_URL'),
        'realms' => env('KEYCLOAK_REALM')
    ],
...    
```

limpe o cache de configurações novamente
```
php artisan config:cache
```

Edite seu arquivo routes/web.php e utilize o novo controller para lidar com o precesso de autenticação. Exemplo:
```
Route::get('/auth/redirect', [\TCEMT\KeyCloak\Http\Controllers\KeyCloakController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/callback', [\TCEMT\KeyCloak\Http\Controllers\KeyCloakController::class, 'callback'])->name('auth.callback');
Route::get('/auth/logout', [\TCEMT\KeyCloak\Http\Controllers\KeyCloakController::class, 'logout'])->name('auth.logout');
```

Execute os comandos abaixo:
```
php artisan clear-compiled && composer dumpautoload && php artisan optimize
```

# Utilização

## Dentro de um Controller

Utilize o facade `Gate` para verificar as permissões dos usuários no controller.
O método `Gate::allows($recurso)` se encarrega de fazer a verificação e retorna verdadeiro ou falso.
```
<?php
...
use Secorphp;

class MeuController extends Controller {

    public function index() {
        // verifica acesso do usuário que está logado ao recurso
        if(Gate::allows('usuario_incluir') {
            // tem acesso ao recurso 
        }
    }
}
```

## Uso em templates do Blade

```
<h1>Pode acessar video?</h1>
@can("video_acessar")
<strong>Pode sim!</strong>
@else
<strong>ACESSO NEGADO!!</strong>
@end_can

<h1>Pode incluir materia?</h1>
@can("materia_incluir)
<strong>Claro que pode!</strong>
@else
<strong>ACESSO NEGADO!!</strong>
@end_can
```
