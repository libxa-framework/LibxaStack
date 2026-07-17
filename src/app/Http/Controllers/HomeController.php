<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Libxa\Http\Response;

/**
 * Home Controller
 *
 * The authenticated landing page, shown after login (behind the "auth"
 * middleware — see routes/web.php).
 */
class HomeController
{
    public function index(): Response
    {
        return view('welcome', [
            'framework' => 'LibxaFrame',
            'version'   => '0.0.1',
            'user'      => auth()->user(),
        ]);
    }
}
