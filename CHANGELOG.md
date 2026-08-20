# Changelog

All notable changes to the LibxaStack starter kit are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

> This is the **application skeleton**. Framework changes are listed in the
> [framework's changelog](https://github.com/libxa-framework/libxa/blob/main/CHANGELOG.md).

## [Unreleased]

## [0.6.0] - 2026-08-20

Migrations now run on all three databases the starter kit offers.

### Changed

- **Requires framework [0.13.0](https://github.com/libxa-framework/libxa/releases/tag/v0.13.0).**

  0.5.0 added a `pgsql` connection and settled on `DB_CONNECTION`, so you
  could *select* Postgres or MySQL. This is the release where the migrations
  then succeed against them.

  The schema builder emitted SQLite's SQL whatever it was connected to, so
  `php libxa migrate` worked on SQLite and failed on the first table
  everywhere else — with a syntax error reported against line 2, pointing at
  a column that was perfectly fine. 0.13.0 gives each driver its own grammar.

  Verified with this skeleton's own four migrations, against the published
  framework rather than a local checkout:

  | | |
  |---|---|
  | SQLite | 4 migrations |
  | MariaDB 11.4 | 4 migrations |
  | PostgreSQL 18.4 | 4 migrations |


## [0.5.0] - 2026-08-19

Postgres, and one name for the setting that picks a database.

Requires **framework 0.12.0**, which is the release that made
`config/database.php` actually get read. On anything earlier the file is
ignored, so the connections below would be configured and never used.

### Added

- **A `pgsql` connection.** The framework has had Postgres support for a
  while, but there was no `pgsql` block in `config/database.php` — so there
  was no way to select it through config at all. Comes with `schema` and
  `sslmode`, and port **5432** rather than inheriting MySQL's 3306.

- **`DB_SQLITE_PATH`** says where the SQLite file goes, explicitly.

### Changed

- **`DB_CONNECTION` is the name for choosing a connection.** `.env.example`
  shipped `DB_DRIVER` while `config/database.php` read `DB_CONNECTION`, so
  which one took effect depended on which code path resolved the connection
  first. `DB_DRIVER` is still read, so an existing project keeps its database
  rather than quietly moving to SQLite on upgrade.

- **`DB_DATABASE` is used for SQLite only when it looks like a path.** It is a
  database *name* for MySQL and Postgres and a file *path* for SQLite. A
  project that set `DB_DATABASE=myapp` for MySQL and later switched to SQLite
  got an extension-less file literally called `myapp`, created without
  complaint, and no clue where its data had gone. Existing values such as
  `database/database.sqlite` keep working.

- **`libxa/framework` now requires `^0.12.0`.** Below that, none of the above
  has any effect.


## [0.4.0] - 2026-08-12

> **Requires `libxa/framework` ^0.10.0.**

### Fixed

- **Every route except the home page returned 404 once deployed.** The kit
  shipped no `.htaccess` at all. Locally that goes unnoticed because
  `php libxa serve` passes `src/public/router.php` to PHP's built-in server,
  and that shim emulates mod_rewrite by handing any path that is not a real
  file to `index.php`. A real web server does no such thing: asked for
  `/login`, Apache looks for a file named `login`, does not find one, and
  returns its own 404 before PHP is ever started. `/` worked only because
  `DirectoryIndex` finds `index.php`.

  This was not a subtle failure. Every deployment of the starter kit was a
  site where only the front page worked.

### Added

- **`src/public/.htaccess`**, the front controller rule that makes routing
  work on Apache. It also disables `MultiViews` (which would let Apache guess
  `/about` means `about.php` and bypass routing entirely), disables directory
  indexes, redirects `/about/` to `/about` so each page has one canonical URL,
  and restores the `Authorization` header that CGI and FastCGI strip, without
  which API token authentication fails silently.
- **A root `.htaccess`** for shared hosting, where the document root is the
  project directory and cannot be moved. It rewrites requests into
  `src/public/` and refuses `vendor/`, `src/app/`, `src/storage/`, `.env` and
  the other paths that must never be served. It is inert when the document
  root already points at `src/public/`, since Apache only reads `.htaccess`
  files at or below the document root.
- **[DEPLOYMENT.md](DEPLOYMENT.md)** with working configuration for
  Apache, nginx, Caddy and shared hosting, the two settings that stop
  `.htaccess` being read at all (`AllowOverride None`, mod_rewrite disabled),
  a production checklist, and `curl` commands to verify a deployment rather
  than assume it.

  It also documents a real limitation rather than leaving it to be
  discovered: **installing into a subdirectory does not work.** Routes are
  matched against the full request path, so `example.com/my-app/about` is
  looked up as `/my-app/about` and matches nothing. Use a subdomain or a
  virtual host.
- **`DeploymentConfigTest`**, which asserts the rewrite rules ship and still
  say what they need to say. The original bug was these files not existing;
  CI now fails rather than a deployment.

### Changed

- Page titles use `|` rather than a colon as the separator, so `Login |
  LibxaFrame` instead of `Login: LibxaFrame`.

## [0.3.0] - 2026-08-08

> **Requires `libxa/framework` ^0.10.0.**

### Added

- **PHP 8.5 support.** `^8.3` already permitted it, but nothing tested it. CI
  now runs the suite on **8.3, 8.4 and 8.5**, and the `create-project` job
  installs on 8.5: a fresh install should work on what a new user most likely
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
  existed: only their compiled output was committed, so a new project could
  never rebuild the assets it was given. Both entry files are restored.
- **`@vite()` emitted nothing.** Vite 5+ writes the manifest to
  `<outDir>/.vite/manifest.json` while the framework reads
  `<outDir>/manifest.json`.
- The orphaned pre-built assets at `src/public` root are removed: build output
  with no sources. Builds now go to `src/public/build`, which is gitignored.

### Security

- **Six moderate Svelte XSS advisories**, fixed only in Svelte 5. The skeleton
  contains no `.svelte` files, so there was nothing to migrate: `svelte` to
  `^5.56.8` and `@sveltejs/vite-plugin-svelte` to `^7.2.0`. `npm audit` now
  reports zero vulnerabilities.

### Changed

- `phpunit/phpunit` widened to `^11.5 || ^12.0 || ^13.0`; resolves to 12.5.
- **`pestphp/pest` removed.** Nothing used it: the suite runs on PHPUnit, and
  Pest 2 predates PHP 8.5, so it would have blocked the upgrade. Drops 20
  transitive packages.
- `package-lock.json` is now committed, consistent with `composer.lock`.

## [0.2.0] - 2026-08-08

> **Requires `libxa/framework` ^0.9.0.** The skeleton's tests depend on fixes
> that shipped in that release, so this is a minor bump rather than a patch:
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
  session files, cache data and logs were all committed, which is how a
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

[Unreleased]: https://github.com/libxa-framework/LibxaStack/compare/v0.6.0...HEAD
[0.6.0]: https://github.com/libxa-framework/LibxaStack/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/libxa-framework/LibxaStack/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/libxa-framework/LibxaStack/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/libxa-framework/LibxaStack/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/libxa-framework/LibxaStack/compare/v0.1.1...v0.2.0
[0.1.1]: https://github.com/libxa-framework/LibxaStack/releases/tag/v0.1.1
