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

Execute o comando abaixo para criar o arquivo de configuração:
```
php artisan vendor:publish --tag="keycloak"
```

Edite o arquivo .env e adicione a configuração básicas para o componente de segurança:
```
# configurações de autenticação
AUTH_LOGIN_ROUTE=login # rota da página de logon na aplicação
AUTH_LOGIN_SUCCESS_ROUTE=home # rota de redirecionamento  quando o logon for bem sucedido
AUTH_USER_MODEL=App\User # modelo do usuário
AUTH_USER_LOGIN_FIELD=logon # campo referente ao logon do usuário

#configuração de autorização do Secorp
KEYCLOAK_KEY_FILE=app/credentials/keycloak-jwt-public-key.txt  # JWT Public Key do KeyCloak 
KEYCLOAK_API_BASE_URL=https://am.tce.mt.gov.br # url base do API Manager
KEYCLOAK_CLIENT_ID=chave # chave pública da aplicação dentro do REALM
KEYCLOAK_CLIENT_SECRET=chave # chave secreta do aplicação dentro do REALM
KEYCLOAK_ENABLED=true/false # habilita ou desabilita a verificação de segurança
KEYCLOAK_CACHE_ENABLED=true/false # habilita ou desabilita o cache das credenciais de acesso
KEYCLOAK_CACHE_TIMEOUT=30 # tempo em minutos
```

limpe o cache de configurações novamente
```
php artisan config:cache
```

Edite seu arquivo app/Http/routes.php e utilize o novo controller para lidar com o precesso de autenticação. Exemplo:
```
Route::post('/login', '\TCEMT\KeyCloak\Http\Controllers\KeyCloakController@autentica')->name('autentica');
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
