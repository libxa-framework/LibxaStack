<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Libxa\Http\Request;

/**
 * API Controller
 *
 * Small example endpoints for the API guard. Return values are plain
 * arrays — the router JSON-encodes array/toArray()-able results
 * automatically (see Router::toResponse()).
 */
class ApiController
{
    /**
     * Fetch the currently authenticated API user.
     * Protected by the "auth:api" middleware group (see routes/api.php).
     */
    public function user(Request $request): array
    {
        return [
            'status' => 'success',
            'user'   => auth('api')->user(),
        ];
    }

    /**
     * Public health-check / token introspection example.
     */
    public function ping(Request $request): array
    {
        return [
            'status'  => 'ok',
            'message' => 'LibxaFrame API is running',
            'time'    => date('c'),
        ];
    }
}
