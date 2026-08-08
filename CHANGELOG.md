# Changelog

All notable changes to the LibxaStack starter kit are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

> This is the **application skeleton**. Framework changes are listed in the
> [framework's changelog](https://github.com/libxa-framework/libxa/blob/main/CHANGELOG.md).

## [Unreleased]

## [0.3.0] - 2026-08-08

> **Requires `libxa/framework` ^0.10.0.**

### Added

- **PHP 8.5 support.** `^8.3` already permitted it, but nothing tested it. CI
  now runs the suite on **8.3, 8.4 and 8.5**, and the `create-project` job
  installs on 8.5 — a fresh install should work on what a new user most likely
  has.
- A **frontend CI job** that installs, audits, builds, and asserts the Vite
  manifest lands where the framework reads it. There was no npm job at all
  before, which is why none of the breakage below was noticed.

### Fixed

The frontend toolchain could not install or build at all:

- **`npm install` failed outright.** `@vitejs/plugin-react@4` caps at vite 7,
  but `package.json` pinned `vite: ^8.0.7`. Bumped `plugin-react` to `^6.0.5`
  and `plugin-vue` to `^6.0.8`.
- **`npm run build` could never succeed.** `vite.config.js` pointed at
  `src/resources/js/app.js` and `src/resources/css/app.css`, neither of which
  existed — only their compiled output was committed, so a new project could
  never rebuild the assets it was given. Both entry files are restored.
- **`@vite()` emitted nothing.** Vite 5+ writes the manifest to
  `<outDir>/.vite/manifest.json` while the framework reads
  `<outDir>/manifest.json`.
- The orphaned pre-built assets at `src/public` root are removed — build output
  with no sources. Builds now go to `src/public/build`, which is gitignored.

### Security

- **Six moderate Svelte XSS advisories**, fixed only in Svelte 5. The skeleton
  contains no `.svelte` files, so there was nothing to migrate: `svelte` to
  `^5.56.8` and `@sveltejs/vite-plugin-svelte` to `^7.2.0`. `npm audit` now
  reports zero vulnerabilities.

### Changed

- `phpunit/phpunit` widened to `^11.5 || ^12.0 || ^13.0`; resolves to 12.5.
- **`pestphp/pest` removed.** Nothing used it — the suite runs on PHPUnit — and
  Pest 2 predates PHP 8.5, so it would have blocked the upgrade. Drops 20
  transitive packages.
- `package-lock.json` is now committed, consistent with `composer.lock`.

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

[Unreleased]: https://github.com/libxa-framework/LibxaStack/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/libxa-framework/LibxaStack/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/libxa-framework/LibxaStack/compare/v0.1.1...v0.2.0
[0.1.1]: https://github.com/libxa-framework/LibxaStack/releases/tag/v0.1.1
