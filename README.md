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

## Running the tests

```bash
php vendor/bin/phpunit
```

The suite boots the real application, migrates a throwaway SQLite database per
test and drives requests through the actual HTTP kernel — so a green run means
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

- editing framework source takes effect on the very next request — no
  `composer update`, no `dump-autoload`, not even for brand-new classes
  (the framework is PSR-4, so the autoloader finds them on the fly);
- the two can never drift apart. They previously did, in both directions:
  the vendored copy had console commands the source lacked while the source
  had bug fixes and entire directories the copy lacked, so framework fixes
  silently had no effect on the app.

`tests/Feature/FrameworkLinkTest.php` fails the build if that link is ever
replaced by a copy. It skips itself when there is no sibling checkout, which
is the correct state for CI and production.

Two details in `composer.json` are deliberate — please keep them:

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

- [LibxaFrame Documentation](https://libxa.dev/docs)
- [LibxaFrame GitHub](https://github.com/libxa/framework)
- [LibxaFrame Discord](https://discord.gg/libxa)

## Support

For issues and questions:
- Check the [documentation](https://libxa.dev/docs)
- Search [GitHub Issues](https://github.com/libxa/framework/issues)
- Join our [Discord community](https://discord.gg/libxa)

## License

The LibxaFrame framework is open-sourced software licensed under the MIT license.

---

**Happy coding with LibxaFrame! 🚀**

## Project governance

| Document | What it covers |
|---|---|
| [CONTRIBUTING.md](CONTRIBUTING.md) | Local setup, branch rules, commit convention, test policy |
| [docs/BRANCHING.md](docs/BRANCHING.md) | The branch model — `main` is what Packagist publishes |
| [docs/RELEASING.md](docs/RELEASING.md) | Release and hotfix runbook |
| [docs/REPOSITORY_SETUP.md](docs/REPOSITORY_SETUP.md) | One-time GitHub settings: branch protection, tag rules, Packagist webhook |
| [SECURITY.md](SECURITY.md) | Private vulnerability disclosure and supported versions |
| [CHANGELOG.md](CHANGELOG.md) | What changed in each release |

Contributions branch from `develop`, never from `main`.
