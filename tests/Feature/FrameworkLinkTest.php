<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

/**
 * Guards the framework <-> starter-kit wiring.
 *
 * The starter kit previously carried a *mirrored copy* of the framework in
 * vendor/libxa/framework. Nothing kept it in step with the real source at
 * ../libxaframe, and the two duly drifted apart in both directions: the vendor
 * copy had console commands the source lacked, the source had bug fixes and
 * whole directories the vendor copy lacked. Every "fix" applied to the
 * framework simply had no effect on the application until someone remembered
 * to re-run composer.
 *
 * The path repository now uses symlink:true, so vendor/libxa/framework *is*
 * ../libxaframe (a junction on Windows). Framework edits are live immediately
 * and the two can no longer disagree.
 *
 * These checks only apply when the sibling checkout is present. A real install
 * (CI, production, `composer create-project`) resolves libxa/framework from
 * Packagist into a normal directory, which is correct, so they skip instead
 * of failing there.
 */
class FrameworkLinkTest extends TestCase
{
    private string $vendorPath;
    private string $sourcePath;

    protected function setUp(): void
    {
        parent::setUp();

        $root             = dirname(__DIR__, 2);
        $this->vendorPath = $root . '/vendor/libxa/framework';
        $this->sourcePath = dirname($root) . '/libxaframe';

        if (! is_dir($this->sourcePath)) {
            $this->markTestSkipped('No sibling framework checkout: installed from Packagist.');
        }
    }

    /**
     * Whether Composer actually resolved the framework through the path
     * repository, as recorded in the lock file.
     *
     * The sibling checkout merely *existing* is not enough to demand a link:
     * re-locking for a release deliberately resolves from Packagist while the
     * checkout is still on disk, and that is a correct, expected state. The
     * lock is the authority on which source was chosen.
     */
    private function lockedFromPath(): bool
    {
        $lock = json_decode(
            (string) file_get_contents(dirname(__DIR__, 2) . '/composer.lock'),
            true
        );

        foreach ($lock['packages'] ?? [] as $package) {
            if (($package['name'] ?? '') === 'libxa/framework') {
                return ($package['dist']['type'] ?? '') === 'path';
            }
        }

        return false;
    }

    public function test_vendor_is_a_link_to_the_framework_source_not_a_copy(): void
    {
        if (! $this->lockedFromPath()) {
            $this->markTestSkipped('Lock resolves the framework from Packagist, so vendor is a real directory.');
        }

        $this->assertDirectoryExists($this->vendorPath);

        $realVendor = realpath($this->vendorPath);
        $realSource = realpath($this->sourcePath);

        $this->assertSame(
            $realSource,
            $realVendor,
            "vendor/libxa/framework must resolve to the framework source.\n"
            . "It currently resolves to: {$realVendor}\n"
            . "Fix: set repositories[0].options.symlink to true in composer.json, "
            . "then `rm -rf vendor/libxa && composer update libxa/framework`."
        );
    }

    /**
     * The two copies drifted in *both* directions last time, so compare file
     * lists rather than just checking that one path exists.
     */
    public function test_the_framework_source_tree_is_identical_through_vendor(): void
    {
        if (! $this->lockedFromPath()) {
            $this->markTestSkipped('Lock resolves the framework from Packagist; a version difference is expected.');
        }

        $listing = function (string $base): array {
            $files = [];

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($base . '/src', \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'php') {
                    $files[] = str_replace('\\', '/', substr($file->getPathname(), strlen($base)));
                }
            }

            sort($files);

            return $files;
        };

        $this->assertSame(
            $listing($this->sourcePath),
            $listing($this->vendorPath),
            'vendor/libxa/framework has drifted from the framework source.'
        );
    }

    /**
     * The composer.json contract itself: symlink must stay on, and the url
     * must stay a glob so a missing sibling directory falls back to Packagist
     * instead of aborting Composer outright.
     */
    public function test_the_path_repository_is_configured_for_live_linking(): void
    {
        $composer = json_decode(
            (string) file_get_contents(dirname(__DIR__, 2) . '/composer.json'),
            true
        );

        $pathRepos = array_values(array_filter(
            $composer['repositories'] ?? [],
            static fn(array $repo): bool => ($repo['type'] ?? '') === 'path'
        ));

        $this->assertNotEmpty($pathRepos, 'the local framework path repository is missing');

        $repo = $pathRepos[0];

        $this->assertTrue(
            $repo['options']['symlink'] ?? false,
            'symlink must be true so vendor tracks the framework source live'
        );

        $this->assertStringContainsString(
            '*',
            (string) $repo['url'],
            'the url must be a glob: a literal missing path makes Composer abort'
        );

        // A canonical path repository *replaces* Packagist for any package it
        // provides. With the sibling checkout present, that made every
        // released version unresolvable, including during `composer update
        // --lock`, which then failed outright.
        $this->assertFalse(
            $repo['canonical'] ?? true,
            'the path repository must be non-canonical so Packagist versions stay resolvable'
        );
    }

    /**
     * A version constraint that only accepts dev-main would make the starter
     * kit uninstallable for anyone without the sibling checkout.
     */
    public function test_the_framework_constraint_still_allows_a_published_release(): void
    {
        $composer = json_decode(
            (string) file_get_contents(dirname(__DIR__, 2) . '/composer.json'),
            true
        );

        $constraint = $composer['require']['libxa/framework'] ?? '';

        $this->assertStringContainsString('dev-main', $constraint, 'local development path');
        $this->assertMatchesRegularExpression(
            '/\^\d/',
            $constraint,
            'there must also be a released-version constraint for a normal install'
        );

        // Without the @dev suffix this constraint needs a global
        // minimum-stability of "dev", which would let *every* dependency
        // resolve to an unreleased version, not just the framework.
        $this->assertStringContainsString(
            'dev-main@dev',
            $constraint,
            'the @dev suffix scopes dev stability to this package alone'
        );
    }

    /**
     * The committed lock must be installable by CI and by real users. A lock
     * that records the framework from a `path` dist points at ../libxaframe,
     * which exists on exactly one machine: every CI job and every
     * create-project then fails with:
     *   Source path "../libxaframe" is not found for package libxa/framework
     */
    public function test_the_committed_lock_does_not_reference_a_local_path(): void
    {
        $lock = json_decode(
            (string) file_get_contents(dirname(__DIR__, 2) . '/composer.lock'),
            true
        );

        $offenders = [];

        foreach (array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []) as $package) {
            if (($package['dist']['type'] ?? '') === 'path') {
                $offenders[] = $package['name'] . ' -> ' . ($package['dist']['url'] ?? '?');
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "The lock references local paths, so it only installs on the machine that generated it:\n  "
            . implode("\n  ", $offenders)
            . "\n\nRegenerate it with the sibling checkout hidden - see docs/RELEASING.md."
        );
    }
}
