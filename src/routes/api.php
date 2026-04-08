<?php

use Libxa\Router\Router;
use Libxa\Http\Request;
use Libx\Billing\Http\Controllers\BillingController;

/** @var Router $router */

// The 'api' guard securely uses the Bearer token lookup from personal_access_tokens
$router->group(['middleware' => 'auth:api'], function ($router) {
    
    // Example: Fetch currently authenticated user
    $router->get('/api/user', function (Request $request) {
        return [
            'status' => 'success',
            'user' => auth('api')->user()
        ];
    });

});

  $router->get('/api/token', function (Request $request) {
        return [
            'status' => 'success',
            'user' => (new BillingController())->index()
        ];
    });