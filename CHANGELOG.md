# Changelog

All notable changes to the LibxaStack starter kit are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

> This is the **application skeleton**. Framework changes are listed in the
> [framework's changelog](https://github.com/libxa-framework/libxa/blob/main/CHANGELOG.md).

## [Unreleased]

## [0.2.0] - 2026-08-08

> **Requires `libxa/framework` ^0.9.0.** The skeleton's tests depend on fixes
> that shipped in that release, so this is a minor bump rather than a patch —
> below 1.0 Composer treats the minor number as the compatibility boundary.

### Added

- A real test suite. `tests/TestCase.php` boots the application, migrates a
  per-test SQLite database and drives requests through the actual HTTP kernel,
  with `assertDatabaseHas()`/`assertDatabaseMissing()` helpers.
- End-to-end coverage of register → login → protected page → logout, plus the
  public and authenticated API endpoints.
- `phpunit.xml`. The repository previously had **no PHPUnit configuration at
  all**, so the CI step `php vendor/bin/pest` could not discover a single test
  and had never actually run one.
- `tests/Feature/FrameworkLinkTest.php`, which fails the build if
  `vendor/libxa/framework` stops tracking the framework source.
- A BOM check, because four bootstrap files shipped with one.
- Contribution, security, branching and code-of-conduct documentation.

### Fixed

- **`.gitignore` pointed at `/storage` and `/public`**, but this skeleton keeps
  both under `src/`. Nothing was actually ignored, so compiled Blade views,
  session files, cache data and logs were all committed — which is how a
  compiled view containing a *fatal PHP parse error* ended up in version
  control and kept being served instead of being recompiled.
- **A UTF-8 BOM in four `src/bootstrap/` files**, emitting output before any
  `header()` call on every request.
- **A migration was silently skipped** because its class name did not match its
  filename, so `personal_access_tokens` never received its `refresh_token`
  column. The framework now fails loudly on a mismatch.
- The development SQLite database and the regenerable bootstrap caches are no
  longer tracked in git.

### Changed

- `minimum-stability` is back to `stable`. It was `dev`, which applies to
  **every** dependency, not just the framework; the dev allowance is now scoped
  to `libxa/framework` alone via a per-package `@dev` flag.
- `vendor/libxa/framework` is a symlink (junction on Windows) to a sibling
  framework checkout when one is present, instead of a mirrored copy. The two
  copies had drifted apart in both directions, so framework fixes silently had
  no effect on the application.
- CI runs the real suite on PHP 8.3 and 8.4, migrates, and rejects BOMs.

## [0.1.1] - 2026-XX-XX

Baseline for this changelog. Earlier releases are catalogued in the repository
history.

[Unreleased]: https://github.com/libxa-framework/LibxaStack/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/libxa-framework/LibxaStack/compare/v0.1.1...v0.2.0
[0.1.1]: https://github.com/libxa-framework/LibxaStack/releases/tag/v0.1.1
