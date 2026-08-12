<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Routing works locally because `php libxa serve` passes `src/public/router.php`
 * to PHP's built-in server, which emulates mod_rewrite. A real web server does
 * not, so without the shipped rewrite rules the home page loads and every other
 * route returns the server's own 404 before PHP is reached.
 *
 * That failure only shows up after deploying, which is the worst time to find
 * it. These checks fail in CI instead, the moment the rules are dropped or
 * edited into something that no longer routes.
 */
class DeploymentConfigTest extends TestCase
{
    private function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public function test_the_public_htaccess_ships_with_the_kit(): void
    {
        $this->assertFileExists(
            $this->root() . '/src/public/.htaccess',
            'src/public/.htaccess is what makes routing work on Apache.'
        );
    }

    public function test_the_public_htaccess_sends_unmatched_paths_to_the_front_controller(): void
    {
        $rules = (string) file_get_contents($this->root() . '/src/public/.htaccess');

        $this->assertMatchesRegularExpression(
            '/RewriteEngine\s+On/i',
            $rules,
            'The rewrite engine has to be switched on explicitly.'
        );

        // Both conditions matter. Without !-f every real asset would be handed
        // to PHP; without !-d a directory request would be too.
        $this->assertMatchesRegularExpression(
            '/RewriteCond\s+%\{REQUEST_FILENAME\}\s+!-d/i',
            $rules,
            'Existing directories must be served directly, not routed.'
        );
        $this->assertMatchesRegularExpression(
            '/RewriteCond\s+%\{REQUEST_FILENAME\}\s+!-f/i',
            $rules,
            'Existing files must be served directly, not routed.'
        );
        $this->assertMatchesRegularExpression(
            '/RewriteRule\s+\^\s+index\.php\s+\[L\]/i',
            $rules,
            'Everything else has to reach index.php or routing does not happen.'
        );
    }

    public function test_the_public_htaccess_preserves_the_authorization_header(): void
    {
        // CGI and FastCGI strip it, which silently breaks token authentication
        // on exactly the servers people deploy to.
        $this->assertStringContainsString(
            'HTTP_AUTHORIZATION',
            (string) file_get_contents($this->root() . '/src/public/.htaccess')
        );
    }

    public function test_the_root_htaccess_covers_shared_hosting(): void
    {
        $path = $this->root() . '/.htaccess';

        $this->assertFileExists(
            $path,
            'Shared hosts serve the project root, so it needs its own rules.'
        );

        $rules = (string) file_get_contents($path);

        $this->assertMatchesRegularExpression(
            '#RewriteRule\s+\^\(\.\*\)\$\s+/src/public/\$1#i',
            $rules,
            'Requests have to be rewritten into src/public/.'
        );

        // Without the guard this rule matches its own output and loops.
        $this->assertMatchesRegularExpression(
            '#RewriteCond\s+%\{REQUEST_URI\}\s+!\^/src/public/#i',
            $rules,
            'The loop guard is what stops src/public/src/public/... forever.'
        );
    }

    public function test_the_root_htaccess_refuses_the_paths_that_must_never_be_served(): void
    {
        $rules = (string) file_get_contents($this->root() . '/.htaccess');

        // On shared hosting these sit inside the web root, so a missing deny
        // means .env and its APP_KEY are a plain GET away.
        foreach (['vendor', 'src/(app', 'storage'] as $needle) {
            $this->assertStringContainsString($needle, $rules);
        }

        $this->assertStringContainsString('\.env', $rules);
    }

    public function test_the_local_rewrite_shim_still_exists(): void
    {
        $path = $this->root() . '/src/public/router.php';

        $this->assertFileExists(
            $path,
            'php -S needs router.php to behave like a server with mod_rewrite.'
        );

        $this->assertStringContainsString(
            'index.php',
            (string) file_get_contents($path),
            'The shim exists to fall through to the front controller.'
        );
    }

    public function test_deployment_is_documented(): void
    {
        $this->assertFileExists($this->root() . '/docs/DEPLOYMENT.md');

        $docs = (string) file_get_contents($this->root() . '/docs/DEPLOYMENT.md');

        // The nginx equivalent of the Apache rules, and the mistake that
        // reproduces the same 404 on a server that has no .htaccess at all.
        $this->assertStringContainsString('try_files $uri $uri/ /index.php', $docs);
    }
}
