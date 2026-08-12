# LibxaFrame Starter Application

Welcome to your new LibxaFrame application! This starter provides a clean, modern foundation for building web applications with PHP 8.3+.

## About LibxaFrame

LibxaFrame is a modern, elegant, and lightning-fast PHP framework for the next generation of web applications. Built around developer happiness, performance, and scalability.

## Requirements

- PHP >= 8.3
- Composer
- Node.js & NPM (for frontend assets)
- SQLite, MySQL, PostgreSQL, or SQL Server

## Installation

### 1. Install Dependencies

```bash
composer install
```

### 2. Environment Setup

Copy the example environment file and configure it:

```bash
cp .env.example .env
```

Generate an application key:

```bash
php libxa key:generate
```

### 3. Database Setup

Configure your database in `.env`:

```env
DB_CONNECTION=sqlite
# Or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

Run migrations:

```bash
php libxa migrate
```

### 4. Frontend Assets

Install and compile frontend assets:

```bash
npm install
npm run dev
```

For production:

```bash
npm run build
```

## Quick Start

Start the development server:

```bash
php libxa serve
```

Open your browser and visit `http://localhost:8000`

## Deploying

Point the web server's document root at **`src/public/`** and send anything
that is not a real file to `index.php`. The kit ships the Apache `.htaccess`
files that do this, and [DEPLOYMENT.md](DEPLOYMENT.md) has working
configuration for Apache, nginx, Caddy and shared hosting.

> **The home page works but every other route 404s?** That is this exact
> setting. Locally, `php libxa serve` uses `src/public/router.php` to emulate
> mod_rewrite; a real server needs the rule written out. See
> [DEPLOYMENT.md](DEPLOYMENT.md#the-one-that-catches-everyone).

## Running the tests

```bash
php vendor/bin/phpunit
```

The suite boots the real application, migrates a throwaway SQLite database per
test and drives requests through the actual HTTP kernel, so a green run means
register → login → protected page → logout genuinely works.

## Developing against a local framework checkout

A normal install resolves `libxa/framework` from Packagist and you can ignore
this section. If you keep the framework source as a sibling directory:

```
VYLOXI/libxa/
├── libxaframe/     # the framework source
└── LibxaStack/     # this application
```

then `composer install` **junctions** `vendor/libxa/framework` straight onto
`../libxaframe` (`repositories[0].options.symlink: true`). The vendor directory
*is* the framework working copy, so:

- editing framework source takes effect on the very next request: no
  `composer update`, no `dump-autoload`, not even for brand-new classes
  (the framework is PSR-4, so the autoloader finds them on the fly);
- the two can never drift apart. They previously did, in both directions:
  the vendored copy had console commands the source lacked while the source
  had bug fixes and entire directories the copy lacked, so framework fixes
  silently had no effect on the app.

`tests/Feature/FrameworkLinkTest.php` fails the build if that link is ever
replaced by a copy. It skips itself when there is no sibling checkout, which
is the correct state for CI and production.

Two details in `composer.json` are deliberate: please keep them:

| Setting | Why |
|---|---|
| `repositories[0].url` is the glob `../libxaframe*` | A literal path that does not exist makes Composer **abort**, which would break `composer create-project` for everyone without the sibling directory. A glob that matches nothing is simply ignored, so resolution falls through to Packagist. |
| `require` is `dev-main \|\| ^0.8.0` | `dev-main` picks up the local checkout; `^0.8.0` keeps the project installable from Packagist without it. |

## Project Structure

```
your-app/
├── src/
│   ├── app/              # Application code
│   │   ├── Http/         # Controllers, Middleware, Requests
│   │   ├── Models/       # Eloquent models
│   │   ├── Services/     # Business logic
│   │   └── Providers/    # Service providers
│   ├── config/           # Configuration files
│   ├── database/         # Database files
│   │   ├── migrations/   # Migration files
│   │   └── seeds/        # Seed files
│   ├── public/           # Public assets
│   ├── resources/        # Frontend assets (JS, CSS, Views)
│   │   ├── views/        # Blade templates
│   │   ├── js/          # JavaScript files
│   │   └── css/         # CSS files
│   ├── routes/           # Route definitions
│   │   ├── web.php      # Web routes
│   │   ├── api.php      # API routes
│   │   └── console.php  # Console routes
│   └── storage/          # Application storage
│       ├── app/         # Application generated files
│       ├── framework/   # Framework cache
│       └── logs/        # Log files
├── packages/            # Local packages
├── tests/               # Test files
├── composer.json        # PHP dependencies
├── package.json         # Node dependencies
└── libxa                # Framework CLI tool
```

## Available Commands

### Application

```bash
php libxa serve              # Start development server
php libxa key:generate       # Generate application key
php libxa env                # Display current environment
```

### Database

```bash
php libxa migrate             # Run database migrations
php libxa migrate:rollback    # Rollback last migration
php libxa migrate:refresh     # Rollback and re-run migrations
php libxa migrate:status      # Show migration status
php libxa db:seed             # Run database seeders
php libxa make:migration      # Create a new migration
php libxa make:model          # Create a new model
php libxa make:seeder         # Create a new seeder
```

### Code Generation

```bash
php libxa make:controller     # Create a new controller
php libxa make:model          # Create a new model
php libxa make:migration      # Create a new migration
php libxa make:seeder         # Create a new seeder
php libxa make:request        # Create a form request
php libxa make:middleware     # Create a new middleware
php libxa make:command        # Create a new console command
php libxa make:provider       # Create a new service provider
php libxa make:event          # Create a new event
php libxa make:listener       # Create a new event listener
```

### Package Management

```bash
php libxa make:package        # Create a new package
php libxa package:discover    # Discover and register packages
php libxa vendor:publish      # Publish package assets
```

### Queue

```bash
php libxa queue:work          # Process queue jobs
php libxa queue:listen        # Listen for queue jobs
php libxa queue:restart       # Restart queue workers
```

### Cache

```bash
php libxa cache:clear         # Clear application cache
php libxa config:clear        # Clear configuration cache
php libxa route:clear         # Clear route cache
php libxa view:clear          # Clear view cache
```

### Testing

```bash
php libxa test                # Run all tests
php libxa test --filter       # Run specific test
```

## Routing

Routes are defined in `src/routes/web.php` for web routes and `src/routes/api.php` for API routes.

### Basic Route

```php
$router->get('/', function () {
    return view('welcome');
});
```

### Controller Route

```php
$router->get('/users', [UserController::class, 'index']);
```

### Route with Parameters

```php
$router->get('/users/{id}', function ($id) {
    return "User {$id}";
});
```

### Route Groups

```php
$router->group(['prefix' => 'admin', 'middleware' => 'auth'], function ($router) {
    $router->get('/dashboard', [AdminController::class, 'dashboard']);
});
```

## Controllers

Controllers are stored in `src/app/Http/Controllers/`.

```php
<?php

namespace App\Http\Controllers;

use Libxa\Http\Request;
use Libxa\Http\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return view('users.index');
    }
}
```

## Models

Models are stored in `src/app/Models/` and extend the base Model class.

```php
<?php

namespace App\Models;

use Libxa\Atlas\Model;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
}
```

## Views

Views are stored in `src/resources/views/` and use the Blade templating engine.

```php
// resources/views/welcome.blade.php
<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, {{ $name }}!</h1>
</body>
</html>
```

## Configuration

Configuration files are located in `src/config/`. You can access configuration values using the `config()` helper:

```php
$value = config('app.name');
```

## Environment Variables

Environment variables are loaded from `.env` file. Access them using the `env()` helper:

```php
$debug = env('APP_DEBUG', false);
```

## Database

### Query Builder

```php
use Libxa\Atlas\DB;

$users = DB::table('users')->where('active', true)->get();
$user = DB::table('users')->where('id', 1)->first();
DB::table('users')->insert(['name' => 'John', 'email' => 'john@example.com']);
DB::table('users')->where('id', 1)->update(['name' => 'Jane']);
DB::table('users')->where('id', 1)->delete();
```

### Eloquent ORM

```php
// Get all users
$users = User::all();

// Find by ID
$user = User::find(1);

// Create
User::create(['name' => 'John', 'email' => 'john@example.com']);

// Update
$user = User::find(1);
$user->name = 'Jane';
$user->save();

// Delete
$user->delete();
```

## Middleware

Middleware is stored in `src/app/Http/Middleware/`.

```php
<?php

namespace App\Http\Middleware;

use Libxa\Http\Request;
use Libxa\Http\Response;

class CheckAge
{
    public function handle(Request $request, callable $next): Response
    {
        if ($request->age < 18) {
            return redirect('home');
        }
        return $next($request);
    }
}
```

## Security

### Authentication

```php
// Login
Auth::attempt(['email' => $email, 'password' => $password]);

// Get authenticated user
$user = Auth::user();

// Logout
Auth::logout();
```

### Hashing

```php
// Hash a password
$hashed = Hash::make('password');

// Verify a password
if (Hash::check('password', $hashed)) {
    // Password matches
}
```

## File Storage

```php
use Libxa\Support\Facades\Storage;

// Store a file
Storage::put('file.jpg', $contents);

// Get a file
$contents = Storage::get('file.jpg');

// Check if file exists
$exists = Storage::exists('file.jpg');

// Delete a file
Storage::delete('file.jpg');

// Get file URL
$url = Storage::url('file.jpg');
```

## Cache

```php
use Libxa\Support\Facades\Cache;

// Store value
Cache::put('key', 'value', 3600);

// Get value
$value = Cache::get('key');

// Remember pattern
$value = Cache::remember('key', 3600, function () {
    return DB::table('users')->get();
});
```

## Queue

```php
// Create a job
class SendEmail implements ShouldQueue
{
    public function handle()
    {
        // Process job
    }
}

// Dispatch job
SendEmail::dispatch();

// Process queue
php libxa queue:work
```

## Testing

Tests are stored in `tests/` directory.

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_basic_test(): void
    {
        $this->assertTrue(true);
    }
}
```

Run tests:

```bash
php libxa test
```

## Frontend Assets

This starter uses Vite for frontend asset compilation.

### JavaScript

Add your JavaScript in `src/resources/js/app.js`:

```javascript
import './bootstrap';

console.log('LibxaFrame is ready!');
```

### CSS

Add your styles in `src/resources/css/app.css`:

```css
body {
    font-family: sans-serif;
}
```

### Building Assets

Development:
```bash
npm run dev
```

Production:
```bash
npm run build
```

## Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate application key: `php libxa key:generate`
- [ ] Run migrations: `php libxa migrate`
- [ ] Optimize Composer: `composer install --no-dev --optimize-autoloader`
- [ ] Build frontend assets: `npm run build`
- [ ] Set proper file permissions for `src/storage` and `src/public`
- [ ] Configure web server (Nginx/Apache)

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/your-app/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Additional Resources

- [Framework repository](https://github.com/libxa-framework/libxa) - routing, ORM, Blade, container
- [Framework README](https://github.com/libxa-framework/libxa#readme) - full API reference
- [Contributing to this skeleton](CONTRIBUTING.md)

## Support

| I want to... | Go to |
|---|---|
| Report a bug in the **skeleton** | [LibxaStack issues](https://github.com/libxa-framework/LibxaStack/issues) |
| Report a bug in the **framework** | [libxa issues](https://github.com/libxa-framework/libxa/issues) |
| Ask "how do I ...?" | [Discussions](https://github.com/libxa-framework/libxa/discussions) |
| Report a **vulnerability** | [SECURITY.md](SECURITY.md) - never a public issue |

## License

The LibxaFrame framework is open-sourced software licensed under the MIT license.

---

**Happy coding with LibxaFrame! 🚀**

---

# How this project works

This section is for people **maintaining the starter kit**. If you have already
run `composer create-project`, the code is yours and none of this applies: the
sections above are your documentation.

Everything here is the operating model: how this repository relates to the
framework, how a change reaches Packagist, and what CI enforces on the way.
[BRANCHING](docs/BRANCHING.md), [RELEASING](docs/RELEASING.md) and
[REPOSITORY_SETUP](docs/REPOSITORY_SETUP.md) go deeper; this is the whole
picture in one place.

## The two repositories

| Repository | Packagist | Type | What it is |
|---|---|---|---|
| [libxa-framework/libxa](https://github.com/libxa-framework/libxa) | `libxa/framework` | library | The framework. Installed into `vendor/`. |
| [libxa-framework/LibxaStack](https://github.com/libxa-framework/LibxaStack) | `libxa/libxa` | project | **This repo.** The skeleton `composer create-project` produces. |

```
        ┌──────────────────────────┐
        │  libxa/framework         │   routing, ORM, Blade, container
        └───────────┬──────────────┘
                    │ composer require
                    ▼
        ┌──────────────────────────┐
        │  libxa/libxa             │   this repository
        │  config, controllers,    │
        │  migrations, views       │
        └───────────┬──────────────┘
                    │ composer create-project
                    ▼
              a user's new app
```

**Where does a bug belong?** If it is in routing, the ORM, Blade, the
container, sessions or encryption → the framework repository. If it is a config
default, an example controller, a migration or the directory layout → here.

## Branch model

**`main` is what Packagist publishes. Nothing reaches `main` except through a
reviewed `release/*` or `hotfix/*` pull request.**

```
  feature/x ─┐
  fix/y ─────┼──▶ develop ──▶ release/v0.2.0 ──▶ main ──(tag v0.2.0)──▶ Packagist
  docs/z ────┘        ▲                            │
                      │                            │
                      └────── back-merge ──────────┤
                                                   │
                hotfix/v0.1.2 ────────────────────▶─┘
```

| Branch | From | Into | Protected |
|---|---|---|---|
| `main` |: |: | ✅ |
| `develop` | `main` |: | ✅ |
| `feature/*` `fix/*` `docs/*` `test/*` `refactor/*` `chore/*` | `develop` | `develop` |: |
| `release/vX.Y.Z` | `develop` | `main` **and** `develop` |: |
| `hotfix/vX.Y.Z` | `main` | `main` **and** `develop` |: |

`develop` is the GitHub default branch, so new pull requests target it
automatically.

## Day-to-day development

```bash
git checkout develop
git pull origin develop
git checkout -b fix/session-cookie-defaults

# work, then run everything CI will run
composer check          # lint + full suite + security audit

git fetch origin && git rebase origin/develop
git push -u origin fix/session-cookie-defaults
gh pr create --base develop
```

Commits follow [Conventional Commits](https://www.conventionalcommits.org):

```
fix(config): enable SameSite on the session cookie by default
docs(readme): document the local framework checkout workflow
chore(deps): bump phpunit to 10.5
```

## Working against a local framework checkout

Most skeleton work needs a framework change alongside it. Clone both as
**siblings**:

```
your-workspace/
├── libxaframe/     # git clone .../libxa.git libxaframe
└── LibxaStack/     # this repository
```

`composer install` then **junctions** `vendor/libxa/framework` onto
`../libxaframe`. The vendor directory *is* the framework working copy, so
framework edits take effect on the next request: no `composer update`, no
`dump-autoload`, not even for brand-new classes.

Two settings in `composer.json` make that safe. Neither is redundant, and
`tests/Feature/FrameworkLinkTest.php` fails the build if either is undone:

| Setting | Why |
|---|---|
| repository url is the glob `../libxaframe*` | A literal path that does not exist makes Composer **abort**, breaking `composer create-project` for everyone without the sibling checkout. A glob matching nothing is ignored, so resolution falls through to Packagist. |
| constraint is `^0.8.0 \|\| dev-main@dev` | The `@dev` suffix scopes the dev-stability allowance to this one package. A global `minimum-stability: dev` would let *every* dependency resolve to an unreleased version. |

Without a sibling checkout (CI, or a normal install) the glob matches nothing
and `libxa/framework` comes from Packagist, exactly as a real user gets it.

## What CI enforces

| Job | Checks |
|---|---|
| `Tests · PHP 8.3` / `8.4` | Install, migrate, lint, full suite |
| `Skeleton hygiene` | No BOMs; no compiled views, logs, caches, `.sqlite`, `.env` or archives committed; `composer.lock` present |
| `Security audit` | No known vulnerabilities in dependencies |
| `composer create-project` | Installs this checkout from scratch and runs the **generated** project's own suite |

The last job is the real gate. It is the only check that exercises what a
stranger actually receives.

The hygiene job exists because each of its checks caught something real:

- `.gitignore` pointed at `/storage` and `/public`, but this skeleton keeps
  both under `src/`. Nothing was ignored, so a compiled Blade view containing a
  **fatal PHP parse error** was committed and kept being served.
- Four `src/bootstrap/` files began with a UTF-8 BOM, emitting output before
  `header()` on every request.
- A tracked `tests.zip` held a stale copy of the whole project, `.env`
  included, and shipped to every new project.

## Releasing

Tags are created **by a human**; CI verifies rather than creates them. Full
runbook: [docs/RELEASING.md](docs/RELEASING.md).

### After a framework release: order matters

1. **Wait** until the framework version is live on Packagist. Starting earlier
   resolves the older release.

2. Widen the constraint, keeping both halves:

   ```json
   "libxa/framework": "^0.9.0 || dev-main@dev"
   ```

3. Regenerate the lock file **without** the sibling checkout in play, so it
   records the published release rather than `dev-main`:

   ```bash
   mv ../libxaframe ../libxaframe.tmp
   composer update libxa/framework
   mv ../libxaframe.tmp ../libxaframe

   composer show libxa/framework | head -3   # must NOT say dev-main
   ```

   Skipping this ships a lock pinned to `dev-main`, which resolves to whatever
   `main` happens to be on the day each user installs. That is not a
   reproducible install.

### Then the standard flow

```bash
git checkout develop && git pull
git checkout -b release/v0.2.0
# move [Unreleased] in CHANGELOG.md into a dated ## [0.2.0] section
composer check
gh pr create --base main            # review, merge

git checkout main && git pull
git tag -a v0.2.0 -m "Release v0.2.0"
git push origin v0.2.0

git checkout develop                # back-merge, or the next release reverts it
git merge --no-ff origin/main
git push origin develop
```

`release.yml` refuses a tag that is not annotated, not SemVer, not an ancestor
of `main`, or not documented in `CHANGELOG.md`, and re-runs the suite at the
tagged commit before publishing. Packagist publishes via its push webhook.

Finally, smoke-test the published result for real:

```bash
composer create-project libxa/libxa /tmp/smoke
cd /tmp/smoke && php libxa migrate && php vendor/bin/phpunit
```

### Version numbers

[SemVer](https://semver.org), prefixed `v`. Below 1.0, Composer treats the
**minor** number as the compatibility boundary: `^0.1.0` allows `0.1.9` but
not `0.2.0`, so anything that breaks a documented default needs a minor bump.

The skeleton and the framework version independently. A framework release does
not require one here unless the skeleton itself changes.

## Hotfixes

The only branch that starts from `main`:

```bash
git checkout main && git pull
git checkout -b hotfix/v0.1.2
# fix it, add a regression test, update CHANGELOG.md
gh pr create --base main
# merge → tag on main → back-merge into develop
```

## Reference

| Document | Covers |
|---|---|
| [CONTRIBUTING.md](CONTRIBUTING.md) | Setup, what belongs in a skeleton, test policy |
| [docs/BRANCHING.md](docs/BRANCHING.md) | The branch model in full, plus branch-protection settings |
| [docs/RELEASING.md](docs/RELEASING.md) | Release runbook and the framework-then-skeleton order |
| [docs/REPOSITORY_SETUP.md](docs/REPOSITORY_SETUP.md) | One-time GitHub setup: protection rules, tag rules, Packagist webhook |
| [SECURITY.md](SECURITY.md) | Disclosure process and the production deployment checklist |
| [CHANGELOG.md](CHANGELOG.md) | What changed in each release |
