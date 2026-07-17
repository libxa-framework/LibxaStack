<?php

use Libxa\Router\Router;
use App\Http\Controllers\Api\ApiController;

/** @var Router $router */

// The 'api' guard securely uses the Bearer token lookup from personal_access_tokens
$router->group(['middleware' => 'auth:api'], function (Router $router) {

    // Fetch currently authenticated user
    $router->get('/api/user', [ApiController::class, 'user']);

});

// Public API token introspection (example)
$router->get('/api/ping', [ApiController::class, 'ping']);
