<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Libxa\Http\Response;

/**
 * Welcome Controller
 */
class WelcomeController
{
    /**
     * Show the public welcome page.
     */
    public function index(): Response
    {
        return view('welcome', [
            'framework' => 'LibxaFrame',
            'version'   => '0.0.1',
        ]);
    }
}
