# Contributing to LibxaStack

LibxaStack is the **starter kit** — the skeleton `composer create-project`
produces. Contributions here shape the first hour of every new LibxaFrame
project, so the bar is a little different from a normal application.

> Looking to change the **framework** (routing, ORM, Blade, container)? That
> lives in [libxa-framework/libxa](https://github.com/libxa-framework/libxa).
> This repository only holds the application skeleton: config files, example
> controllers, migrations, views and tooling.

- [Getting set up](#getting-set-up)
- [What belongs here](#what-belongs-here)
- [Where to branch from](#where-to-branch-from)
- [Commit messages](#commit-messages)
- [Tests](#tests)
- [Opening a pull request](#opening-a-pull-request)

---

## Getting set up

Requirements: **PHP 8.3+** with `mbstring`, `xml`, `ctype`, `iconv`, `intl`,
`pdo_sqlite` and `openssl`; plus Composer 2.

```bash
git clone https://github.com/libxa-framework/LibxaStack.git
cd LibxaStack
composer install
cp .env.example .env
php libxa key:generate
php libxa migrate
composer test
```

Then `php libxa serve` and open <http://localhost:8000>.

### Working against a local framework checkout

Most starter-kit work needs a framework change alongside it. Clone both as
**siblings**:

```
your-workspace/
├── libxaframe/     # git clone .../libxa.git libxaframe
└── LibxaStack/     # this repository
```

By default `composer install` gives you the **released** framework from
Packagist, because the committed lock pins a real version — that is what keeps
CI and `composer create-project` reproducible.

To work against your local checkout instead, opt in explicitly:

```bash
composer update libxa/framework:dev-main
```

That junctions `vendor/libxa/framework` onto `../libxaframe`, so the vendor
directory *is* your framework working copy: edits take effect on the next
request, with no `composer update` and no `dump-autoload`, not even for
brand-new classes.

> **Do not commit the lock file change that produces.** A lock recording a
> `path` dist only installs on your machine — every CI job and every
> `create-project` then fails with
> `Source path "../libxaframe" is not found`. `FrameworkLinkTest` fails the
> build if such a lock is ever committed. Run `composer update libxa/framework`
> with the checkout renamed away before committing, as
> [docs/RELEASING.md](docs/RELEASING.md) describes.

Three settings in `composer.json` make this work, and must not be
"simplified" — `tests/Feature/FrameworkLinkTest.php` fails the build if any is
undone:

| Setting | Why |
|---|---|
| url is the glob `../libxaframe*` | A literal path that does not exist makes Composer **abort**, breaking `composer create-project` for everyone without the sibling checkout. A glob matching nothing is simply ignored. |
| `"canonical": false` | A canonical path repository *replaces* Packagist for any package it provides. With the checkout present, every released version became unresolvable — even `composer update --lock` failed. |
| constraint is `^0.9.0 \|\| dev-main@dev` | The `@dev` suffix scopes the dev-stability allowance to this one package. A global `minimum-stability: dev` would let *every* dependency resolve to an unreleased version. |

---

## What belongs here

The skeleton is read by every new user on their first day. Favour clarity over
cleverness.

**Good contributions**

- Fixing a default that is wrong or unsafe
- Making a config file clearer or better commented
- Tests covering skeleton behaviour that could silently break
- Documentation, especially the README
- Keeping the example code idiomatic as the framework evolves

**Please open an issue first**

- Adding a dependency. Everything here is installed by every new project
  forever; the bar is high.
- Adding a new example feature. More scaffolding to delete is not a gift.
- Changing the directory layout. It is baked into framework path helpers,
  documentation and everyone's muscle memory.

---

## Where to branch from

**Always `develop`.** `main` is protected and is what Packagist publishes.

```bash
git checkout develop
git pull origin develop
git checkout -b fix/session-config-defaults
```

Prefixes: `feature/`, `fix/`, `docs/`, `test/`, `refactor/`, `chore/`, `perf/`.

The full model is in [docs/BRANCHING.md](docs/BRANCHING.md).

---

## Commit messages

[Conventional Commits](https://www.conventionalcommits.org):

```
fix(config): enable SameSite on the session cookie by default
docs(readme): document the local framework checkout workflow
chore(deps): bump phpunit to 10.5
```

---

## Tests

```bash
composer test
```

`tests/TestCase.php` boots the real application, migrates a throwaway SQLite
database per test, and drives requests through the actual HTTP kernel. A green
run means register → login → protected page → logout genuinely works.

```php
class MyTest extends Tests\TestCase
{
    public function test_something(): void
    {
        $response = $this->post('/register', ['name' => 'Ada', /* ... */]);

        $this->assertSame(302, $response->getStatus());
        $this->assertDatabaseHas('users', ['email' => 'ada@example.com']);
    }
}
```

Helpers available: `get()`, `post()` (adds a valid CSRF token), `call()` (does
not), `assertDatabaseHas()`, `assertDatabaseMissing()`, `pdo()`, `csrfToken()`.

**A skeleton bug needs a regression test.** These defaults ship to every new
project; a silent breakage here is expensive. Two of the tests already here
exist because the skeleton shipped broken:

- a migration was silently skipped for months because its class name did not
  match its filename;
- four bootstrap files carried a UTF-8 BOM, emitting output before `header()`
  on every single request.

Both now fail the build.

---

## Opening a pull request

1. Rebase onto current `develop`.
2. Open the PR **against `develop`**.
3. Fill in the template, especially how you verified the change.
4. If you touched anything a new user sees on day one — config comments,
   README, example controllers — say what it looks like now.

### After a framework release

When the framework ships a new minor version, the skeleton needs its constraint
widened. That is a release-manager task; see
[the framework's RELEASING.md](https://github.com/libxa-framework/libxa/blob/main/docs/RELEASING.md#releasing-the-two-repositories-together)
for the required order — widening before the framework tag is live pins the
wrong version in the lock file.

---

## Reporting a vulnerability

**Do not open a public issue.** See [SECURITY.md](SECURITY.md).

---

## Licence

By contributing you agree that your contributions are licensed under the
[MIT Licence](LICENSE).
