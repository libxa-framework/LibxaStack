<?php

use Libxa\Router\Router;

/** @var Router $router */

/**
 * Libxa Discovery — Welcome
 * The entry point to your brand new LibxaFrame application.
 */
$router->get('/', function () {
    return view('welcome', [
        'framework' => 'LibxaFrame',
        'version'   => '1.0.0'
    ]);
});
