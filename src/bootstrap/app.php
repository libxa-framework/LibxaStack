<?php

declare(strict_types=1);

/**
 * LibxaFrame App Bootstrapper
 *
 * This file creates the application instance and configures
 * the base environment.
 */

$app = new Libxa\Foundation\Application(
    realpath(__DIR__ . '/../../')
);

// Register application service providers here
// $app->register(App\Providers\AppServiceProvider::class);

return $app;
