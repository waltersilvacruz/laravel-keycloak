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
KEYCLOAK_ENABLED=true/false                                     # se FALSE ativa o bypass nas autorizações
KEYCLOAK_CACHE_TIMEOUT=30                                       # tempo de timeout em segundos para as requisições HTTP
KEYCLOAK_ENABLED=true/false                                     # se TRUE ativa o KeyCloak na aplicação
KEYCLOAK_LOAD_CREDENTIALS=true/false                            # se TRUE ele puxa as credenciais do KeyClock
KEYCLOAK_JWT_LEEWAY=0                                           # tempo de tolerância em segundos para expiração do token JWT    
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
        'realm' => env('KEYCLOAK_REALM'),
        'enabled' => env('KEYCLOAK_ENABLED'),
        'load_credentials' => env('KEYCLOAK_LOAD_CREDENTIALS', false),
        'jwt_leeway' => env('KEYCLOAK_JWT_LEEWAY', 0),
    ],
...    
```

limpe o cache de configurações novamente
```
php artisan config:cache
```

Execute os comandos abaixo:
```
php artisan clear-compiled && composer dumpautoload && php artisan optimize
```

# Utilização

## Links para login e logout via Oauth2

O ServiceProvider desta biblioteca automaticamente irá registrar as seguintes rotas
para o processo de autenticação:
- `GET /auth/redirect`: esta rota irá redirecionar a aplicação para a autenticação Oauth2 no servidor KeyCloak. 
- `GET /auth/callback`: após validar o login e senha, o usuário será redirecionado para esta rota que finalizará o processo de autenticação e autorização.
- `GET /auth/logout`: efetua o logout do usuário

#### Diretivas Blade para gerar os links de login/logout

Dentro do template blade você pode utilizar a diretiva `@keycloak_login_url($label, $css)` e `@keycloak_logout_url($label, $css)` para gerar os links de login e logout respectivamente.

Exemplo:
```HTML
<!-- gera o link de login -->
<div class="d-grid">
    @keycloak_login_url("Login", "btn btn-primary")
</div>

<!-- para logout -->
<div class="d-grid">
    @keycloak_logout_url("Logout", "btn btn-primary")
</div>

```
Se você utilizar a diretiva de logout, o usuário será redirecionado para a tela de logout do KeyCloak e depois será redirecionado para a URL de logout da sua aplicação. Dessa forma, ao efetuar o logout a sessão do usuário será encerrada nas duas aplicações. Caso você queira que o usuário seja desconectado apenas da sua aplicação e mantenha a sessão autenticada no KeyClock (para logar novamente sem precisar redigitar o login/senha) você deve utilizar a rota `auth.logout` ao invés da diretiva:
```
<a href="{{route('auth.logout')}}">Desconectar</a>
```

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
