<?php

/**
 * Database connections.
 *
 * DB_CONNECTION picks which block below is used. DB_DRIVER is accepted as an
 * alias because it shipped in .env.example for several releases — a project
 * upgrading with only DB_DRIVER set keeps working rather than silently
 * falling back to SQLite.
 */

$connection = env('DB_CONNECTION', env('DB_DRIVER', 'sqlite'));

/**
 * Where the SQLite file lives.
 *
 * DB_SQLITE_PATH wins when set. Otherwise DB_DATABASE is used only if it
 * looks like a path, because that key doubles as a MySQL/Postgres database
 * name: a project that sets DB_DATABASE=myapp for MySQL and later switches to
 * SQLite would otherwise get an extension-less file literally called "myapp",
 * created without complaint, and wonder where its data went.
 */
$sqlitePath = static function (): string {
    if ($explicit = env('DB_SQLITE_PATH')) {
        return $explicit;
    }

    $value = env('DB_DATABASE');

    if (is_string($value) && $value !== '') {
        $looksLikeAPath = str_contains($value, '/')
            || str_contains($value, '\\')
            || str_ends_with($value, '.sqlite')
            || str_ends_with($value, '.sqlite3')
            || str_ends_with($value, '.db')
            || $value === ':memory:';

        if ($looksLikeAPath) {
            return $value;
        }
    }

    return database_path('database.sqlite');
};

return [
    'default' => $connection,

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',

            'database' => $sqlitePath(),
            'prefix'   => '',
        ],

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'port'      => env('DB_PORT', '3306'),
            'database'  => env('DB_DATABASE', 'libxa'),
            'username'  => env('DB_USERNAME', 'root'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', '127.0.0.1'),

            // Not DB_PORT's MySQL default: a project switching to Postgres
            // without touching DB_PORT would otherwise dial 3306 and get a
            // connection error that says nothing about the port being wrong.
            'port'     => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'libxa'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'schema'   => env('DB_SCHEMA', 'public'),
            'sslmode'  => env('DB_SSLMODE', 'prefer'),
            'prefix'   => '',
        ],
    ],
];
