<?php

test('application can boot correctly', function () {
    // We mock the base path for testing
    $app = new \Libxa\Foundation\Application(base_path());
    
    expect($app)->toBeObject();
    expect($app->version())->toBe('0.0.1');
});

test('it can resolve core bindings', function () {
    $app = new \Libxa\Foundation\Application(base_path());
    $app->boot();

    expect($app->make('config'))->toBeInstanceOf(\Libxa\Config\Config::class);
});
