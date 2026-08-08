<?php

declare(strict_types=1);

namespace Tests;

use Libxa\Atlas\Connection\ConnectionPool;
use Libxa\Foundation\Application;
use Libxa\Foundation\HttpKernel;
use Libxa\Http\Request;
use Libxa\Http\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base test case for the starter application.
 *
 * Boots the real application against a per-test SQLite database and drives
 * requests through the actual HTTP kernel, so a test failure means the app
 * is genuinely broken rather than a mock having drifted.
 */
abstract class TestCase extends BaseTestCase
{
    protected Application $app;
    protected string $databasePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->databasePath = sys_get_temp_dir() . '/libxa_stack_test_' . bin2hex(random_bytes(6)) . '.sqlite';
        touch($this->databasePath);

        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DRIVER=sqlite');
        putenv('DB_DATABASE=' . $this->databasePath);

        ConnectionPool::resetInstance();

        $this->app = new Application(dirname(__DIR__));
        $this->app->boot();

        $this->migrate();

        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        ConnectionPool::resetInstance();

        @unlink($this->databasePath);

        putenv('DB_DATABASE');
        $_SESSION = [];

        parent::tearDown();
    }

    /**
     * Apply the application's migrations to the throwaway database.
     */
    protected function migrate(): void
    {
        $pdo = new \PDO('sqlite:' . $this->databasePath);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        ConnectionPool::getInstance()->setConnection('default', $pdo);

        $migrator = new \Libxa\Atlas\Migrations\Migrator();
        $migrator->addPath($this->app->basePath('src/database/migrations'));
        $migrator->run();
    }

    /**
     * Send a request through the real HTTP kernel.
     */
    protected function call(
        string $method,
        string $uri,
        array $post = [],
        array $headers = [],
    ): Response {
        $request = new Request(
            method: $method,
            uri: $uri,
            headers: $headers,
            query: [],
            post: $post,
            files: [],
            server: [
                'REMOTE_ADDR'    => '127.0.0.1',
                'HTTP_HOST'      => 'localhost',
                'REQUEST_METHOD' => $method,
                'REQUEST_URI'    => $uri,
            ],
            cookies: [],
        );

        return $this->app->make(HttpKernel::class)->handle($request);
    }

    protected function get(string $uri, array $headers = []): Response
    {
        return $this->call('GET', $uri, [], $headers);
    }

    /**
     * POST with a valid CSRF token already in place.
     */
    protected function post(string $uri, array $data = [], array $headers = []): Response
    {
        return $this->call('POST', $uri, array_merge(['_token' => $this->csrfToken()], $data), $headers);
    }

    protected function csrfToken(): string
    {
        if (! isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_token'];
    }

    protected function pdo(): \PDO
    {
        return ConnectionPool::getInstance()->get('default');
    }

    protected function assertDatabaseHas(string $table, array $where): void
    {
        $clauses = [];
        $values  = [];

        foreach ($where as $column => $value) {
            $clauses[] = "\"$column\" = ?";
            $values[]  = $value;
        }

        $stmt = $this->pdo()->prepare("SELECT COUNT(*) FROM \"$table\" WHERE " . implode(' AND ', $clauses));
        $stmt->execute($values);

        $this->assertGreaterThan(
            0,
            (int) $stmt->fetchColumn(),
            "Expected a row in [$table] matching " . json_encode($where)
        );
    }

    protected function assertDatabaseMissing(string $table, array $where): void
    {
        $clauses = [];
        $values  = [];

        foreach ($where as $column => $value) {
            $clauses[] = "\"$column\" = ?";
            $values[]  = $value;
        }

        $stmt = $this->pdo()->prepare("SELECT COUNT(*) FROM \"$table\" WHERE " . implode(' AND ', $clauses));
        $stmt->execute($values);

        $this->assertSame(
            0,
            (int) $stmt->fetchColumn(),
            "Did not expect a row in [$table] matching " . json_encode($where)
        );
    }
}
