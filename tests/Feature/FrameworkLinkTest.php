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
 * Packagist into a normal directory, which is correct — so they skip instead
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
            $this->markTestSkipped('No sibling framework checkout — installed from Packagist.');
        }
    }

    public function test_vendor_is_a_link_to_the_framework_source_not_a_copy(): void
    {
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
    }
}
