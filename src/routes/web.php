<?php

use Libxa\Router\Router;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/** @var Router $router */

// ── Welcome ───────────────────────────────────────────────────────────
$router->get('/', function () {
    return view('welcome', [
        'framework' => 'LibxaFrame',
        'version'   => '0.0.1',
    ]);
});

// ── Auth ──────────────────────────────────────────────────────────────
$router->get('/login',  [LoginController::class, 'show']);
$router->post('/login', [LoginController::class, 'login']);
$router->post('/logout', [LoginController::class, 'logout'])->middleware('auth');

$router->get('/register',  [RegisterController::class, 'show']);
$router->post('/register', [RegisterController::class, 'store']);

// ── Protected area ────────────────────────────────────────────────────
$router->group(['middleware' => 'auth'], function (Router $router) {
    $router->get('/home', function () {
        return view('welcome', [
            'framework' => 'LibxaFrame',
            'version'   => '0.0.1',
            'user'      => auth()->user(),
        ]);
    });
});
