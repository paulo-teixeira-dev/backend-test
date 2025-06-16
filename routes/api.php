<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HealthCheckController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * sugestão:
 * 
 * Agrupar rotas públicas e privadas usando prefixos como /auth e /public.
 *  - grupo /auth: 
 *      - utilizar middleware de autenticação.
 *      - utilizar middleware de permissão sobre rotas as rotas deste grupo.
 * Utilizar versionamento nas rotas (/api/v1/...).
 *  - Mesmo que o projeto ainda esteja em fase inicial, usar um prefixo como “/v1”, etc. facilita para evoluções futuras.
 * 
 * Melhorias: 
 * 
 * Nomear todas as rotas
 *  - Se a URL da rota mudar, você não precisa alterar todos os pontos onde ela é usada (como route()). Basta atualizar o nome da rota no arquivo de rotas.
 *  - Utilizar padrão de nomeação com exemplo “api.auth.login” onde diz ser rota exclusiva da api, utilizando a feature de auth (autenticação) e o método login.
 */


// Healthcheck
Route::get('healthcheck', [HealthCheckController::class, 'healthCheck']);

// Users (Rota Pública)
Route::prefix('users')->group(function () {
    Route::post('register', [UserController::class, 'register']);

    Route::post('login', [UserController::class, 'login'])->middleware('auth.basic');
});

Route::group(['middleware' => ['auth:sanctum', 'policies.app']], function () {
    // Companies
    Route::prefix('company')->group(function () {
        Route::get('', [CompanyController::class, 'show']);

        Route::patch('', [CompanyController::class, 'update']);
    });

     /**
     * melhorias: Usar Route::apiResources para controllers RESTful, eliminando declarações repetitivas
     *  - Exemplo eliminaria a necessidade de declarar cada método (index, show, create e update) e adicionando “except” para rota de exclusão.
     */

    // Users
    Route::prefix('users')->group(function () {
        Route::get('', [UserController::class, 'index']);

        Route::get('{id}', [UserController::class, 'show']);

        Route::post('', [UserController::class, 'create']);

        Route::patch('{id}', [UserController::class, 'update']);

         /**
         * melhorias: Agrupamento por responsabilidade
         *  - As rotas de Account e Card poderiam ser agrupadas de acordo com suas responsabilidades específicas, 
         *    em vez de estarem totalmente aninhadas dentro do grupo “users”.
         *  - Uma estrutura mais organizada seria, por exemplo:
         *      - Grupo de Account: users/{user}/account
         *      - Grupo de Card: users/{user}/card
         */

        // Accounts
        Route::prefix('{id}/account')->group(function () {
            Route::get('', [AccountController::class, 'show']);

            Route::put('active', [AccountController::class, 'active']);

            Route::put('block', [AccountController::class, 'block']);

            Route::post('register', [AccountController::class, 'register']);
        });

        Route::prefix('{id}/card')->group(function () {
            Route::get('', [CardController::class, 'show']);

            Route::post('register', [CardController::class, 'register']);
        });
    });
});
