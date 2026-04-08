<?php

/**
 * LibxaFrame Router Script
 * 
 * This script allows us to emulate Apache's "mod_rewrite" functionality 
 * when using the PHP built-in development server.
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test 
// a Libxa application without having installed a "real" web server software.
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

require_once __DIR__ . '/index.php';
