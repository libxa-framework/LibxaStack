<?php

declare(strict_types=1);

/**
 * LibxaFrame Web Entry Point
 *
 * All web requests are funneled through this script.
 */

define('Libxa_START', microtime(true));

// 1. Load the composer autoloader
require __DIR__ . '/../../vendor/autoload.php';

// 2. Bootstrap the application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Handle the request via the HTTP Kernel
$kernel = $app->make(Libxa\Foundation\HttpKernel::class);

$response = $kernel->handle(
    $request = Libxa\Http\Request::capture()
);

// 4. Send the response to the browser
$response->send();

// 5. Terminate the request lifecycle
$kernel->terminate($request, $response);
