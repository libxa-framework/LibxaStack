<?php

use Libxa\Router\Router;
use Libxa\Http\Request;

/** @var Router $router */

// The 'api' guard securely uses the Bearer token lookup from personal_access_tokens
$router->group(['middleware' => 'auth:api'], function ($router) {

    // Fetch currently authenticated user
    $router->get('/api/user', function (Request $request) {
        return [
            'status' => 'success',
            'user'   => auth('api')->user(),
        ];
    });

});

// Public API token introspection (example)
$router->get('/api/ping', function (Request $request) {
    return [
        'status'  => 'ok',
        'message' => 'LibxaFrame API is running',
        'time'    => date('c'),
    ];
});
