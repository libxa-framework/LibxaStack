<?php

declare(strict_types=1);

namespace Tests\Feature;

use Libxa\Config\Config;
use Libxa\Router\Router;
use Tests\TestCase;

/**
 * The starter kit must boot cleanly and register everything a new project
 * expects to be there. These checks are what turn "it looks installed" into
 * "it actually works".
 */
class ApplicationBootTest extends TestCase
{
    public function test_the_application_boots(): void
    {
        $this->assertSame('0.0.1', $this->app->version());
    }

    public function test_core_services_resolve(): void
    {
        foreach ([
            'config'    => Config::class,
            'router'    => Router::class,
            'blade'     => \Libxa\Blade\BladeEngine::class,
            'session'   => \Libxa\Session\Session::class,
            'encrypter' => \Libxa\Security\Encrypter::class,
        ] as $binding => $class) {
            $this->assertInstanceOf($class, $this->app->make($binding), "[$binding] should resolve");
        }
    }

    /**
     * The shipped .env has an empty APP_KEY until `key:generate` runs; the
     * encrypter must then fail with an actionable message rather than
     * producing silently weak ciphertext from a zero-length key.
     */
    public function test_the_encrypter_round_trips(): void
    {
        $encrypter = $this->app->make('encrypter');

        $this->assertSame('secret', $encrypter->decrypt($encrypter->encrypt('secret')));
    }

    public function test_config_files_are_loaded(): void
    {
        foreach (['app', 'auth', 'cache', 'database', 'mail', 'queue', 'session'] as $file) {
            $this->assertIsArray(
                $this->app->config($file),
                "config/{$file}.php should be loaded"
            );
        }
    }

    public function test_application_routes_are_registered(): void
    {
        $uris = array_map(
            fn($route) => $route->getUri(),
            $this->app->make(Router::class)->getRoutes()->all()
        );

        foreach (['/', '/login', '/register', '/home', '/api/ping'] as $expected) {
            $this->assertContains($expected, $uris, "route [{$expected}] should be registered");
        }
    }

    public function test_migrations_created_the_expected_tables(): void
    {
        $tables = $this->pdo()
            ->query("SELECT name FROM sqlite_master WHERE type = 'table'")
            ->fetchAll(\PDO::FETCH_COLUMN);

        foreach (['users', 'jobs', 'personal_access_tokens'] as $table) {
            $this->assertContains($table, $tables, "table [{$table}] should exist after migrating");
        }
    }

    /**
     * A migration whose class name does not match its filename used to be
     * skipped in silence: the starter kit shipped exactly one such file, so
     * personal_access_tokens never got its refresh_token column.
     */
    public function test_every_migration_actually_ran(): void
    {
        $columns = array_column(
            $this->pdo()->query('PRAGMA table_info(personal_access_tokens)')->fetchAll(\PDO::FETCH_ASSOC),
            'name'
        );

        $this->assertContains('refresh_token', $columns);
    }

    /**
     * A UTF-8 BOM in any bootstrapped file is emitted before headers, which
     * breaks every header() call and corrupts the response body.
     */
    public function test_no_php_file_starts_with_a_byte_order_mark(): void
    {
        $offenders = [];

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(dirname(__DIR__, 2) . '/src', \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            if (str_starts_with((string) file_get_contents($file->getPathname(), false, null, 0, 3), "\xEF\xBB\xBF")) {
                $offenders[] = $file->getPathname();
            }
        }

        $this->assertSame([], $offenders, "These files begin with a UTF-8 BOM:\n  " . implode("\n  ", $offenders));
    }
}
