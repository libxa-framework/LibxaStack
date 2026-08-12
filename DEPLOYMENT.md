# Deploying a LibxaFrame application

## The one that catches everyone

> The home page loads, every other route returns 404, and it all works locally.

Locally you run:

```bash
php -S 127.0.0.1:8000 -t src/public src/public/router.php
```

That last argument is the whole story. `router.php` exists to emulate Apache's
mod_rewrite for PHP's built-in server: if the requested path is not a real
file, it hands the request to `index.php`.

A real web server does not do that on its own. Ask Apache for `/about` and it
looks for a file called `about`, does not find one, and returns its own 404
before PHP is ever started. `/` works only because `DirectoryIndex` finds
`index.php`.

So the fix is always the same: **tell the web server to send anything that is
not a real file to `src/public/index.php`.** The rest of this page is how to do
that on each server.

## Apache

The starter kit ships two `.htaccess` files and, if your host reads them, it
already works.

**`src/public/.htaccess`** does the actual routing. **`.htaccess`** in the
project root is a fallback for shared hosting, where the document root cannot
be changed and points at the project root instead of `src/public/`.

Two things stop those files working:

- **`AllowOverride None`.** Apache ignores `.htaccess` entirely. Either set
  `AllowOverride All` for the directory, or paste the rules straight into the
  virtual host.
- **mod_rewrite not enabled.** `a2enmod rewrite && systemctl restart apache2`.

A virtual host that does not depend on `.htaccess` at all:

```apache
<VirtualHost *:80>
    ServerName example.com
    DocumentRoot /var/www/my-app/src/public

    <Directory /var/www/my-app/src/public>
        AllowOverride All
        Require all granted

        Options -MultiViews -Indexes

        RewriteEngine On
        RewriteCond %{HTTP:Authorization} .
        RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </Directory>

    ErrorLog  ${APACHE_LOG_DIR}/my-app-error.log
    CustomLog ${APACHE_LOG_DIR}/my-app-access.log combined
</VirtualHost>
```

## Nginx

Nginx has no `.htaccess`, so this has to live in the server block. The `try_files`
line is the equivalent of everything above.

```nginx
server {
    listen 80;
    server_name example.com;

    root /var/www/my-app/src/public;
    index index.php;

    charset utf-8;

    # Anything that is not a real file goes to the front controller.
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;

        # Imports and long reports outlive the 60s default.
        fastcgi_read_timeout 300;
    }

    # .env holds the app key and the database password.
    location ~ /\. { deny all; }

    client_max_body_size 32M;
}
```

The single most common nginx mistake is `try_files $uri $uri/ =404;`, copied
from a static-site config. That returns 404 for every route for exactly the
same reason Apache does without mod_rewrite.

## Caddy

```
example.com {
    root * /var/www/my-app/src/public
    encode gzip
    php_fastcgi unix//run/php/php8.3-fpm.sock
    file_server
}
```

`php_fastcgi` already implies the front-controller behaviour, so there is
nothing else to configure.

## Shared hosting

Most shared hosts serve `public_html` and will not let you move the document
root. Two ways to handle it:

**Upload the project into `public_html` directly.** The root `.htaccess` that
ships with the kit rewrites everything into `src/public/` and refuses direct
access to `vendor/`, `src/app/`, `.env` and the rest. Check that it worked by
visiting `/.env`: you should get a 403 or a 404, never a download.

**Or upload above the web root and symlink**, if the host allows it:

```bash
ln -s /home/you/my-app/src/public /home/you/public_html
```

Cleaner, because nothing but the public directory is ever reachable.

## Do not install into a subdirectory

`example.com/my-app/` will not work. The framework matches routes against the
full request path, so a request for `/my-app/about` is looked up as
`/my-app/about` and no route matches.

Use a subdomain, or point a virtual host at the project. This is a real
limitation rather than a configuration mistake, and it is better to know it
before deploying than after.

## Before you go live

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

`--no-dev` leaves PHPUnit and the rest of the development tooling off the
server. `--optimize-autoloader` turns the autoloader into a plain class map,
which removes a filesystem stat from every class load.

In `.env`:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_KEY=<a real generated key>
```

`APP_DEBUG=true` in production prints stack traces, file paths and often
environment values straight to the browser on any error.

`APP_ENV` also decides where `@vite()` points: `local` sends asset URLs to the
dev server on port 5173, so a production deployment left on `local` renders
with no stylesheet at all.

Permissions, if the web server runs as a different user:

```bash
chmod -R ug+w src/storage src/bootstrap/cache
```

## Checking it worked

```bash
curl -I https://example.com/            # 200
curl -I https://example.com/login       # 200, not 404
curl -I https://example.com/.env        # 403 or 404, never 200
curl -I https://example.com/vendor/autoload.php   # 403 or 404
```

If the second one 404s, the rewrite is not in effect: `.htaccess` is being
ignored, mod_rewrite is off, or the nginx block still says `=404`.
