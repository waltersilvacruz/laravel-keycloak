<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API environment
    |--------------------------------------------------------------------------
    | Informa qual é o ambiente da API
    */
    'environment' => env('KEYCLOAK_ENVIRONMENT', null),

    /*
    |--------------------------------------------------------------------------
    | JWT Token
    |--------------------------------------------------------------------------
    | Informa qual é o arquivo que contem a chave JWT para acesso à API do Keycloak
    */
    'key_file' => env('KEYCLOAK_KEY_FILE', null),

    /*
    |--------------------------------------------------------------------------
    | API base URL
    |--------------------------------------------------------------------------
    | URL base da api
    */
    'api_base_url' => env('KEYCLOAK_API_BASE_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Client ID
    |--------------------------------------------------------------------------
    */
    'client_id' => env('KEYCLOAK_CLIENT_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Client Secret
    |--------------------------------------------------------------------------
    */
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET', null),

    /*
    |--------------------------------------------------------------------------
    | Auth User Model
    |--------------------------------------------------------------------------
    | Informa qual é o Model da tabela de usuários para autenticação.
    */
    'auth_model' => env('AUTH_USER_MODEL', null),

    /*
    |--------------------------------------------------------------------------
    | Auth User Login Field
    |--------------------------------------------------------------------------
    | Informa qual é o campo da tabela que contém o logon do usuário
    */
    'auth_login_field' => env('AUTH_USER_LOGIN_FIELD', 'login'),

    /*
    |--------------------------------------------------------------------------
    | Auth login route
    |--------------------------------------------------------------------------
    | Informa qual é a rota para a tela de login
    */
    'auth_login_route' => env('AUTH_LOGIN_ROUTE'),

    /*
    |--------------------------------------------------------------------------
    | Auth success route
    |--------------------------------------------------------------------------
    | Informa qual é a rota para redirecionamento quando o login for bem sucedido
    */
    'auth_login_success_route' => env('AUTH_LOGIN_SUCCESS_ROUTE'),

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    | Habilita/desabilita o middleware de verificação de credenciais
    */
    'keycloak_enabled' => env('KEYCLOAK_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | App
    |--------------------------------------------------------------------------
    | Informe aqui o nome do sistema cadastrado no campo SIS_IDENTIFICACAO do
    | sistema de segurança do TCE
    */
    'keycloak_app' => env('KEYCLOAK_APP', null),


    /*
    |--------------------------------------------------------------------------
    | Cache Enabled
    |--------------------------------------------------------------------------
    | Determina se deve fazer o cache das permissões do Secorp
    */
    'keycloak_cache_enabled' => env('KEYCLOAK_CACHE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Cache Timeout
    |--------------------------------------------------------------------------
    | Determina quanto tempo de duração do cache contendo as credenciais
    | recuperadas do banco. Esta ação visa reduzir o acesso ao banco de dados
    | de credenciais. Valores em MINUTOS.
    */
    'keycloak_cache_timeout' => env('KEYCLOAK_CACHE_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Mapeamento das rotas e processos
    |--------------------------------------------------------------------------
    | A chave do cabeçalho é o nome da rota e o valor é o nome do processo
    | (campo TABELA_PROCESSO do sistema de segurança). É possível utilizar "*"
    | para que o sistema mapeie várias rotas para o mesmo processo
    */
    'keycloak_rules' => [
        'home' => ['recurso' => 'HOME'],
        'dashboard.*' => ['recurso' => 'DASHBOARD'],
        'usuario.index' => ['recurso' => 'USUARIO', 'acao' => 'ACESSAR'],
        'usuario.incluir' => ['recurso' => 'USUARIO', 'acao' => 'INCLUIR'],
        'usuario.editar' => ['recurso' => 'USUARIO', 'acao' => 'ALTERAR'],
        'usuario.excluir' => ['recurso' => 'USUARIO', 'acao' => 'EXCLUIR']
    ],

    /*
    |--------------------------------------------------------------------------
    | Rotas ignoradas
    |--------------------------------------------------------------------------
    | Informe aqui as rotas que deseja ignorar (bypass). Não é possível colocar
    | "*" no nome das rotas.
    */
    'keycloak_ignore_routes' => [
        'logout',
    ],
];
